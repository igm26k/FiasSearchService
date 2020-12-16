<?php

namespace App\Helpers;

/**
 * Class Composer
 *
 * @package App\Helpers
 */
class Composer
{
    private static $_file;

    private static function _open()
    {
        self::$_file = (array)json_decode(file_get_contents(base_path() . '/composer.json'));
    }

    /**
     * @param $key
     *
     * @return string|array
     */
    public static function get($key)
    {
        self::_open();

        return self::$_file[$key];
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return self::get('version') . '-' . self::get('revision') . '+' . self::get('build');
    }
}
