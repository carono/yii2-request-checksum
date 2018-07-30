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
    public $redefineActiveField = true;
    public $activeFormClasses = [
        \yii\widgets\ActiveForm::class,
        \yii\bootstrap\ActiveForm::class,
        \carono\checksum\ActiveForm::class
    ];
    protected $_checksumKey;

    public function getChecksumKey()
    {
        return $this->_checksumKey ?: hash("sha256", $this->cookieValidationKey);
    }

    public function setChecksumKey($value)
    {
        $this->_checksumKey = $value;
    }

    public function init()
    {
        parent::init();
        if ($this->redefineActiveField) {
            $behavior = ['as caronoChecksumBehavior' => ChecksumBehavior::class];
            foreach ($this->activeFormClasses as $class) {
                if (class_exists($class)) {
                    \Yii::$container->set($class, $behavior);
                }
            }
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
            $checksum = ArrayHelper::remove($post, $this->checksumParam);
            $stack = $this->getStackByChecksum($checksum);
            $postPartials = Checksum::formKeyPartials($post);
            $stackPartials = Checksum::formKeyPartials($stack);
            foreach (array_diff($stackPartials, $postPartials) as $lostPartial) {
                list($formName, $attribute) = explode('=', $lostPartial);
                $post[$formName][$attribute] = '';
            }
            if (!Checksum::validate($post, $checksum, $this->checksumKey)) {
                return false;
            }
            $this->clearStack();
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
        return ArrayHelper::getValue($this->getStack(), $checksum);
    }
}