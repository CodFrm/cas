<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/25
 * function:
 *============================
 */

namespace app\index\ctrl;


use app\common\api\BaiduPlatform;
use app\common\ctrl\authCtrl;
use app\common\Error;
use app\common\model\platform;
use app\common\PlatformLogin;
use app\index\model\task;
use icf\lib\db;

class user extends authCtrl {
    public function index() {
        view()->assign('task', task::getUserAllTaskMsg($this->uid));
        view()->assign('platform', db::table('platform')->select()->fetchAll());
        view()->assign('account', db::table('platform_account as a')
            ->join(':platform as b', 'a.pid=b.pid')
            ->where('uid', $this->uid)
            ->select()->fetchAll());
        view()->display();
    }

    public function run($tid) {
        $task = new task($tid);
        $ret = $task->run();
        if (isset($ret['ret']['error_msg'])) {
            unset($ret['ret']['error_msg']);
        }
        return $ret;
    }

    public function update_cookie() {
        $actDb = db::table('platform_account')->where('uid', $this->uid)
            ->where('puid', input('post.puid'));
        $ret = $actDb->find();
        if (!$ret) {
            return new Error(-1, '没有找到相应的修改记录');
        }
        if (input('post.cookie')) {
            $pid = $ret['pid'];
            $retUser = $this->verify_account($pid);
            if (is_array($retUser)) {
                $actDb->update(['pu_status' => 1, 'pu_time' => time(),
                    'pu_u' => $retUser['u'], 'pu_cookie' => input('post.cookie')]);
                $ret = new Error(0, '修改成功');
            } else {
                $ret = new Error(-1, '错误的账号Cookie');
            }
        } else {
            $actDb->delete();
            $ret = new Error(1, '删除成功');
        }
        return $ret;
    }

    public function postAccount() {
        $pid = _post('pid');
        if (db::table('platform_account')->where('uid', _cookie('uid'))->where('pid', $pid)->count()) {
            return new Error(-1, '已经添加过一个账号');
        }
        $ret = $this->verify_account($pid);
        if (is_array($ret)) {
            db::table('platform_account')
                ->insert(['pid' => $pid, 'pu_time' => time(), 'uid' => _cookie('uid'), 'pu_u' => $ret['u'],
                    'pu_status' => 1, 'pu_cookie' => _post('cookie')]);
            return new Error(0, '添加成功');
        } else {
            return new Error(-1, $ret);
        }
    }

    public function getAction($pid) {
        return db::table('action')->where('pid', $pid)->select()->fetchAll();
    }

    public function plogin($pid) {
        $plat = new platform($pid);
        if ($plat->getData()) {
            $platApi = 'app\\common\\api\\' . $plat->_api;
            $platApi = new $platApi('') ?: new BaiduPlatform('');
            if($platApi instanceof PlatformLogin){
                return new Error(0, 'success', ['is_login' => 1]);
            }
            return new Error(-1, '没有账号登录的接口');
        }
        return new Error(-1, '不存在的平台');
    }

    public function postAction() {
        $pid = _post('pid');
        $aid = _post('aid');
        if (db::table('action_task')->where('uid', _cookie('uid'))->where('aid', $aid)->count()) {
            return new Error(-1, '已经添加过一次');
        }
        if (!($accountRow = db::table('platform_account')->where(['pid' => $pid, 'uid' => _cookie('uid')])->find())) {
            return new Error(-1, '找不到账号');
        }
        $ret = $this->verify_action($pid, $aid);
        if (is_array($ret)) {
            db::table('action_task')
                ->insert(['uid' => _cookie('uid'), 'puid' => $accountRow['puid'], 'aid' => $aid,
                    'task_param' => json($ret['param']), 'task_last_time' => 0, 'task_status' => 1]);
            return new Error(0, '添加成功');
        } else {
            return new Error(-1, $ret);
        }

    }

    private function verify_action($pid, $aid) {
        $plat = new platform($pid);
        if ($plat->getData()) {
            if ($actionRow = db::table('action')->where(['aid' => $aid, 'pid' => $pid])->find()) {
                $platApi = 'app\\common\\api\\' . $plat->_api;
                $platApi = new $platApi(['uid' => _cookie('uid'), 'pid' => $pid]) ?: new BaiduPlatform(_post('cookie'));
                if ($platData = $platApi->VerifyAction($actionRow['action_api'])) {
                    return ['param' => $platData];
                } else {
                    return '操作错误';
                }
            } else {
                return '操作不存在';
            }
        } else {
            return '平台不存在';
        }
    }

    private function verify_account($pid) {
        $plat = new platform($pid);
        if ($plat->getData()) {
            $platApi = 'app\\common\\api\\' . $plat->_api;
            $platApi = new $platApi(_post('cookie')) ?: new BaiduPlatform(_post('cookie'));
            if ($platData = $platApi->VerifyAccount()) {
                return ['u' => $platData];
            } else {
                return '账号错误';
            }
        } else {
            return '平台不存在';
        }
    }
}