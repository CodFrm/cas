<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:自动加载类
 *============================
 */

//注册自动装载
spl_autoload_register('loader::loadClass');

class loader {
    //路径映射
    static $path = ['lib' => 'icf/lib'];
    //已加载
    static $loaded = [];

    /**
     * 加载类
     * @param $className
     * @return bool
     */
    static function loadClass($className) {
        if (in_array($className, self::$loaded)) {
            return true;
        }
        self::$loaded[] = $className;
        //处理斜杠,linux系统中得用/
        $className = str_replace('\\', '/', $className);
        //取出左边的路径
        $rootPath = substr($className, 0, strpos($className, '/'));
        $loadFile = __ROOT_ . '/' . (isset(loader::$path[$rootPath]) ? loader::$path[$rootPath] : $rootPath);
        $loadFile .= substr($className, strpos($className, '/')) . '.php';
        if (!is_file($loadFile)) {
            $loadFile = __ROOT_ . '/icf/lib/' . (isset(loader::$path[$rootPath]) ? loader::$path[$rootPath] : $rootPath);
            $loadFile .= substr($className, strpos($className, '/')) . '.php';
        }
        if (is_file($loadFile)) {
            require_once $loadFile;
        }
        return true;
    }
}