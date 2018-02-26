<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:框架入口
 *============================
 */

namespace icf;

use icf\lib\log;
use lib\route;

require_once 'functions.php';
require_once 'common/common.php';
date_default_timezone_set('PRC');
$home = $_SERVER['REQUEST_URI'];
if (!empty($_SERVER['QUERY_STRING'])) {
    $home = substr($_SERVER['REQUEST_URI'], 0, strpos($home, '?'));
}
if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    $home = str_replace($_SERVER['PATH_INFO'], '', $home);
} else {
    $home = substr($home, 0, strrpos($home, '/'));
}
if (!isset($_SERVER['REQUEST_SCHEME'])) {
    $_SERVER['REQUEST_SCHEME'] = 'http';
}
define('__HOME_', '//' . $_SERVER['HTTP_HOST'] . $home);

class index {
    static $log;

    /**
     * 运行框架
     */
    public static function run() {
        self::$log = new log();
        //加载配置
        $config = include 'config.php';
        $modConfig = __ROOT_ . '/app/' . __DEFAULT_MODULE_ . '/config.php';
        if (file_exists($modConfig)) {
            $config = array_merge_in($config, include $modConfig);
        }
        _global('config', $config);
        //调试模式
        if (_config('debug')) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
            ini_set('display_errors', '0');
        }
        //记录这一次日志
        if (input('config.log')) {
            self::$log->notice('ip:' . getip() . ' url:' . getReqUrl() . ' post:' . json_encode($_POST, JSON_UNESCAPED_UNICODE)
                . ' cookie:' . json_encode($_COOKIE, JSON_UNESCAPED_UNICODE));
        }
        //路由加载
        if (isset($config['route'])) {
            foreach ($config['route'] as $key => $item) {
                route::add($key, $item);
            }
        }
        route::analyze();
    }
}