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
    //开启restful
    'rest' => true,
    //模块,控制器,操作 默认关键字
    'module_key' => 'm',
    'ctrl_key' => 'c',
    'action_key' => 'a',
    'route' => ['get' => ['start' => 'index->sign->start']],
    'tpl_suffix' => 'html',
    'log' => true,
    //url 样式
    //0=module/ctrl/action/key1/value1/key2/value2
    //1=module.php?{$ctrl_key}=ctrl&{$action_key}=action&key1=value1
    //2=?{$module_key}=module&{$ctrl_key}=ctrl&{$action_key}=action&key1=value1
    'url_style' => 1
];