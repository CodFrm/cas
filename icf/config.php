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
        'server' => env('DB_SERVER'),
        'port' => env('DB_PORT'),
        'db' => env('DB_NAME'),
        'user' => env('DB_USER'),
        'pwd' => env('DB_PASSWORD'),
        'prefix' => env('DB_PREFIX')
    ],
    'rest' => true,
    'module_key' => 'm',
    'ctrl_key' => 'c',
    'action_key' => 'a',
    'route' => ['get' => ['start' => 'index->sign->start']],
    'tpl_suffix' => 'html',
    'log' => false,
    'url_style' => 2
];