<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/25
 * function:
 *============================
 */

namespace app\common\model;

use icf\lib\db;
use icf\lib\model;

class user extends model {
    public function __construct($uid = 0) {
        if (!$uid) {
            parent::__construct('user', ['uid' => $uid]);
        }
    }

    /**
     * 验证用户名
     * @author Farmer
     * @param $user
     * @return bool|string
     */
    public static function isUser($user, $my = null) {
        if ($um = self::getUser($user)) {
            if (is_null($my)) {
                return '用户名已经被注册';
            }
            return ($um['user'] == $my ?: '用户名已经被注册');
        } else {
            return true;
        }
    }

    /**
     * 通过 邮箱/用户名/uid 获取用户数据
     * @param $user
     * @return mixed
     */
    public static function getUser($user) {
        return db::table('users')->where('uid', $user)->_or()->where('username', $user)->_or()->where('email', $user)->find();
    }

    public static function register($u, $p, $email) {
        db::table()->begin();
        db::table('users')->insert([
            'username' => $u,
            'password' => 'tmp',
            'email' => $email,
            'avatar' => 'default.png',
            'reg_time' => time()
        ]);
        $uid = db::lastinsertid();
        db::table('users')->where('uid', $uid)->update(['password' => self::encodePwd($uid, $p)]);
        db::table()->commit();
        return $uid;
    }

    /**
     * 通过uid获取用户数据
     * @param $uid
     * @return mixed
     */
    public static function uidUser($uid) {
        return db::table('users')->where(['uid' => $uid])->find();
    }

    /**
     * 申请一个账号
     * @param $postData
     * @return bool
     */
    public static function applyUser($postData) {
        $ret = verify($postData, [
            'act' => ['func' => [
                function ($act, $email) {
                    if (user::verifyToken($act, $email, 1)) {
                        return true;
                    }
                    return '错误的令牌';
                }, 'email'], 'msg' => '错误的令牌'],
            'username' => ['func' => ['\app\common\model\user::isUser'], 'regex' => ['/^[\x{4e00}-\x{9fa5}\w\@\.]{2,16}$/u', '用户名格式错误'], 'msg' => '用户名不能为空', 'sql' => 'username'],
            'password' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'password'],
            'email' => ['func' => ['\app\common\model\user::isEmail'], 'regex' => ['/^[\w\.]{1,16}@(qq\.com|foxmail.com|163\.com|outlook\.com)$/', '邮箱格式错误'], 'msg' => '邮箱不能为空', 'sql' => 'email'],
        ], $data);
        if ($ret === true) {
            //添加用户
            $data['avatar'] = 'default.png';
            $data['reg_time'] = time();
            db::table('users')->insert($data);
            $uid = db::table()->lastinsertid();
            user::deleteToken($postData['act']);
            return true;
        }
        return $ret;
    }

    /**
     * 创建一个验证令牌
     * @param $val
     * @param int $type
     * @return string
     */
    public static function createToken($val, $type = 0) {
        $len = 16;
        if ($type == 1) {
            $len = 64;
            \icf\lib\db::table('token')->where(['type' => 1, 'value' => $val])->delete();
        }
        $token = '';
        do {
            $token = getRandString($len, 2);
        } while (\icf\lib\db::table('token')->where('token', $token)->count());
        \icf\lib\db::table('token')->insert(['token' => $token, 'value' => $val, 'time' => time(), 'type' => $type]);
        return $token;
    }

    /**
     * 验证token是否有效,返回token数量
     * @param $token
     * @param $val
     * @param int $type
     * @return mixed
     */
    public static function verifyToken($token, $val, $type = 0) {
        $db = \icf\lib\db::table('token');
        $db->where(['token' => $token, 'value' => $val]);
        if ($type == 1) {
            $db->where('time', time() - 1800, '>');//30分钟有效期
        } else {
            $db->where('time', time() - 432000, '>');//5天有效期
        }
        return $db->count();
    }

    /**
     * 删除令牌
     * @param $token
     */
    public static function deleteToken($token) {
        \icf\lib\db::table('token')->where('token', $token)->delete();
    }

    /**
     * 验证邮箱
     * @author Farmer
     * @param $user
     * @return bool|string
     */
    public static function isEmail($email) {
        if (self::getUser($email)) {
            return '邮箱已经被注册';
        } else {
            return true;
        }
    }

    /**
     * 判断是否登陆
     * @return bool|mixed
     */
    public static function isLogin() {
        if ($uid = _cookie('uid') && $token = _cookie('token')) {
            return self::verifyToken(_cookie('token'), _cookie('uid'));
        }
        return false;
    }

    /**
     * 编码密码
     * @author Farmer
     * @param $uid
     * @param $pwd
     * @return string
     */
    public static function encodePwd($uid, $pwd) {
        $str = hash('sha256', $uid . $pwd . config('pwd_encode_salt'));
        return $str;
    }
}