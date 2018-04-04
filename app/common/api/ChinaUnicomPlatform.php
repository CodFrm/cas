<?php
/**
 *============================
 * author:Farmer
 * time:2018/3/30
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace app\common\api;


use app\common\BasePlatform;
use app\common\PlatformLogin;

class ChinaUnicomPlatform extends BasePlatform implements PlatformLogin {
    public function VerifyAccount() {
        // TODO: Implement VerifyAccount() method.
    }
    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.
    }
    public function VerifyActionResult($actionRet) {
        // TODO: Implement VerifyActionResult() method.
    }
    public function Login($u, $p) {
        // TODO: Implement Login() method.
        return false;
    }
}