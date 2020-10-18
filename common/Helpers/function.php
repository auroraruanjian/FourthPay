<?php
// 支付页类型错误
define('PAY_VIEW_TYPE_ERROR',0);
// 支付页类型网页
define('PAY_VIEW_TYPE_HTML',1);
// 支付页类型原生
define('PAY_VIEW_TYPE_RAW',2);
// 支付页类型跳转URL
define('PAY_VIEW_TYPE_URL',3);
// 支付页类型二维码
define('PAY_VIEW_TYPE_QRCODE',4);


/**
 * 获取系统配置，从redis获取缓存2秒
 * @param        $key
 * @param string $default
 * @return string
 */
function getSysConfig($key,$default='')
{
    $value = Cache::store('apc')->remember(
        'redis:'.$key,
        2,
        function() use ($key,$default){
            return Redis::hget('sys_config',$key);
        }
    );

    return !empty($value)?$value:$default;
}

/**
 * ID 加密
 */
function id_encode($id)
{
    $private_key = 2682663594;

    $id = (string) $id;

    $length = strlen($id);
    $result = '';

    $a = 0;
    $b = $private_key % 33;
    for ($i = 0; $i < $length; $i++) {
        $v = (int) $id{$i};
        $a += $v;
        $b *= $v + 1;
    }

    $m = $a % 9;
    $n = ($a + $b) % 9;
    $key = (int) (($m + $n) / 2);

    $result .= chr(ord($m) + 17 + $private_key % 13);
    for ($i = 0; $i < $length; $i++) {
        $result .= chr(ord($id{$i}) + 17 + ($key + ($i * 13 + $key) * $private_key) % 16);
    }

    $result .= chr(ord($n) + 17 + $private_key % 16);
    return $result;
}

/**
 * ID 解密
 */
function id_decode($data)
{
    $private_key = 2682663594;

    $length = strlen($data);

    if ($length <= 2) {
        return '';
    }

    $result = '';

    $key = (int) (((int) chr(ord($data{0}) - $private_key % 13 - 17) + (int) chr(ord($data{$length - 1}) - $private_key % 16 - 17)) / 2);

    $length -= 1;
    for ($i = 1; $i < $length; $i++) {
        $result .= chr(ord($data{$i}) - 17 - ($key + $private_key * (($i - 1 ) * 13 + $key)) % 16);
    }

    if ($result === (string) ((int) $result)) {
        return $result;
    }

    return '';
}

/**
 * 签名验证
 * @param array $data 参数
 * @return boolean 验证通过：true 失败：false
 */
function md5_verify( $data , $md5_key )
{
    if  (md5_sign( $data,$md5_key) === $data['sign'] ) {
        return true;
    }

    return false;
}

/**
 * 签名
 * @param array $data 参数
 * @param $md5_key
 * @return boolean 验证通过：true 失败：false
 */
function md5_sign( $data , $md5_key )
{
    ksort($data);

    $sign_str = '';
    foreach ($data as $k => $v) {
        if ($k !== 'sign') {
            $sign_str .= $k.'='.$v.'&';
        }
    }

    return strtoupper(md5($sign_str . $md5_key));
}

