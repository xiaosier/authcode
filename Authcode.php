<?php

namespace Authcode;

class Authcode
{
    /**
     * The authcode key.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new authcode instance.
     *
     * @param  string  $key
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Encode the given value.
     *
     * @param  string  $value
     * @param  bool  $serialize
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encode($value, $expiry = 0)
    {
        $this->authcode($value, 'ENCODE', $this->key, $expiry);
    }

    /**
     * Decode the given value.
     *
     * @param  string  $value
     * @param  bool  $serialize
     * @return string
     *
     */
    public function decode($value, $expiry = 0)
    {
        $this->authcode($value, 'DECODE', $this->key, $expiry);
    }

    /**
     * Authcode function, from discuz source.
     *
     * @param  string  $string
     * @param  string  $operation
     * @param  string  $key
     * @param  integer  $expiry
     * @return string
     */
    protected function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {

        $ckey_length = 4;

        $key = md5($key ? $key : UC_KEY);
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
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * Get the authcode key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}