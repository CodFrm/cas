<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/17
 * blog:blog.icodef.com
 * function:数据库驱动
 *============================
 */

namespace icf\lib;

use icf\lib\db\query;

/**
 * 数据库驱动
 * Class db
 * @package icf\lib
 */
class db {
    public static function table($table = '') {
        return new query($table);
    }

    /**
     * 获取上一次插入id
     * @author Farmer
     * @return int
     */
    public static function lastinsertid() {
        return (new query(''))->lastinsertid();
    }

    public static function reconnect(){
        (new query())->reconnect();
    }
}
