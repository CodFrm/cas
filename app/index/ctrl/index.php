<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/23
 * function:
 *============================
 */

namespace app\index\ctrl;

use app\common\model\user;
use icf\lib\db;

class index {
    public function index() {
        view()->display();
    }

    protected function errorCode($code, $error = '') {
        //方便前端通过错误代码提示错误
        $errorCode = [
            '登录成功' => 0,
            '用户名已经被注册' => 10001,
            '用户名不能为空' => 10002,
            '用户名格式错误' => 10003,
            '用户不存在' => 10003,
            '密码不符合规范' => 10004,
            '密码错误' => 10005,
            '请输入密码' => 10006,
            '错误的令牌' => 10020
        ];
        if (empty($error)) {
            $error = $code;
            $code = -1;
        }
        if (isset($errorCode[$error])) $code = $errorCode[$error];
        return ['code' => $code, 'msg' => $error];
    }

    public function login() {
        $ret = verify($_POST, [
            'u' => ['msg' => '用户名不能为空', 'sql' => 'username'],
            'p' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'password']
        ], $data);
        if ($ret === true) {
            if ($userMsg = user::getUser($data['username'])) {
                if (user::encodePwd($userMsg['uid'], $data['password']) == $userMsg['password']) {
                    setcookie('token', user::createToken($userMsg['uid']), time() + 432000, getUrlRoot());
                    setcookie('uid', $userMsg['uid'], time() + 432000, getUrlRoot());
                    $ret = '登录成功';
                } else {
                    $ret = '密码错误';
                }
            } else {
                $ret = '用户不存在';
            }
        }
        return self::errorCode($ret);
    }
}