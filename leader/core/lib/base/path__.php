<?php
/**
 * 路径纠错支持
 * 如果当前目录存在多个需要作为请求入口的脚本，则包含当前这个脚本[path__.php]可以节省多余的代码。
 * 如果当前目录仅需一个入口，则直接在该入口文件第一句写【defined('APPROOT') or define('APPROOT','../');】，而这时这个path__.php就可以删除掉了。
 * 一般不建议删除path__.php，因为该脚本的存在    可以作为    判断某目录是否有入口文件    的标准。【__include__.php所在的目录除外】
 */

defined('APPROOT') or define('APPROOT','../../../../');	//添加路径纠错支持