<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/19 14:33
 * blog:blog.icodef.com
 * function:封装的curl模块
 *============================
 */

namespace icf\lib\other;


/**
 * Class http
 * @package icf\lib\other
 */
class http {
    private $curl;
    private $data;
    private $responseHeader = false;

    public function __construct($url = '') {
        $this->curl = curl_init($url);
        curl_setopt($this->curl, CURLOPT_HEADER, 0); //不返回header部分
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 10);
    }

    public function setopt($key, $value) {
        curl_setopt($this->curl, $key, $value);
    }

    public function responseHeader() {
        $this->setopt(CURLOPT_HEADER, $this->responseHeader = (!$this->responseHeader));
    }

    public function getResponseHeader() {
        if ($this->responseHeader) {
            return substr($this->data, 0, strpos($this->data, "\r\n\r\n"));
        }
        return false;
    }

    public function data() {
        if ($this->responseHeader) {
            return substr($this->data, strpos($this->data, "\r\n\r\n") + 4);
        }
        return $this->data;
    }

    public function getCookie() {
        $cookie = '';
        preg_match_all('/Set-Cookie:(.*);/iU', $this->getResponseHeader(), $matchCookie);
        foreach ($matchCookie[1] as $value) {
            $cookie .= $value . ';';
        }
        return $cookie;
    }

    public function getHeader($opt = 0) {
        return curl_getinfo($this->curl, $opt);
    }

    public function setRedirection($value = 1) {
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $value);
    }

    public function __destruct() {
        // TODO: Implement __destruct() method.
        curl_close($this->curl);
    }

    public function setCookie($cookie) {
        curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
    }

    public function setHeader($header) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
    }

    public function setUrl($url) {
        curl_setopt($this->curl, CURLOPT_URL, $url);
    }

    public function https() {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function get($url = '') {
        if (!empty($url)) {
            $this->setUrl($url);
        }
        curl_setopt($this->curl, CURLOPT_POST, 0);
        return $this->access();
    }

    public function post($url = '', $data = '') {
        curl_setopt($this->curl, CURLOPT_POST, 1);
        if (!empty($data)) {
            $this->setUrl($url);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $url);
        }
        return $this->access();
    }

    public function access() {
        $response = curl_exec($this->curl);
        if ($response == false) return curl_error($this->curl);
        return $this->data = $response;
    }
}