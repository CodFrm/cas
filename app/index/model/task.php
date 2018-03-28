<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/26
 * blog:blog.icodef.com
 * function:
 *============================
 */


namespace app\index\model;


use app\common\api\BaiduPlatform;
use icf\lib\db;
use icf\lib\model;

class task extends model {
    private $tid;

    public function __construct($tid) {
        $this->tid = $tid;
        $this->data = db::table('action_task as a')
            ->join(':action as b', 'a.aid=b.aid')
            ->join(':platform as c', 'b.pid=c.pid')
            ->join(':platform_account as d', 'd.puid=a.puid')
            ->where('tid', $tid)->find();
    }

    /**
     * @param int $uid
     * @param array $limit
     * @return array
     */
    public static function getUserAllTaskMsg($uid, $limit = []) {
        return db::table('action_task as a')->join(':action as b', 'a.aid=b.aid')
            ->where('uid', $uid)->select()->fetchAll();
    }

    public function getTaskActionApi() {
        return $this->data['action_api'];
    }

    public function getTaskPlatformApi() {
        return $this->data['platform_api'];
    }

    public function getCookie() {
        return $this->data['pu_cookie'];
    }

    public function run() {
        $platApi = 'app\\common\\api\\' . $this->getTaskPlatformApi();
        $platApi = new $platApi($this->getCookie()) ?: new BaiduPlatform(_post('cookie'));
        $param = $platApi->VerifyAction($this->getTaskActionApi());
        if ($param) {
            $row = $this->data;
            $row['param'] = $param;
            $param = json($param);
            db::table('action_task')->where('tid', $this->tid)->update(['task_param' => $param]);
            $row['task_param'] = $param;
            $ret = call_user_func([
                $platApi, $this->getTaskActionApi()
            ], $row);
            $result = call_user_func([
                $platApi, 'VerifyActionResult'
            ], $ret);
            return ['code' => $result, 'aid' => $this->data['aid'], 'msg' => '成功', 'ret' => $ret];
        } else {
            return ['code' => 2, 'aid' => $this->data['aid'], 'msg' => '操作失败'];
        }
    }
}