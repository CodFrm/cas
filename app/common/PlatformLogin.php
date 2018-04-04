<?php
/**
 *============================
 * author:Farmer
 * time:2018/4/4
 * blog:blog.icodef.com
 * function:登录接口
 *============================
 */


namespace app\common;


interface PlatformLogin {
    public function Login($u,$p);
}