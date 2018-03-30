<?php
/**
 *============================
 * author:Farmer
 * time:2018/3/10
 * function:
 *============================
 */

namespace app\common\api;


use app\common\BasePlatform;
use app\common\Encrypt;
use icf\lib\other\http;

class WangyiPlatform extends BasePlatform {

    public function __construct($param) {
        parent::__construct($param);
        $this->httpRequest = new http();
        $this->httpRequest->setHeader([
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.119 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
        ]);
    }

    public function VerifyAccount() {
        // TODO: Implement VerifyAccount() method.
        $this->httpRequest->setCookie($this->cookie);
        $data = $this->httpRequest->get('http://music.163.com/');
        return getStrMid($data, 'nickname:"', '",');
    }

    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.
        return true;
    }

    public function VerifyActionResult($actionRet) {
        // TODO: Implement VerifyActionResult() method.
        if (isset($actionRet['code'])) {
            if ($actionRet['code'] != 200) {
                if ($actionRet['code'] == 301) {
                    return 2;
                }
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

    public function SignMusic($actMsg) {
        $cookie = $actMsg['pu_cookie'];
        $this->httpRequest->setCookie($cookie);
        $csrf_token = getStrMid($cookie, '__csrf=', ';');
        $this->httpRequest->setUrl('http://music.163.com/weapi/login/token/refresh?csrf_token=' . $csrf_token);
        $this->httpRequest->setCookie($cookie);
        $this->httpRequest->setopt(CURLOPT_HEADER, true);
        $post = 'params=' .
            urlencode(Encrypt::wy_encrypt(Encrypt::wy_encrypt('{"csrf_token":"' . $csrf_token . '"}',
                '0CoJUm6Qyw8W8jud'), 'OKZSxCIegROmuPzk')) . '&encSecKey=596a4853ee7618edb0192bc26b8ff9c88992b4e455c16585be0c41125f56f8dd7eeb9394e0bffc412801bf3ef2d86d52c50e5f19d3aab8e3cca724f9a2b0ac98718b961021e0d488fc1d63772a975841593f4094aa187989eae7f59fe68d3b7077393150b2f529f305fb89068f9ea35d2eacab9188ac8891e911e5513c098b3e';
        $data = $this->httpRequest->post($post);
        $data = str_replace('__csrf=""', 'error', $data);
        $new_csrf_token = getStrMid($data, '__csrf=', ';');
        $cookie = str_replace('__csrf=' . $csrf_token, '__csrf=' . $new_csrf_token, $cookie);
        $this->httpRequest->setCookie($cookie);
        $csrf_token = $new_csrf_token;
        $encText = Encrypt::wy_encrypt('{"type":1,"csrf_token":"' . $csrf_token . '"}',
            '0CoJUm6Qyw8W8jud');
        $encText = Encrypt::wy_encrypt($encText, 'OKZSxCIegROmuPzk');
        $this->httpRequest->setHeader(['Referer: http://music.163.com/discover']);
        $this->httpRequest->setopt(CURLOPT_HEADER, 0);
        $post = 'params=' . urlencode($encText) . '&encSecKey=596a4853ee7618edb0192bc26b8ff9c88992b4e455c16585be0c41125f56f8dd7eeb9394e0bffc412801bf3ef2d86d52c50e5f19d3aab8e3cca724f9a2b0ac98718b961021e0d488fc1d63772a975841593f4094aa187989eae7f59fe68d3b7077393150b2f529f305fb89068f9ea35d2eacab9188ac8891e911e5513c098b3e';
        $msgJson = $this->httpRequest->post('http://music.163.com/weapi/point/dailyTask?csrf_token=' . $csrf_token, $post);
        return json_decode($msgJson, true);
    }

}