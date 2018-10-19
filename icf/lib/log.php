<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/20
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace icf\lib;


class log {
    private static $year;
    private static $month;
    private static $day;
    private static $filename = '';
    private static $hFile = 0;
    private static $isWrite = true;

    public function __construct() {
        if (!self::$hFile) {
            //初始化
            self::$year = date('Y');
            self::$month = date('m');
            self::$day = date('d');
            self::$filename = __ROOT_ . '/app/cache/log/' . self::$year . '/' . self::$month . '/' . self::$day . '.log';
            //检查缓存目录
            if (!file_exists(__ROOT_ . '/app/cache/log')) {
                if (!@mkdir(__ROOT_ . '/app/cache/log', 0777, true)) return;
            }
            if (!file_exists(__ROOT_ . '/app/cache/log/' . self::$year)) {
                if (!@mkdir(__ROOT_ . '/app/cache/log/' . self::$year, 0777, true)) return;
            }
            if (!file_exists(__ROOT_ . '/app/cache/log/' . self::$year . '/' . self::$month)) {
                if (!@mkdir(__ROOT_ . '/app/cache/log/' . self::$year . '/' . self::$month, 0777, true)) return;
            }
            if (!@(self::$hFile = fopen(self::$filename, 'a+'))) {
                self::$isWrite = false;
            }
        }
    }

    /**
     * 判断是否可以写入
     * @author Farmer
     * @return bool
     */
    public function isWrite() {
        return self::$isWrite;
    }

    /**
     * 写一行
     * @author Farmer
     * @param $content
     * @return bool|int
     */
    private function wline($content) {
        if (!self::$isWrite) return false;
        return fwrite(self::$hFile, $content . "\r\n");
    }

    /**
     * 通知日志
     * @author Farmer
     * @param $msg
     * @return bool|int
     */
    public function notice($msg) {
        return self::wline(date('Y-m-d H:i:s') . ">>> [notice] $msg");
    }

    /**
     * 错误日志
     * @author Farmer
     * @param $msg
     * @return bool|int
     */
    public function error($msg) {
        return self::wline(date('Y-m-d H:i:s') . ">>> [error] $msg");
    }

}