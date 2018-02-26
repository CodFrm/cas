<?php
/**
 *============================
 * author:Farmer
 * time:2017/12/28
 * blog:blog.icodef.com
 * function:阿里云短信发送
 *============================
 */


namespace icf\lib\info;


use icf\lib\other\http;

class AliSms {
    private $akID;
    private $akSecrt;

    public function __construct($AccessKeyID, $AccessKeySecret) {
        $this->akID = $AccessKeyID;
        $this->akSecrt = $AccessKeySecret;

    }

    public function sendSms($SignName, $TemplateCode, $TemplateParam, $PhoneNumbers) {
        return $this->request([
            'Action' => 'SendSms',
            'Version' => '2017-05-25',
            'RegionId' => 'cn',
            'SignName' => $SignName,
            'TemplateCode' => $TemplateCode,
            'TemplateParam' => json_encode($TemplateParam, JSON_UNESCAPED_UNICODE),
            'PhoneNumbers' => $PhoneNumbers
        ]);
    }

    public function request($param) {
        $req = array_merge([
            'AccessKeyId' => $this->akID,
            'Timestamp' => gmdate("Y-m-d\TH:i:s\Z"),
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => uniqid(mt_rand(0, 0xffff), true),
            'Format' => 'JSON'
        ], $param);
        $getParam = $this->getReqString($req);
        $Signature = $this->sign($getParam);
        $http = new http("https://dysmsapi.aliyuncs.com/?Signature={$Signature}{$getParam}");
        $http->https();
        $data = $http->get();
        return $data;
    }

    private function getReqString($req) {
        ksort($req);
        $ret = '';
        foreach ($req as $key => $value) {
            $ret .= '&' . $this->encode($key) . '=' . $this->encode($value);
        }
        return $ret;
    }

    private function sign($param) {
        $stringToSign = "GET&%2F&" . $this->encode(substr($param, 1));
        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $this->akSecrt . "&", true));
        return $this->encode($sign);
    }

    private function encode($str) {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

}