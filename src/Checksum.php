<?php


namespace carono\checksum;

/**
 * Class Checksum
 *
 * @package app\helpers
 */
class Checksum
{
    public static function calculate($array, $salt = null)
    {
        $res = [];
        foreach ($array as $model => $values) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $res[] = join('|', [$model, is_numeric($key) ? "{$value}[{$subKey}]" : "{$key}[{$subKey}]"]);
                    }
                } else {
                    $res[] = join('|', [$model, is_numeric($key) ? $value : $key]);
                }
            }
        }
        return crypt(join('|', $res), $salt);
    }
}