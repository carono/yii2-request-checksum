<?php


namespace carono\checksum;


use yii\base\Behavior;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * Class ActiveFormBehavior
 *
 * @package carono\checksum
 * @property ActiveForm $owner
 */
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
        if (strtolower($this->owner->method) == 'post' && \Yii::$app->request instanceof Request && \Yii::$app->request->checksumIsEnabled()) {
            $stack = \Yii::$app->request->getStack($this->owner->id);
            $key = Checksum::calculate($stack, \Yii::$app->request->checksumKey);
            \Yii::$app->request->stackField($this->owner->id, \Yii::$app->request->checksumParam, $key);
            echo Html::hiddenInput(\Yii::$app->request->checksumParam, $key);
        }
    }
}