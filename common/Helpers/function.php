<?php
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
