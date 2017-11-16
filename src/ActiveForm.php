<?php


namespace carono\checksum;

use yii\helpers\Html;

/**
 * Class ActiveForm
 *
 * @package app\components
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    public $fieldClass = 'carono\checksum\ActiveField';

    public function init()
    {
        parent::init();
        if (\Yii::$app->request instanceof Request && \Yii::$app->request->enableChecksumValidation) {
            \Yii::$app->request->clearStack($this->id);
        }
    }

    public function run()
    {
        if (\Yii::$app->request instanceof Request && \Yii::$app->request->enableChecksumValidation) {
            $stack = \Yii::$app->request->getStack($this->id);
            $key = Checksum::calculate($stack);
            \Yii::$app->request->stackField($this->id, \Yii::$app->request->checksumParam, $key);
            echo Html::hiddenInput(\Yii::$app->request->checksumParam, $key);
        }
        return parent::run();
    }
}