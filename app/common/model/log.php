<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/26
 * function:
 *============================
 */

namespace app\common\model;


use icf\lib\db;
use icf\lib\model;

class log extends model {
    private $uid;

    public function __construct($uid = 0, $msg = '') {
        parent::__construct('log', $msg);
        $this->uid = $uid;
        $this->data['log_time'] = time();
    }

    public function action($log, $status) {
        $this->data = array_merge(['uid' => $this->uid, 'log_content' => $log, 'log_type' => $status + 10], $this->data);
        return $this->add();
    }

    public function system($log, $status) {
        $this->data = array_merge(['uid' => 0, 'log_content' => $log, 'log_type' => $status + 20], $this->data);
        return $this->add();
    }

}