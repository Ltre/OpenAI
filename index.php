<?php
/**
 * 项目默认入口
 */
//设置默认时区
date_default_timezone_set('PRC');
//开启输出缓冲（租用的服务器需要）
ob_start();
//开启PHP错误提示
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
/*
 * 所在公共目录的名称，可自行定义，默认放置于leader。如果位于项目根目录，则填入''。
*/
define('PUBLIC_DIR_NAME', 'leader');


require_once 'path__.php';	//路径纠错支持
require_once PUBLIC_DIR_NAME.'/core/lib/base/__include__.php';	//一次性包含常用库和所有Action
/*不要在后面添加代码了，绝对执行不到的！*/
