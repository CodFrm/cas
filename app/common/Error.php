<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/24
 * function:
 *============================
 */

namespace app\common;


class Error {
    private $code;
    private $msg;
    private $param;

    public function __construct($code, $msg, $param=[]) {
        $this->code = $code;
        $this->msg = $msg;
        $this->param = $param;
    }

    public function __toString() {
        // TODO: Implement __toString() method.
        header('Content-Type: application/json; charset=utf-8');
        $json = array_merge(['code' => $this->code, 'msg' => $this->msg], $this->param);
        return json($json);
    }
}