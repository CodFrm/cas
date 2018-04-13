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
        db::table('action_task')->where('task_status', 4)->update(['task_status' => 1]);
        while (1) {
            try {
                if (config('monitor_status') != 1) {
                    //停止监控
                    break;
                }
                if (date('H') < 3) {
                    //3点开始
                    sleep(90);
                    continue;
                }
                $row = db::table('action_task')
                    ->where('task_last_time', strtotime(date('Y/m/d 00:00:00')), '<')
                    ->where('task_status', 1)->find();
                if ($row) {
                    db::table('action_task')->where('tid', $row['tid'])->update(['task_status' => 4]);
                    $task = new task($row['tid']);
                    $ret = $task->run();
                    $user_log = new \app\common\model\log($row['uid']);
                    $user_log->action(json($ret), $ret['code']);
                    if ($ret['code'] == 2) {
                        //2为账号失效,将cookie关联的操作停止
                        db::table('action_task')->where('puid', $row['puid'])
                            ->update(['task_status' => 2]);
                    } else {
                        db::table('action_task')->where('tid', $row['tid'])
                            ->update(['task_last_time' => time(), 'task_status' => 1]);
                    }
                    continue;
                }
            } catch (\Exception $e) {
                db::reconnect();//数据库重连
                $log->error(json(['msg' => '监控错误', 'file' => $e->getFile(), 'line' => $e->getLine(), 'error' => $e->getMessage()]));
            }
            sleep(10);
        }
        $log->notice('监控停止');
        config('monitor_status', 0);
    }

}