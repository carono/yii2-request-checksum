<?php


namespace carono\checksum;

use yii\helpers\Html;

/**
 * Class ActiveField
 *
 * @package carono\checksum
 */
class ActiveField extends \yii\widgets\ActiveField
{
    public function __toString()
    {
        $string = parent::__toString();
        if (\Yii::$app->request instanceof Request && preg_match('#<input|<select|<textarea#', $string)) {
            $attribute = Html::getAttributeName($this->attribute);
            \Yii::$app->request->stackField($this->form->id, $this->model->formName(), $attribute);
        }
        return $string;
    }
}