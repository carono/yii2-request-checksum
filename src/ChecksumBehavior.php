<?php


namespace carono\checksum;


use DOMDocument;
use DOMElement;
use yii\base\Behavior;
use yii\base\ViewEvent;
use yii\web\View;

/**
 * Class ChecksumBehavior
 *
 * @package carono\checksum
 */
class ChecksumBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events()
    {
        return [
            View::EVENT_AFTER_RENDER => 'registerChecksumField'
        ];
    }

    /**
     * @param \yii\base\WidgetEvent $event
     */
    public function registerChecksumField(ViewEvent $event)
    {
        /**
         * @var DOMElement $form
         * @var DOMElement $element
         * @var Request $request
         */
        if (!$event->output) {
            return;
        }
        $output = mb_convert_encoding($event->output, 'HTML-ENTITIES', 'UTF-8');
        $document = new DOMDocument();
        $document->loadHTML($output, LIBXML_NOERROR);
        $xpath = new \DOMXPath($document);
        $request = \Yii::$app->request;
        foreach ($xpath->query("//form[@method='post']") as $form) {
            $items = [];
            foreach ($xpath->query('//input|//select|//textarea', $form) as $element) {
                $items[] = $element->getAttribute('name');
            }
            $items = array_unique($items);
            parse_str(implode('&', $items), $stack);
            $checksum = $request->setStack($stack);
            $input = $document->createElement('input');
            $input->setAttribute('name', \Yii::$app->request->checksumParam);
            $input->setAttribute('value', $checksum);
            $input->setAttribute('type', 'hidden');
            $form->appendChild($input);
        }
        $event->output = $document->saveHTML();
    }
}