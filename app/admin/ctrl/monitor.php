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
use app\index\model\task;
use icf\lib\db;
use icf\lib\log;

class monitor {

    /**
     * 开始监控
     * @author Farmer
     */
    public function start() {
        if (config('monitor_status') == 1) {
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
        while (1) {
            try {
                $row = db::table('action_task')
                    ->where('task_last_time', strtotime(date('Y/m/d 00:00:00')), '<')
                    ->where('task_status', 1)->find();
                //3点开始
                if (config('monitor_status') == 11) {
                    break;
                }
                if (date('H') < 3) {
                    sleep(90);
                    db::reconnect();
                    continue;
                }
                if ($row) {
                    db::table('action_task')->where('tid', $row['tid'])->update(['task_status' => 2]);
                    $task = new task($row['tid']);
                    $ret = $task->run();
                    $log = new \app\common\model\log($row['uid']);
                    $log->action(json($ret), $ret['code']);
                    db::table('action_task')->where('tid', $row['tid'])
                        ->update(['task_last_time' => time(), 'task_status' => 1]);
                    continue;
                }
                sleep(10);
            } catch (\Exception $e) {
                $log = new \app\common\model\log($row['uid']);
                $log->system(json(['file' => $e->getFile(), 'line' => $e->getLine(), 'error' => $e->getMessage()]), 6);
            }
        }
        $log->notice('监控停止');
        config('monitor_status', 0);
    }

}