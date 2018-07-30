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
            return hash('sha256', $key . $salt);
        }

        return null;
    }

    /**
     * @param $array
     * @param $hash
     * @param null $salt
     * @return bool
     */
    public static function validate($array, $hash, $salt = null)
    {
        return static::calculate($array, $salt) === $hash;
    }

    /**
     * @param $array
     * @return array
     */
    public static function formKeyPartials($array)
    {
        $array = static::prepareData($array);
        $result = [];
        foreach ((array)$array as $model => $values) {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            $result[] = implode('=', [$model, is_numeric($key) ? $value : $key]);
                            break;
                        }
                    } else {
                        $result[] = implode('=', [$model, is_numeric($key) ? $value : $key]);
                    }
                }
            }
        }
        $result = array_unique($result);
        sort($result);
        return $result;
    }

    protected static function prepareData($array)
    {
        $array = array_filter($array ?: [], 'is_array');

        return $array;
    }

    /**
     * @param $array
     * @return string
     */
    public static function formKey($array)
    {
        return implode('|', static::formKeyPartials($array));
    }
}