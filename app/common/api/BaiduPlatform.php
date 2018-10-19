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
        $this->httpRequest->setHeader([
            'Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
            'Upgrade-Insecure-Requests: 1'
        ]);
        $data = $this->httpRequest->get('https://tieba.baidu.com/index/tbwise/forum');
        return getStrMid($data, '?un=', '"');
    }

    public function VerifyAction($action) {
        // TODO: Implement VerifyAction() method.
        switch ($action) {
            case 'SignTieba':
                {
                    $this->httpRequest->setCookie($this->cookie);
                    $this->httpRequest->setHeader([
                        'Connection: keep-alive',
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
                        'Upgrade-Insecure-Requests: 1'
                    ]);
                    $data = $this->httpRequest->get('https://tieba.baidu.com/');
                    $data = getStrMid($data, "use('spage/widget/forumDirectory',", ");");
                    $tieba_arr = json_decode($data, true);
                    $ret_arr = [];
                    foreach ($tieba_arr['forums'] as $val) {
                        $tmp_arr = [$val['forum_name'], $val['forum_id']];
                        $ret_arr[] = $tmp_arr;
                    }
                    return $ret_arr;
                }
        }
        return false;
    }

    public function VerifyActionResult($actionRet) {
        // TODO: Implement VerifyActionResult() method.
        if (isset($actionRet['ret']['error_msg'][0]['error_code'])) {
            if ($actionRet['ret']['error_msg'][0]['error_code'] == 1) {
                return 2;
            }
            return 0;
        } else if ($actionRet == []) {
            return 0;
        }
        if ($actionRet == []) {
            return 0;
        }
        return 1;
    }

    private $BDUSS;

    public function SignTieba($actMsg) {
        if (!($this->BDUSS = getStrMid($actMsg['pu_cookie'], 'BDUSS=', ';'))) {
            $this->BDUSS = substr($actMsg['pu_cookie'], strpos($actMsg['pu_cookie'], 'BDUSS=') + 6);
        }
        $msgJson = [];
        foreach ($actMsg['param'] as $value) {
            $ret = $this->sign_tieba($value[0], $value[1]);
            if ($ret !== true) {
                $msgJson['error_list'][] = $value[0];
                $msgJson['error_msg'][] = $ret;
            }
        }
        return $msgJson;
    }

    public function getTbs() {
        $http = new http('http://tieba.baidu.com/dc/common/tbs');
        $http->setHeader([
            'User-Agent: bdtb for Android 6.5.8', 'Referer: http://tieba.baidu.com/', 'X-Forwarded-For: 115.28.1.' . mt_rand(1, 255)
        ]);
        $http->setCookie("BDUSS=" . $this->BDUSS);
        $json = json_decode($http->get(), true);
        return $json['tbs'];
    }

    public function addTiebaSign(&$data) {
        $data = array(
                '_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
                '_client_type' => '4',
                '_client_version' => '6.0.1',
                '_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
            ) + $data;
        $x = '';
        foreach ($data as $k => $v) {
            $x .= $k . '=' . $v;
        }
        $data['sign'] = strtoupper(md5($x . 'tiebaclient!!!'));
    }

    public function sign_tieba($name, $fid) {
        $tbs = $this->getTbs();
        $time = time() . rand(100, 999);
        $sign = 'BDUSS=' . urldecode($this->BDUSS) .
            '_client_id=wappc_1519637358655_406_client_type=2_client_version=9.3.8.0_phone_imei=000000000000000cuid=baidutiebaappa638e2bf-d021-4fd0-aa34-4d8e9b5cf991' .
            'fid=' . $fid . 'from=1019960rkw=' . $name . 'model=Android SDK built for x86_64net_type=4stErrorNums=1stMethod=1stMode=1stSize=966stTime=98stTimesNum=1stoken=b399b18b5d887995e96efafadfe4b87186e9fc7b6006bd98333f2201783dae15' .
            'tbs=' . $tbs . 'timestamp=' . $time . 'z_id=609C16C532BDDA19187A48FC6F100F36A5tiebaclient!!!';
        $data = 'BDUSS=' . $this->BDUSS .
            '&_client_id=wappc_1519637358655_406&_client_type=2&_client_version=9.3.8.0&_phone_imei=000000000000000&cuid=baidutiebaappa638e2bf-d021-4fd0-aa34-4d8e9b5cf991' .
            '&fid=' . $fid . '&from=1019960r&kw=' . urlencode($name) . '&model=Android+SDK+built+for+x86_64&net_type=4&sign=' . md5($sign) .
            '&stErrorNums=1&stMethod=1&stMode=1&stSize=966&stTime=98&stTimesNum=1' .
            '&stoken=b399b18b5d887995e96efafadfe4b87186e9fc7b6006bd98333f2201783dae15&tbs=' . $tbs . '&timestamp=' . $time . '&z_id=609C16C532BDDA19187A48FC6F100F36A5';
        $data = $this->httpRequest->post('http://c.tieba.baidu.com/c/c/forum/sign', $data);
        $tmpJson = json_decode($data, true);
        if (isset($tmpJson['error_code']) && $tmpJson['error_code'] != 0) {
            $tmpJson['sign'] = $sign;
            return $tmpJson;
        } else {
            return true;
        }
    }
}