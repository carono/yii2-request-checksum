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
    protected $fieldStack = [];

    public function run()
    {
        if (\Yii::$app->request instanceof Request && \Yii::$app->request->enableChecksumValidation) {
            echo Html::hiddenInput(\Yii::$app->request->checksumParam, Checksum::calculate(ActiveField::$fieldStack));
            ActiveField::$fieldStack = [];
        }
        return parent::run();
    }
}