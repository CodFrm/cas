<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/21
 * blog:blog.icodef.com
 * function:公共函数库
 *============================
 */

function _js($file) {
    return '<script type="text/javascript" src="' . __HOME_ . '/static/js/' . $file . '.js"></script>';
}

function _css($file) {
    return '<link rel="stylesheet" href="' . __HOME_ . '/static/css/' . $file . '.css">';
}

/**
 * 对变量进行验证
 * @author Farmer
 * @param $array
 * @param $mode
 * @return bool
 */
function verify($array, $mode, &$data = '') {
    foreach ($mode as $key => $value) {
        if (is_string($value)) {
            if (empty($array[$key])) {
                return $value;
            }
        } else if (is_array($value)) {
            if (empty($array[$key])) {
                return $value['msg'];
            }
            if (!empty($value['regex'])) {//正则
                if (!preg_match($value['regex'][0], $array[$key])) {
                    return $value['regex'][1];
                }
            }
            if (!empty($value['func'])) {//对函数处理
                $tmpFunction = $value['func'];
                $funName = $value['func'][0];
                $parameter = array();
                unset($tmpFunction[0]);
                $parameter[] = $array[$key];
                foreach ($tmpFunction as $v) {
                    $parameter[] = $array[$v];
                }
                $tmpValue = call_user_func_array($funName, $parameter);
                if ($tmpValue !== true) {
                    return $tmpValue;
                }
            }
            if (!empty($value['enum'])) {//判断枚举类型
                if (!in_array($array[$key], $value['enum'][0])) {
                    return $value['enum'][1];
                }
            }
            if (!empty($value['sql'])) {//将其复制给sql插入数组
                $data[$value['sql']] = $array[$key];
            }
        }
    }
    return true;
}

/**
 * 取中间文本
 * @author Farmer
 * @param $str
 * @param $left
 * @param $right
 * @return bool|string
 */
function getStrMid($str, $left, $right) {
    $lpos = strpos($str, $left);
    if ($lpos === false) {
        return false;
    }
    $rpos = strpos($str, $right, $lpos + strlen($left));
    if ($rpos === false) {
        return false;
    }
    return substr($str, $lpos + strlen($left), $rpos - $lpos - strlen($left));
}

/**
 * 取随机字符串
 * @author Farmer
 * @param $length
 * @param $type
 * @return string
 */
function getRandString($length, $type = 2) {
    $randString = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    $retStr = '';
    $type = 9 + $type * 26;
    for ($n = 0; $n < $length; $n++) {
        $retStr .= substr($randString, mt_rand(0, $type), 1);
    }
    return $retStr;
}

/**
 * 获取/设置配置
 * @author Farmer
 * @param $key
 * @param string $value
 * @return int
 */
function config($key, $value = null) {
    if (!is_null($value)) {
        if (config($key) !== false) {
            return \icf\lib\db::table('config')->where(['key' => $key])->update(['value' => $value]);
        } else {
            return \icf\lib\db::table('config')->insert(['value' => $value, 'key' => $key]);
        }
    } else {
        $rec = \icf\lib\db::table('config')->where(['key' => $key])->find();
        if (!$rec) {
            return false;
        }
        return $rec['value'];
    }
}

/**
 * 取出url中的路径
 * @return bool|string
 */
function getUrlRoot() {
    return substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
}