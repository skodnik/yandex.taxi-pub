<?php

if (!function_exists('env')) {
    /**
     * Возвращает значения переменной окружения при ее наличии
     *
     * @param $key
     * @param false $if_not_exist
     * @return false|mixed
     */
    function env($key, $if_not_exist = false)
    {
        return $_ENV[$key] ?? $if_not_exist;
    }
}

if (!function_exists('isJson')) {
    /**
     * Проверка корректности json
     *
     * @param $string
     * @return bool
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('makeId')) {
    /**
     * Генератор ID для запроса в Яндекс Такси
     *
     * @param $string
     * @return bool
     */
    function makeId()
    {
        return md5(microtime());
    }
}