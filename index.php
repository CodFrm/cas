<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:入口文件
 *============================
 */

require_once 'icf/loader.php';

//进入框架的入口
define('__ROOT_',__DIR__);
define('__DEFAULT_MODULE_','index');

icf\index::run();