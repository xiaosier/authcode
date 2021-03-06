<?php

namespace Encryption;

class Authcode
{

    /**
     * Encode the given value.
     *
     * @param  string  $value
     * @param  string  $key
     * @param  integer $expiry
     * @return string
     */
    public static function encode($value, $key, $expiry = 0)
    {
        return static::_authcode($value, 'ENCODE', $key, $expiry);
    }

    /**
     * Encode the given value remain equal signs.
     *
     *
     *
     * @param  string  $value
     * @param  string  $key
     * @param  integer $expiry
     * @return string
     */
    public static function encodeRemainEqualsigns($value, $key, $expiry = 0)
    {
        return static::_authcode($value, 'ENCODE', $key, $expiry, false);
    }

    /**
     * Decode the given value.
     *
     * @param  string $value
     * @param  string $key
     * @return string
     */
    public static function decode($value, $key)
    {
        return static::_authcode($value, 'DECODE', $key);
    }

    /**
     * Authcode function, from discuz source.
     *
     * @param  string  $string
     * @param  string  $operation
     * @param  string  $key
     * @param  integer  $expiry
     * @param  boolean  $replaceEqual
     * @return string
     */
    protected static function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0, $replaceEqual = true)
    {

        $ckey_length = 4;

        $key = md5($key ? $key : 'AUTHCODE_KEY');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            if ($replaceEqual === true) {
                return $keyc.str_replace('=', '', base64_encode($result));
            } else {
                return $keyc.base64_encode($result);
            }
        }
    }

}