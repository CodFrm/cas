<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/24
 * function:
 *============================
 */

namespace app\common;


use icf\lib\db;

abstract class BasePlatform {
    protected $cookie = '';
    protected $platApi = 'BasePlatform';
    protected $httpRequest;

    public function __construct($param) {
        if (is_string($param)) {
            $this->cookie = $param;
        } else {
            $data = db::table('platform_account')->where($param)->find();
            $this->cookie = $data['pu_cookie'];
        }
    }

    abstract public function VerifyAccount();

    abstract public function VerifyAction($action);
}