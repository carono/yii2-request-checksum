<?php


namespace carono\checksum;

use yii\helpers\ArrayHelper;

/**
 * Class Request
 *
 * @property mixed checksumKey
 * @package carono\checksum
 */
class Request extends \yii\web\Request
{
    public $checksumParam = '_checksum';
    public $enableChecksumValidation = true;
    public $attachBehaviorViewBehaviour = true;
    protected $_checksumKey;

    public function getChecksumKey()
    {
        return $this->_checksumKey ?: hash('sha256', $this->cookieValidationKey);
    }

    public function setChecksumKey($value)
    {
        $this->_checksumKey = $value;
    }

    public function init()
    {
        parent::init();
        if ($this->attachBehaviorViewBehaviour) {
            \Yii::$app->view->attachBehavior('caronoChecksumBehavior', ChecksumBehavior::class);
        }
    }

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