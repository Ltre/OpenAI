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
//本成员从属的公共目录的名称，可自行定义，默认放置于../leader。如果需要定义公共目录位置为上层目录，则改为“..”
define('PUBLIC_DIR_NAME', '../leader');


require_once 'path__.php';	//路径纠错支持
require_once APPROOT.'lib/base/__include__.php';	//一次性包含常用库和所有Action
/*
 * 以上两行代码已经完成了框架的初始化，后面也不用添加什么代码了。
 * 要写业务处理，就定义XxxxAction；
 * 要显示页面，使用模板输出。
 */
?>