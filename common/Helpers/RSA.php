<?php
namespace Common\Helpers;
/**
 * RSA 加密类
 * 参考文档：https://www.php.net/manual/zh/function.openssl-public-decrypt.php
 * @package Common\Helper
 * @author Nick  Github:https://github.com/tuo0
 */
class RSA
{
    //Block size for encryption block cipher
    const ENCRYPT_BLOCK_SIZE = 200;// this for 2048 bit key for example, leaving some room

    //Block size for decryption block cipher
    const DECRYPT_BLOCK_SIZE = 256;// this again for 2048 bit key

    /**
     * 私钥加密
     * @param string $string        原文
     * @param string $private_key   秘钥字符串
     * @return string      密文
     */
    public static function private_encrypt($string,$private_key)
    {
        // 检查key是否正确,以---开头
        if( strpos($private_key,'---') !== 0 ){
            $private_key = self::get_private_key( $private_key );
        }

        $private_key =  openssl_pkey_get_private($private_key);
        if ($private_key == false) {
            return false;
        }

        $encryptData = '';
        $crypto = '';
        foreach (str_split($string, self::ENCRYPT_BLOCK_SIZE) as $chunk) {
            $encryptionOk = openssl_private_encrypt($chunk, $encryptData, $private_key);

            if($encryptionOk === false)return false;

            $crypto = $crypto . $encryptData;
        }

        $crypto = base64_encode($crypto);
        return $crypto;
    }

    /**
     * 私钥解密
     * @param string $string        密文
     * @param string $private_key   秘钥字符串
     * @return string       原文
     */
    public static function private_decrypt( $string , $private_key )
    {
        // 检查key是否正确,以---开头
        if( strpos($private_key,'---') !== 0 ){
            $private_key = self::get_private_key( $private_key );
        }

        $private_key =  openssl_pkey_get_private($private_key);
        if ($private_key == false) {
            return false;
        }

        $string = base64_decode($string);
        $crypto = '';
        //分段解密
        foreach (str_split($string, self::DECRYPT_BLOCK_SIZE) as $chunk) {
            $decryptionOK = openssl_private_decrypt($chunk, $decryptData, $private_key);

            if($decryptionOK === false)return false;

            $crypto .= $decryptData;
        }
        return $crypto;
    }

    /**
     * 公钥加密
     * @param string $string        原文
     * @param string $public_key    秘钥字符串
     * @return string      密文
     */
    public static function public_encrypt( $string, $public_key )
    {
        // 检查key是否正确,以---开头
        if( strpos($public_key,'---') !== 0 ){
            $public_key = self::get_public_key( $public_key );
        }

        $public_key =  openssl_pkey_get_public($public_key);
        if ($public_key == false) {
            return false;
        }

        $encryptData = '';
        $crypto = '';
        foreach (str_split($string, self::ENCRYPT_BLOCK_SIZE) as $chunk) {
            $encryptionOk = openssl_public_encrypt($chunk, $encryptData, $public_key);

            if($encryptionOk === false)return false;

            $crypto = $crypto . $encryptData;
        }

        $crypto = base64_encode($crypto);
        return $crypto;
    }

    /**
     * 公钥解密
     * @param string $string        密文
     * @param string $public_key    秘钥字符串
     * @return string               原文
     */
    public static function public_decrypt( $string , $public_key )
    {
        // 检查key是否正确,以---开头
        if( strpos($public_key,'---') !== 0 ){
            $public_key = self::get_public_key( $public_key );
        }

        $public_key =  openssl_pkey_get_public($public_key);
        if ($public_key == false) {
            return false;
        }
        $string = base64_decode($string);

        $crypto = '';
        //分段解密
        foreach (str_split($string, self::DECRYPT_BLOCK_SIZE) as $chunk) {
            $decryptionOK = openssl_public_decrypt($chunk, $decryptData, $public_key);

            if($decryptionOK === false)return false;

            $crypto .= $decryptData;
        }
        return $crypto;
    }

    /**
     * 创建一组公钥私钥
     * @param boolean 是否获取单行文本
     * @return array 公钥私钥数组
     */
    public static function new( $uniline = false )
    {
        $res = openssl_pkey_new();

        openssl_pkey_export($res, $private_key);

        $d = openssl_pkey_get_details($res);

        $public_key = $d['key'];

        // 如果获取的是单行文本
        if( $uniline ){
            $private_key = implode('',array_slice(explode(PHP_EOL,$private_key),1,-2));
            $public_key = implode('',array_slice(explode(PHP_EOL,$public_key),1,-2));
        }

        return array(
            'private' => $private_key,
            'public'  => $public_key
        );
    }

    /**
     * 获取公钥
     * @param string $str 单行秘钥字符串
     * @return string
     */
    public static function get_public_key( $str )
    {
        $public_key = '-----BEGIN PUBLIC KEY-----' . PHP_EOL .
            chunk_split($str, 64, PHP_EOL) .
            '-----END PUBLIC KEY-----' . PHP_EOL;
        return $public_key;
    }

    /**
     * 获取私钥
     * @param string $str 单行秘钥字符串
     * @return string
     */
    public static function get_private_key( $str )
    {
        $private_key = '-----BEGIN PRIVATE KEY-----' . PHP_EOL .
            chunk_split($str, 64, PHP_EOL) .
            '-----END PRIVATE KEY-----' . PHP_EOL;
        return $private_key;
    }
}
