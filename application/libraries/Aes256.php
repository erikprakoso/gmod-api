<?php
if (!defined("BASEPATH")) exit("No direct script access allowed");

class aes256
{
    function encrypt($text)
    {
        $security = parse_ini_file('security.ini');
        $secret_key     = $security['encryption_key'];
        $key = $secret_key;
        $iv = str_repeat("\0", 16);
        $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $base64Result = base64_encode($encrypted);
        return $base64Result;
    }

    function decrypt($encryptedText)
    {
        $security = parse_ini_file('security.ini');
        $secret_key     = $security['encryption_key'];
        $key = $secret_key;
        $iv = str_repeat("\0", 16);
        $encryptedText = str_replace(['-', '_'], ['+', '/'], $encryptedText);
        $decrypted = openssl_decrypt(base64_decode($encryptedText), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $textResult = utf8_encode($decrypted);
        return $textResult;
    }
}
