<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/24
 * function:
 *============================
 */

namespace app\common\api;


use app\common\BasePlatform;
use icf\lib\other\http;

class BilibiliPlatform extends BasePlatform {

    public function __construct($param) {
        parent::__construct($param);
        $this->httpRequest = new http();
        $this->httpRequest->https();
    }

    public function VerifyAccount() {
        // TODO: Implement VerifyAccount() method.
        $this->httpRequest->setCookie($this->cookie);
        $data = $this->httpRequest->get('https://api.bilibili.com/x/web-interface/nav');
        return getStrMid($data, 'uname":"', '",');
    }

    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.

        return true;
    }

    public function VerifyActionResult($actionRet) {
        // TODO: Implement VerifyActionResult() method.
        if (isset($actionRet['code'])) {
            if ($actionRet['code'] == -401) {
                return 2;
            } else if ($actionRet['code'] != 0) {
                return 1;
            }
            return 0;
        }
        return 1;
    }

    public function IsLogin() {
        // TODO: Implement IsLogin() method.
        return false;
    }

    public function SignLive($actMsg) {
        $cookie = $actMsg['pu_cookie'];
        $this->httpRequest->setCookie($cookie);
        $msgJson = $this->httpRequest->get('https://api.live.bilibili.com/sign/doSign');
        return json_decode($msgJson, true);
    }

}