<?php


namespace carono\checksum;


use yii\base\Behavior;
use yii\base\Widget;
use yii\helpers\Html;

class ActiveFormBehavior extends Behavior
{
    public $_checksumInit = true;

    public function events()
    {
        return [
            Widget::EVENT_BEFORE_RUN => 'registerChecksumField'
        ];
    }

    public function registerChecksumField()
    {
        if (\Yii::$app->request instanceof Request && \Yii::$app->request->enableChecksumValidation) {
            $stack = \Yii::$app->request->getStack($this->owner->id);
            $key = Checksum::calculate($stack);
            \Yii::$app->request->stackField($this->owner->id, \Yii::$app->request->checksumParam, $key);
            echo Html::hiddenInput(\Yii::$app->request->checksumParam, $key);
        }
    }
}