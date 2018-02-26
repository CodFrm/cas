<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/20
 * blog:blog.icodef.com
 * function:mysql 驱动
 *============================
 */

namespace icf\lib\db;
class mysql {
    public static function dns(){
        $dns = input('config.db.type') . ':dbname=' . input('config.db.db') . ';host=';
        $dns .= input('config.db.server') . ';charset=utf8';
        return $dns;
    }
}