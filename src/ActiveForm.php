<?php


namespace carono\checksum;

/**
 * Class ActiveForm
 *
 * @package app\components
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    public $fieldClass = 'carono\checksum\ActiveField';
}