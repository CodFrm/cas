<?php
/**
 *============================
 * author:Farmer
 * time:2018/3/28
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace app\common\api;


use app\common\BasePlatform;
use icf\lib\other\http;

class V2EXPlatform extends BasePlatform {
    public function __construct($param) {
        parent::__construct($param);
        $this->httpRequest = new http();
        $this->httpRequest->https();
    }

    public function VerifyAccount() {
        // TODO: Implement VerifyAccount() method.
        $this->httpRequest->setCookie($this->cookie);
        $data = $this->httpRequest->get('https://www.v2ex.com/');
        $matches = [];
        preg_match('/<a href="\/member\/(.*?)" class="top">(.*?)<\/a>/',$data, $matches);
        if(isset($matches[2])){
            return $matches[2];
        }
        return false;
    }

    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.
    }

    public function VerifyActionResult($actionRet) {
        // TODO: Implement VerifyActionResult() method.
    }
}