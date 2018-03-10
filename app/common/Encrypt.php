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
        return base64_encode(openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_PKCS1_PADDING, '0102030405060708'));//OPENSSL_PKCS1_PADDING 不知道为什么可以与PKCS5通用,未深究
    }
}