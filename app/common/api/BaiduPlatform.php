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
                    $this->httpRequest->setHeader(['Connection: keep-alive',
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
                        'Upgrade-Insecure-Requests: 1']);
                    $data = $this->httpRequest->get('https://tieba.baidu.com/mo/q/m?tn=bdIndex&');
                    preg_match_all('/<li data-fn="(.*?)"[\s\S]+?<a data-fid="(.*?)"/', $data, $match, PREG_OFFSET_CAPTURE);
                    if (sizeof($match) >= 2) {
                        return $match[1];
                    }
                    break;
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

    public function sign_tieba($name, $fid) {
        $this->httpRequest->setHeader(['Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) CriOS/56.0.2924.75 Mobile/14E5239e Safari/602.1',
            'Upgrade-Insecure-Requests: 1']);
        $data = $this->httpRequest->get('https://tieba.baidu.com/f?kw=' . urlencode($name));
        $this->httpRequest->setHeader(['Content-Type: application/x-www-form-urlencoded',
            'Cookie: ka=open',
            'cuid: baidutiebaappa638e2bf-d021-4fd0-aa34-4d8e9b5cf991',
            'User-Agent: bdtb for Android 9.3.8.0',
            'client_logid: 1519637262934',
            'Connection: Keep-Alive',
            'client_user_token: 1089940244']);

        $tbs = getStrMid($data, '"tbs":"', '"');
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