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

class BaiduPlatform extends BasePlatform {

    public function __construct($param) {
        parent::__construct($param);
        $this->httpRequest = new http();
        $this->httpRequest->https();
    }

    public function VerifyAccount() {
        // TODO: Implement VerifyAccount() method.
        $this->httpRequest->setCookie($this->cookie);
        $data = $this->httpRequest->get('https://tieba.baidu.com/mo/q/m?tn=bdIndex&');
        return getStrMid($data, 'uname: "', '",');
    }

    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.
        switch ($action) {
            case 'SignTieba':
                {
                    $this->httpRequest->setCookie($this->cookie);
                    $data = $this->httpRequest->get('https://tieba.baidu.com/mo/q/m?tn=bdIndex&');
                    preg_match_all('/<div class="forumTile_name">(.*?)<\/div>/U', $data, $match);
                    if (sizeof($match) >= 2) {
                        return $match[1];
                    }
                    break;
                }
        }
        return false;
    }

    private $BDUSS;

    public function SignTieba($actMsg) {
        if ($this->BDUSS = getStrMid($actMsg['pu_cookie'], 'BDUSS=', ';')) {
            $this->BDUSS = substr($actMsg['pu_cookie'], strpos($actMsg['pu_cookie'], 'BDUSS=') + 6);
        }
        foreach ($actMsg['param'] as $value) {
            $this->sign_tieba($value);
        }
    }

    public function sign_tieba($name) {

    }
}