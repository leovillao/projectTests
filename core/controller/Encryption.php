<?php
class Encryption {

    private static $encryptionKey = "ee6c3d02a8c814bfc18176143b323295789b3cbee383a0bd4812a8247a1f00c3";
    private static $encrytionType = "aes-256-cbc";

    public static function encrypt($value) {
        $iv_length = openssl_cipher_iv_length(self::$encrytionType);
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($value, self::$encrytionType, self::$encryptionKey, OPENSSL_RAW_DATA, $iv);
        $result = base64_encode($iv . $encrypted);
        return $result;
    }

    public static function decrypt($value) {
        $data = base64_decode($value);
        $iv_length = openssl_cipher_iv_length(self::$encrytionType);
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);
        $decrypted = openssl_decrypt($encrypted, self::$encrytionType, self::$encryptionKey, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}
?>