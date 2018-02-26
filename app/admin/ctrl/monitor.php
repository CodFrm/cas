<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/26
 * function:监控
 *============================
 */

namespace app\admin\ctrl;


use app\common\api\BaiduPlatform;
use icf\lib\db;
use icf\lib\log;

class monitor {
    public function __construct() {
        $w_ip = ['127.0.0.1', '::', 'localhost'];
        if (!in_array(getip(), $w_ip)) {
            _404();
            exit();
        }
    }

    /**
     * 开始监控
     * @author Farmer
     */
    public function start() {
        if (config('monitor_status') == 1 && false) {
            return 'error';
        }
        config('monitor_status', 1);
        set_time_limit(0);
        if (function_exists('pcntl_fork')) {
            $pid_list = [];
            for ($i = 0; $i < 4; $i++) {
                $tmp_pid = pcntl_fork();
                if ($tmp_pid == 0) {
                    $this->monitor();
                } else if ($tmp_pid > 0) {
                    $pid_list[] = $tmp_pid;
                }
            }
            foreach ($pid_list as $p) {
                pcntl_wait($p);
            }
        } else {
            $this->monitor();
        }
    }

    private function monitor() {
        $log = new log();
        $log->notice('监控开启');
//        while (1) {
        //查询未操作记录
        $row = db::table('action_param as a')->join(':action as b', 'a.aid=b.aid')
            ->join(':platform as c', 'b.pid=c.pid')
            ->where('action_last_time', strtotime(date('Y/m/d 03:00:00')), '<')
            ->where('action_status', 1)->find();
        if ($row) {
            db::table('action_param')->where('param_id', $row['param_id'])->update(['action_status' => 2]);
            try {
                $platApi = 'app\\common\\api\\' . $row['platform_api'];
                $platApi = new $platApi(['uid' => $row['uid'], 'pid' => $row['pid']]) ?: new BaiduPlatform(_post('cookie'));
                $param = $platApi->VerifyAction($row['action_api']);
                if ($param) {
                    $row['param'] = $param;
                    $param = implode(',', $param);
                    db::table('action_param')->update(['action_param' => $param]);
                    $row['action_param'] = $param;
                    $ret = call_user_func([
                        $platApi, $row['action_api']
                    ], $row);
//                    break;
                } else {
                    $log = new \app\common\model\log($row['uid']);
                    $log->action(json(['aid' => $row['aid'], 'msg' => '操作失败']), 2);
                }
            } catch (\Exception $e) {
                $log = new \app\common\model\log($row['uid']);
                $log->system(json(['file' => $e->getFile(), 'line' => $e->getLine(), 'error' => $e->getMessage()]), 6);
            }
            db::table('action_param')->where('param_id', $row['param_id'])
                ->update(['action_last_time' => time(), 'action_status' => 1]);
        }
        if (config('monitor_status') == 11) {
            $log->notice('监控停止');
            config('monitor_status', 0);
        }
//            sleep(10);
//        }
    }
}