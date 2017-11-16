<?php


namespace carono\checksum;

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

    public function init()
    {
        parent::init();
        \Yii::$container->set('yii\widgets\ActiveField', '\carono\checksum\ActiveField');
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
            ArrayHelper::remove($post, $this->csrfParam);
            $postPartials = Checksum::formKeyPartials($post);
            $stackPartials = Checksum::formKeyPartials($stack);
            foreach (array_diff($stackPartials, $postPartials) as $lostPartial) {
                list($formName, $attribute) = explode('=', $lostPartial);
                $post[$formName][$attribute] = '';
            }
            if (!Checksum::validate($post, $checksum)) {
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
     * @param $modelName
     * @param $fieldName
     */
    public function stackField($widgetId, $modelName, $fieldName)
    {
        $key = $this->getStackKey();
        $data = $this->getStack();
        $data[$widgetId][$modelName][] = $fieldName;
        return \Yii::$app->session->set($key, $data);
    }

    /**
     * @param null $widgetId
     */
    public function clearStack($widgetId = null)
    {
        $key = $this->getStackKey();
        $data = $this->getStack();
        if (!is_null($widgetId)) {
            unset($data[$widgetId]);
        } else {
            $data = [];
        }
        return \Yii::$app->session->set($key, $data);
    }

    /**
     * @param null $widgetId
     * @return mixed
     */
    public function getStack($widgetId = null)
    {
        $key = self::getStackKey();
        $data = \Yii::$app->session->get($key, []);
        return is_null($widgetId) ? $data : ArrayHelper::getValue($data, $widgetId);
    }

    /**
     * @param $checksum
     * @return array
     */
    public function getStackByChecksum($checksum)
    {
        foreach ($this->getStack() as $item) {
            if (($value = ArrayHelper::getValue($item, $this->checksumParam . '.0')) && $value == $checksum) {
                unset($item[$this->checksumParam]);
                return $item;
            }
        }
        return [];
    }
}