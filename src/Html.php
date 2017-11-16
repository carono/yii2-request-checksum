<?php


namespace carono\checksum;


use yii\base\Widget;
use yii\helpers\ArrayHelper;

class Html extends \yii\helpers\Html
{
    public static function submitButton($content = 'Submit', $options = [])
    {
        if (\Yii::$app->request instanceof Request && \Yii::$app->request->checksumIsEnabled()) {
            $name = ArrayHelper::getValue($options, 'name');
            if ($name && Widget::$stack && ($widget = Widget::$stack[count(Widget::$stack) - 1])) {
                \Yii::$app->request->stackField($widget->id, $name, '');
            }
        }
        return parent::submitButton($content, $options);
    }
}