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
use icf\lib\db;

class user extends authCtrl {
    public function index() {
        view()->assign('platform', db::table('platform')->select()->fetchAll());
        view()->display();
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

    public function postAction() {
        $pid = _post('pid');
        $aid = _post('aid');
        if (db::table('action_param')->where('uid', _cookie('uid'))->where('aid', $aid)->count()) {
            return new Error(-1, '已经添加过一次');
        }
        if (!($accountRow = db::table('platform_account')->where(['pid' => $pid, 'uid' => _cookie('uid')])->find())) {
            return new Error(-1, '找不到账号');
        }
        $ret = $this->verify_action($pid, $aid);
        if (is_array($ret)) {
            db::table('action_param')
                ->insert(['uid' => 1, 'puid' => $accountRow['puid'], 'aid' => $aid,
                    'action_param' => implode(',', $ret['param']), 'action_last_time' => 0, 'action_status' => 1]);
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