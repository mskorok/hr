<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 17.11.7
 * Time: 10:40
 */

namespace App\Traits;

/**
 * Trait Ajax
 * @package App\Traits
 */
trait Ajax
{
    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * @param int $length
     * @return string
     */
    public static function random($length = 16): string
    {
        $string = '';

        while (($len = static::length($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= static::substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * @param $value
     * @return int
     */
    public static function length($value): int
    {
        return mb_strlen($value);
    }

    /**
     * @param $string
     * @param $start
     * @param null $length
     * @return string
     */
    public static function substr($string, $start, $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }
}
