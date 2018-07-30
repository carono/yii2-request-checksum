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
class ChecksumBehavior extends Behavior
{
    public $_checksumInit = true;

    public function events()
    {
        return [
            Widget::EVENT_AFTER_RUN => 'registerChecksumField'
        ];
    }

    /**
     * @param \yii\base\WidgetEvent $event
     */
    public function registerChecksumField($event)
    {
        /**
         * @var Request $request
         */
        if (strtolower($this->owner->method) !== 'post' || (\Yii::$app->request instanceof Request && !\Yii::$app->request->checksumIsEnabled())) {
            return;
        }
        $request = \Yii::$app->request;
        $doc = new \DOMDocument();
        @$doc->loadHTML($event->result);
        $xpath = new \DOMXpath($doc);
        $inputs = $xpath->query('//input | //select | //textarea');
        $result = [];
        for ($j = 0; $j < $inputs->length; $j++) {
            $input = $inputs->item($j);
            $result[] = $input->getAttribute('name');
        }
        $result = array_unique($result);

        parse_str(implode('&', $result), $stack);
        unset($doc, $xpath);
        $checksum = $request->setStack($stack);
        $input = Html::hiddenInput(\Yii::$app->request->checksumParam, $checksum);
        $event->result = str_replace('</form>', $input . '</form>', $event->result);
//            $stack = \Yii::$app->request->getStack($this->owner->id);
//            $key = Checksum::calculate($stack, \Yii::$app->request->checksumKey);
//            \Yii::$app->request->stackField($this->owner->id, \Yii::$app->request->checksumParam, $key);
//            echo Html::hiddenInput(\Yii::$app->request->checksumParam, $key);
    }
}