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
        preg_match('/<a href="\/member\/(.*?)" class="top">(.*?)<\/a>/', $data, $matches);
        if (isset($matches[2])) {
            return $matches[2];
        }
        return false;
    }

    private $data;

    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.
        switch ($action) {
            case 'SignV2EX':
                $this->httpRequest->setCookie($this->cookie);
                $this->httpRequest->setRedirection(4);
                $this->data = $this->httpRequest->get('https://www.v2ex.com/mission/daily');
                if (strpos($this->data, '你要查看的页面需要先登录') !== false) {
                    return false;
                }
                break;
        }
        return true;
    }

    public function VerifyActionResult($actionRet) {
        // TODO: Implement VerifyActionResult() method.
        return $actionRet;
    }

    public function SignV2EX($actMsg) {
        if ($signUrl = getStrMid($this->data, 'value="领取 X 铜币" onclick="location.href = \'', '\';"')) {
            $signUrl = 'https://www.v2ex.com' . $signUrl;
        } else {
            return 1;
        }
        $data = $this->httpRequest->get($signUrl);
        if (strpos($data, '已成功领取每日登录奖励') >= 0) {
            return 0;
        }
        return 1;
    }
}