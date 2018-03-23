<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/16
 * blog:blog.icodef.com
 * function:模板引擎
 *============================
 */

namespace icf\lib;


/**
 * 视图类
 *
 * @author Farmer
 * @version 1.0
 */
class view {
    private static $tplVar = array();

    public function __construct() {
        //检查缓存目录
        if (!file_exists(__ROOT_ . '/app/cache/tpl')) {
            mkdir(__ROOT_ . '/app/cache/tpl', 0777, true);
        }
    }

    /**
     * 设置模板所在的模块
     * @param $module
     */
    public function setModule($module) {
        $this->module = $module;
    }

    /**
     * 设置值
     *
     * @author Farmer
     * @param string $param
     * @param mixed $value
     * @return
     *
     */
    public function assign($param, $value) {
        self::$tplVar [$param] = $value;
    }

    /**
     * 输出模板内容
     * @author Farmer
     * @param string $filename
     * @return null
     */
    public function display($filename = '') {
        $cache = self::compile($filename);
        if ($cache) {
            header('Content-type: text/html; charset=utf-8');
            echo $cache;
            return true;
        }
        return false;
    }

    /**
     * 返回编译内容
     * @param $filename
     * @return string
     */
    public function compile($filename = '') {
        if ($filename === '') {
            $filename = input('action');
        }
        if (strpos($filename, '/') === false) {
            $path = __ROOT_ . '/app/' . input('module') . '/tpl/' . input('ctrl') . '/' . $filename;
        } else {
            $path = __ROOT_ . '/app/' . input('module') . '/tpl/' . $filename;
        }
        $suffix = '.' . input('config.tpl_suffix');
        if (substr($path, strlen($path) - strlen($suffix), strlen($suffix)) != $suffix) {
            $path .= $suffix;
        }
        if (!is_file($path)) {
            echo '</br>template load error';
            return false;
        }
        $cache = __ROOT_ . '/app/cache/tpl/' . md5($path) . '.php';
        return self::fetch($path, $cache);
    }

    /**
     * 生成编译文件并返回
     *
     * @author Farmer
     * @param string $path
     * @param string $cache
     * @return
     *
     */
    private function fetch($path, $cache) {
        $fileData = file_get_contents($path);
        if (!file_exists($cache) || filemtime($path) > filemtime($cache)) {
            $pattern = array(
                '/\{(\$[\w\[\]\']+)\}/',
                '/{break}/',
                '/{continue}/',
                '/{if (.*?)}/',
                '/{\/if}/',
                '/{elseif (.*?)}/',
                '/{else}/',
                '/{foreach (.*?)}/',
                '/{\/foreach}/',
                "/{include '(.*?)'}/",
                '/{\:(.*?)}/'
            );
            $replace = array(
                '<?php echo ${1};?>',
                '<?php break;?>',
                '<?php continue;?>',
                '<?php if(${1}):?>',
                '<?php endif;?>',
                '<?php elseif(${1}):?>',
                '<?php else:?>',
                '<?php foreach(${1}):?>',
                '<?php endforeach;?>',
                '<?php view()->display("${1}");?>',
                '<?php echo ${1};?>'
            );
            $cacheData = preg_replace($pattern, $replace, $fileData);
            @file_put_contents($cache, $cacheData);
        } else {
            $cacheData = file_get_contents($cache);
        }
        $pattern = array(
            '/__HOME__/'
        );
        $replace = array(
            __HOME_
        );
        $cacheData = preg_replace($pattern, $replace, $cacheData);
        preg_match_all('/\{\$([a-zA-Z0-9]+)\}/', $fileData, $tmp);
        for ($i = 0; $i < sizeof($tmp [1]); $i++) {
            if (!isset (self::$tplVar [$tmp [1] [$i]])) {
                self::$tplVar [$tmp [1] [$i]] = '';
            }
        }
        ob_start();
        extract(self::$tplVar);
        eval ('?>' . $cacheData);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}