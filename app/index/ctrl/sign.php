<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/23
 * function:
 *============================
 */

namespace app\index\ctrl;


use icf\lib\db;

class sign {
    public function start() {
        if (_config('debug') || config('is_start') == 0) {
            if (_config('debug') || pcntl_fork() == 0) {
                //子进程执行,轮询
//                while (1) {
                    if ($row = db::table('action_param')
                        ->where('last_time', strtotime(date('Y/m/d') . ' 02:00:00'), '<')->find()) {
                        print_r($row);
                    } else {
                        echo 'next';
//                        sleep(10000);
                    }
//                }

            }
        }
    }
}