<?php


namespace carono\checksum;

/**
 * Class Checksum
 *
 * @package app\helpers
 */
class Checksum
{
    /**
     * @param $array
     * @param null $salt
     * @return string
     */
    public static function calculate($array, $salt = null)
    {
        if ($key = static::formKey($array)) {
            return md5($salt . $key);
        } else {
            return null;
        }
    }

    /**
     * @param $array
     * @param $hash
     * @param null $salt
     * @return bool
     */
    public static function validate($array, $hash, $salt = null)
    {
        $realHash = static::calculate($array, $salt);
        return $realHash == $hash;
    }

    /**
     * @param $array
     * @return array
     */
    public static function formKeyPartials($array)
    {
        $result = [];
        foreach ((array)$array as $model => $values) {
            foreach ((array)$values as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
//                            $result[] = join('=', [$model,is_numeric($key) ? "{$value}[{$subKey}]" : "{$key}[{$subKey}]"]);
                        $result[] = join('=', [$model, is_numeric($key) ? $value : $key]);
                        break;
                    }
                } else {
                    $result[] = join('=', [$model, is_numeric($key) ? $value : $key]);
                }
            }
        }
        $result = array_unique($result);
        sort($result);
        return $result;
    }

    /**
     * @param $array
     * @return string
     */
    public static function formKey($array)
    {
        $result = join('|', static::formKeyPartials($array));
        return $result;
    }
}