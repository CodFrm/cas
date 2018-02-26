<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/24
 * function:
 *============================
 */

namespace app\common\model;

use icf\lib\model;

class platform extends model {
    public function __construct($pid = '') {
        if ($pid === '') {
            parent::__construct('platform');
        } else {
            parent::__construct('platform', ['pid' => $pid]);
        }
    }
}