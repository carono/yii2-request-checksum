<?php


namespace carono\checksum;

/**
 * Class ActiveField
 *
 * @package carono\checksum
 */
class ActiveField extends \yii\widgets\ActiveField
{
    static $fieldStack;

    public function __toString()
    {
        $string = parent::__toString();
        if (preg_match('#<input|<select|<textarea#', $string)) {
            self::$fieldStack[$this->model->formName()][] = $this->attribute;
        }
        return $string;
    }
}