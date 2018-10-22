<?php


namespace carono\checksum;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class Request
 *
 * @package carono\checksum
 */
class Request extends \yii\web\Request
{
    public $checksumParam = '_checksum';
    public $enableChecksumValidation = true;
    public $attachBehaviorViewBehaviour = true;
    public $checksumKey;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->attachBehaviorViewBehaviour) {
            \Yii::$app->view->attachBehavior('caronoChecksumBehavior', ChecksumBehavior::class);
        }
        if (!$this->checksumKey){
            throw new InvalidConfigException('The "checksumKey" property must be set.');
        }
    }

    /**
     * @return bool
     */
    public function checksumIsEnabled()
    {
        return $this->enableChecksumValidation;
    }

    /**
     * @param null $clientSuppliedToken
     * @return bool
     */
    public function validateCsrfToken($clientSuppliedToken = null)
    {
        if ($this->isPost && $this->checksumIsEnabled()) {
            $post = $this->post();
            $checksum = ArrayHelper::getValue($post, $this->checksumParam);
            $stack = $this->getStackByChecksum($checksum);
            if (!Checksum::compareStacks($post, $stack, $this->checksumKey)) {
                return false;
            }
        }
        return parent::validateCsrfToken($clientSuppliedToken);
    }

    /**
     * @return string
     */
    protected function getStackKey()
    {
        return self::className() . $this->checksumParam;
    }

    /**
     * @param $widgetId
     * @param $stack
     * @return string
     */
    public function setStack($stack)
    {
        $checksum = Checksum::calculate($stack, $this->checksumKey);
        $key = $this->getStackKey();
        $data = $this->getStack();
        $data[$checksum] = $stack;
        \Yii::$app->session->set($key, $data);
        return $checksum;
    }

    /**
     * @return mixed
     */
    public function clearStack()
    {
        return \Yii::$app->session->set($this->getStackKey(), []);
    }

    /**
     * @return mixed
     */
    public function getStack()
    {
        return \Yii::$app->session->get($this->getStackKey(), []);
    }

    /**
     * @param $checksum
     * @return array
     */
    public function getStackByChecksum($checksum)
    {
        return ArrayHelper::getValue($this->getStack(), $checksum, []);
    }
}