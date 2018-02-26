<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:路由类
 *============================
 */

namespace lib;


use icf\index;
use icf\lib\log;

class route {
    static $module = '';//模块
    static $ctrl = '';//控制器
    static $action = '';//操作

    static $rule = [
        '*' => [
            '{s}.php' => '${1}->index->index',
            '{s}/{s}/{s}#p' => '${1}->${2}->${3}',
            '{s}/{s}#p' => '${1}->${2}',
            '{s}#p' => '${1}->index'
        ], 'get' => [

        ]];

    static $get_param = [];

    static $replace_param = '';

    static $classNamePace = '';

    static $get = [];

    static $matchUrl = '';

    static $req_method = 'get';

    static function matchRule($match, $pathInfo) {
        //初始化
        self::$module = '';
        self::$ctrl = '';
        self::$action = '';
        //处理各个参数
        $param = '';
        if ($cut = strpos($match[0], '#')) {
            $param = substr($match[0], $cut + 1);
            $match[0] = substr($match[0], 0, $cut);
            if (strpos($param, 'p') !== false) {
                $match[0] .= '/';
                $pathInfo .= '/';
            }
        }
        $var = preg_replace_callback('#{(.*?)}#', function ($v) {
            static $count = 0;
            $count++;
            self::$get_param[$count] = $v[1];
            return '([\S][^\{^\}^/]*)';
        }, $match[0]);
        self::$replace_param = $match[1];
        self::$get = [];
        $count = 0;
        preg_replace_callback('#^\/' . $var . '#', function ($v) {
            foreach ($v as $key => $value) {
                self::$replace_param = str_replace('${' . $key . '}', $value, self::$replace_param);
                if (isset(self::$get_param[$key])) {
                    self::$get[self::$get_param[$key]] = $value;
                }
            }
            self::$matchUrl = $v[0];
            return '';
        }, $pathInfo, 1, $count);
        if ($count <= 0) {
            return false;
        }
        $mca = explode('->', self::$replace_param, 3);
        if (sizeof($mca) <= 2) {
            $mca[2] = $mca[isset($mca[1])];
            $mca[1] = isset($mca[1]) ? $mca[0] : 'index';
            $mca[0] = __DEFAULT_MODULE_;
        }
        self::$module = _get(_config('module_key'), $mca[0]);
        self::$ctrl = _get(_config('ctrl_key'), $mca[1]);
        self::$action = _get(_config('action_key'), $mca[2]);
        self::$classNamePace = 'app\\' . self::$module . '\\ctrl\\' . self::$ctrl;
        $className = str_replace('\\', '/', self::$classNamePace);
        if (!is_file($className . '.php')) {
            return false;
        }
        $tmpParam = '';
        if (self::$matchUrl) {
            $tmpParam = substr($pathInfo, strpos($pathInfo, self::$matchUrl) + strlen(self::$matchUrl));
        }
        if (strpos($param, 'p') !== false) {
            //处理后方参数
            preg_match_all('#/([\S][^\{^\}^/]*)/([\S][^\{^\}^/]*)#', '/' . $tmpParam, $matchArr, PREG_SET_ORDER);
            foreach ($matchArr as $item) {
                self::$get[$item[1]] = $item[2];
            }
        } else {
            if ($tmpParam != '') {
                return false;
            }
        }
        return true;
    }

    /**
     * 解析URL,加载控制类
     * @access public
     * @author Farmer
     */
    static function analyze() {
        self::$req_method = strtolower($_SERVER['REQUEST_METHOD']);
        if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
            $pathInfo = $_SERVER['PATH_INFO'];
            if (isset(self::$rule[self::$req_method])) {
                $tmpRule = self::$rule[self::$req_method];
                foreach ($tmpRule as $key => $value) {
                    //匹配规则
                    if (self::matchRule([$key, $value], $pathInfo)) {
                        if (self::runAction()) {
                            return;
                        }
                    }
                }
            }
            $tmpRule = self::$rule['*'];
            foreach ($tmpRule as $key => $value) {
                //匹配规则
                if (self::matchRule([$key, $value], $pathInfo)) {
                    if (self::runAction()) {
                        return;
                    }
                }
            }
            _404();
            return;
        } else {
            self::$module = _get(_config('module_key'), __DEFAULT_MODULE_);
            self::$ctrl = _get(_config('ctrl_key'), 'index');
            self::$action = _get(_config('action_key'), 'index');
            self::$classNamePace = 'app\\' . self::$module . '\\ctrl\\' . self::$ctrl;
            $className = str_replace('\\', '/', self::$classNamePace);
            if (!is_file($className . '.php')) {
                _404();
                return false;
            }
            self::runAction();
            return;
        }
    }


    static function runAction() {
        input('module', route::$module);
        input('ctrl', route::$ctrl);
        input('action', route::$action);
        //加载全局函数
        $comPath = __ROOT_ . '/app/common.php';
        if (file_exists($comPath)) {
            require_once $comPath;
        }
        //加载模块函数
        $comPath = __ROOT_ . '/app/' . self::$module . '/';
        if (file_exists($comPath . 'common.php')) {
            require_once $comPath . 'common.php';
        }
        try {
            input('get', $_GET);
            if (sizeof($_POST) <= 0) {
                parse_str(file_get_contents('php://input'), $_POST);
            }
            input('post', $_POST);
            $tmp = self::$classNamePace;
            $object = new $tmp();
            if (input('config.rest')) {
                $tmpMethod = self::$req_method . self::$action;
                if (method_exists($object, $tmpMethod)) {
                    self::$action = $tmpMethod;
                }
            }
            // 获取方法参数
            $method = new \ReflectionMethod ($object, self::$action);
            // 参数绑定
            $param = [];
            $_GET = array_merge($_GET, self::$get);
            foreach ($method->getParameters() as $value) {
                if ($val = _get($value->getName())) {
                    $param [] = $val;
                } else {
                    $param [] = $value->isDefaultValueAvailable() ? $value->getDefaultValue() : '';
                }
            }
            $data = call_user_func_array([
                $object,
                self::$action
            ], $param);
            if (is_array($data)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json($data);
            } else {
                echo $data;
            }
        } catch (\Exception $e) {
            if (input('config.debug')) {
                if (input('config.log')) {
                    index::$log->error('[file] ' . $e->getFile() . ' [line] ' . $e->getLine() . ' [error] ' . $e->getMessage());
                }
                echo "file:" . $e->getFile() . "<br/>\n";
                echo "line:" . $e->getLine() . "<br/>\n";
                echo "error:" . $e->getMessage() . "<br/>\n";
                var_dump($e->getTrace());
            } else {
                _404();
            }
        }
        return true;
    }

    /**
     * 添加规则
     * @access public
     * @author Farmer
     * @param $req_type
     * @param $rule
     * @param string $to
     */
    static function add($req_type, $rule, $to = '') {
        if (is_array($rule)) {
            foreach ($rule as $pattern => $value) {
                self::$rule [$req_type][$pattern] = $value;
            }
        } else {
            self::$rule [$req_type][$rule] = $to;
        }
    }
}