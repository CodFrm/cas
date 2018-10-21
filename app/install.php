<?php
/**
 *============================
 * author:Farmer
 * time:18-10-21 下午10:23
 * blog:blog.icodef.com
 * function:
 *============================
 */

require_once realpath(__DIR__ . '/../') . '/icf/loader.php';

echo realpath(__DIR__ . '/../') . '/icf/loader.php';
//进入框架的入口
define('__ROOT_', realpath(__DIR__ . '/../'));
define('__DEFAULT_MODULE_', 'index');

ob_start();
icf\index::run();
ob_end_clean();

$dbcontent = explode(';', file_get_contents(__ROOT_ . "/db.sql"));
foreach ($dbcontent as $sql) {
    try {
        \icf\lib\db::table()->query($sql);
    } catch (Throwable $exception) {

    }
}
unlink(__ROOT_ . '/app/install.php');