<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:配置文件
 *============================
 */

return [
    'debug' => true,
    'db' => [
        'type' => 'mysql',
        'server' => 'localhost',
        'port' => 3306,
        'db' => 'cas',
        'user' => 'root',
        'pwd' => '',
        'prefix' => 'cas_'
    ],
    'rest' => true,
    'module_key' => 'm',
    'ctrl_key' => 'c',
    'action_key' => 'a',
    'route' => ['get' => ['start' => 'index->sign->start']],
    'tpl_suffix' => 'html',
    'log' => false,
    'url_style' => 1
];