<?php


namespace carono\checksum;

use yii\helpers\Html;

/**
 * Trait ActiveFieldTrait
 *
 * @package carono\checksum
 * @mixin \yii\widgets\ActiveField
 */
trait ActiveFieldTrait
{
    public function init()
    {
        if (\Yii::$app->request instanceof Request && \Yii::$app->request->enableChecksumValidation) {
            if (!$this->form->canGetProperty('_checksumInit')) {
                $this->form->attachBehavior('caronoChecksumBehavior', ActiveFormBehavior::className());
                \Yii::$app->request->clearStack($this->form->id);
            }
        }
        parent::init();
    }

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