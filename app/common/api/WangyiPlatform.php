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
        $this->httpRequest->https();
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

    public function SignMusic($actMsg) {
        $cookie = $actMsg['pu_cookie'];
        $this->httpRequest->setCookie($cookie);
        $csrf_token = getStrMid($cookie, '__csrf=', ';');
        $cookie = 'usertrack=c+xxC1mdDhE2WhpdA9SbAg==; _ntes_nnid=b81e4f1584b44747365625511be9df63,1503465086429; _ntes_nuid=b81e4f1584b44747365625511be9df63; vjuids=1f0b03fcd.15ea35c85ea.0.5988fdb9321cd; _ga=GA1.2.324697652.1503465088; P_INFO=a958139621@163.com|1513685733|0|mail163|00&99|jis&1513676467&mail#hun&430100#10#0#0|186377&0|mail&dz&mailuni|a958139621@163.com; _iuqxldmzr_=32; vjlast=1505979303.1516522136.22; vinfo_n_f_l_n3=1c0b8cb0868354b4.1.5.1505979303429.1513930428928.1516522141216; __utmc=94650624; __utmz=94650624.1520653113.5.4.utmcsr=baidu|utmccn=(organic)|utmcmd=organic; __utma=94650624.324697652.1503465088.1520653113.1520660032.6; JSESSIONID-WYYY=1dWPwAGVY9s56R2CUdIQ1sG5im5myrW6Kg12lmQ4VlnoQqZkOd1ZOmMG0h53evYQDMUB48xV%2BtzZ5h3taOG7yCEcaImCwa%5ClU%2BuKkk0kiny88CJAlGgsiCOg9aiu98oOy0j8DgcKT2RYczNa2Ggo5omPOYGOtFpwt4P%2FEtwk7Wcphkcj%3A1520661835552; __remember_me=true; jsessionid-cpta=gj7pKLoWWt1kzGSKK2AYhxMSdgIctOMhCrWymtozNxcBUMNhljcx%5CekNBD7Xc8z%2FXWWDTuuUMPiKFgvb6x%2BUk%2FVic4kFce46XnjEER%5CH3FNpfrMI7uya1%5C2GT89qxFPQzWn2LT6et5fYq5B2pd6%5CvrK%5CP9uUgVtkgNSmOyfV%2Be0nUg0k%3A1520661760091; c98xpt_=30; NETEASE_WDA_UID=1394487863#|#1520660950466; MUSIC_U=51754dcc091d478f088c505a3e34d95d05067c8129c75d54c2fad9a12647807272b261356f347eb7aa2bd26473a8b92673c6664c0383301f25c1f759807ff837e89ac33de7487e5fde39c620ce8469a8; __csrf=dd3af9cf083ed88d2e99fe18651e82c5; __utmb=94650624.21.10.1520660032';
        $http = new http('http://music.163.com/weapi/login/token/refresh?csrf_token=' . $csrf_token);
        $http->setCookie($cookie);
        $http->setopt(CURLOPT_HEADER, true);
        $post = 'params=' .
            urlencode(Encrypt::wy_encrypt(Encrypt::wy_encrypt('{"csrf_token":"' . $csrf_token . '"}',
                '0CoJUm6Qyw8W8jud'), 'OKZSxCIegROmuPzk')) . '&encSecKey=596a4853ee7618edb0192bc26b8ff9c88992b4e455c16585be0c41125f56f8dd7eeb9394e0bffc412801bf3ef2d86d52c50e5f19d3aab8e3cca724f9a2b0ac98718b961021e0d488fc1d63772a975841593f4094aa187989eae7f59fe68d3b7077393150b2f529f305fb89068f9ea35d2eacab9188ac8891e911e5513c098b3e';
        $data = $http->post($post);
        $data = str_replace('__csrf=""', 'error', $data);
        $new_csrf_token = getStrMid($data, '__csrf=', ';');
        $cookie = str_replace('__csrf=' . $csrf_token, '__csrf=' . $new_csrf_token, $cookie);
        $http->setCookie($cookie);
        $csrf_token = $new_csrf_token;
        $encText = Encrypt::wy_encrypt('{"type":1,"csrf_token":"' . $csrf_token . '"}',
            '0CoJUm6Qyw8W8jud');
        $encText = Encrypt::wy_encrypt($encText, 'OKZSxCIegROmuPzk');
        $http->setHeader(['Referer: http://music.163.com/discover']);
        $http->setopt(CURLOPT_HEADER, true);
        $post = 'params=' . urlencode($encText) . '&encSecKey=596a4853ee7618edb0192bc26b8ff9c88992b4e455c16585be0c41125f56f8dd7eeb9394e0bffc412801bf3ef2d86d52c50e5f19d3aab8e3cca724f9a2b0ac98718b961021e0d488fc1d63772a975841593f4094aa187989eae7f59fe68d3b7077393150b2f529f305fb89068f9ea35d2eacab9188ac8891e911e5513c098b3e';
        $msgJson = $http->post('http://music.163.com/weapi/point/dailyTask?csrf_token=' . $csrf_token, $post);
        return $msgJson;
    }

}