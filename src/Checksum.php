<?php


namespace carono\checksum;

/**
 * Class Checksum
 *
 * @package carono\checksum
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
            foreach (array_keys((array)$values) as $key) {
                $result[] = implode('=', [$model, $key]);
            }
        }
        $result = array_unique($result);
        sort($result);
        return $result;
    }

    /**
     * @param $array
     * @return array
     */
    protected static function prepareData($array)
    {
        return array_filter($array ?: [], 'is_array');
    }

    /**
     * @param $array
     * @return string
     */
    public static function formKey($array)
    {
        return implode('|', static::formKeyPartials($array));
    }

    /**
     * @param $post
     * @param $stack
     * @param null $salt
     * @return bool
     */
    public static function compareStacks($post, $stack, $salt = null)
    {
        $checksum = static::calculate($stack, $salt);
        $post = static::mergeStacks($post, $stack);
        return static::validate($post, $checksum, $salt);
    }

    /**
     * @param $post
     * @param $stack
     * @return mixed
     */
    protected static function mergeStacks($post, $stack)
    {
        $postPartials = static::formKeyPartials($post);
        $stackPartials = static::formKeyPartials($stack);
        foreach (array_diff($stackPartials, $postPartials) as $lostPartial) {
            list($formName, $attribute) = explode('=', $lostPartial);
            $post[$formName][$attribute] = '';
        }
        return $post;
    }
}