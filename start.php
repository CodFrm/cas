<?php
/**
 *============================
 * author:Farmer
 * time:2018/8/15 11:04
 * blog:blog.icodef.com
 * function:cli启动监控
 *============================
 */

require_once 'icf/loader.php';

//进入框架的入口
define('__ROOT_', __DIR__);
define('__DEFAULT_MODULE_', 'index');

ob_start();
icf\index::run();
ob_end_clean();

$monitor = new \app\admin\ctrl\monitor();
$monitor->start();
