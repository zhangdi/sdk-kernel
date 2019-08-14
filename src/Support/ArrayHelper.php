<?php


namespace ZhangDi\SdkKernel\Support;


class ArrayHelper
{
    /**
     * @param array $array
     * @param string|int $key
     * @return bool
     */
    public static function exists(array $array, $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * @param array $array
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function get(array $array, $key, $default = null)
    {
        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::get($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_array($array)) {
            return static::exists($array, $key) ? $array[$key] : $default;
        }

        return $default;
    }
}