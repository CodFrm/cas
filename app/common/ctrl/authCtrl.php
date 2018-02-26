<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/25
 * function:
 *============================
 */

namespace app\common\ctrl;

use app\common\model\user;
use app\index\ctrl\index;
use icf\lib\db;

class authCtrl {
    protected $userMsg;
    protected $uid;

    public function __construct() {
        if (!user::isLogin()) {
            //没有登录跳回首页
            header('location:' . url('index', 'index', 'index'));
        }
        $this->userMsg = db::table('users')->where('uid', _cookie('uid'))->find();
        $this->uid = _cookie('uid');
    }
}