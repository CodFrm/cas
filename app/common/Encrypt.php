<?php
/**
 *============================
 * author:Farmer
 * time:2018/3/10
 * function:
 *============================
 */

namespace app\common;

class Encrypt {
    /**
     * 网易云的acs/cbc加密
     * @author Farmer
     * @param $data
     * @param $key
     * @return string
     */
    public static function wy_encrypt($data, $key) {
        return base64_encode(openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_PKCS1_PADDING, '0102030405060708'));
    }

    /**
     * RSA/ECB/PKCS1Padding
     * @param $data
     * @param $key
     * @return string
     */
    public static function lt_encrypt($data, $key) {
        openssl_public_encrypt($data, $data, $key, OPENSSL_PKCS1_PADDING);
        $data = base64_encode($data);
        return $data;
    }
}