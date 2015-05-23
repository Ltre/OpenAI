<?php
/**
 * 系统初始化脚本。
 *  =>先解析URL参数
 * 	=>尝试常规调度(Action)
 * 	=>常规调度失败则尝试根据URL参数寻找模板文件并输出
 * 	=>匹配不到模板文件，则按403处理。
 */

header("Content-type: text/html;charset=utf-8");	//本系统固定使用UTF-8编码，使用别的编码将出错。
session_start();	//提供session支持，后续代码中将不用再重复此代码

/*
 * 分析URI 是否合法（需要 URL_REWRITE 支持）
 */
if ( UrlUtil::URIInterceptor ( $_SERVER['REQUEST_URI'] ) ){
	echo $_SERVER['REQUEST_URI'];
	require_once APPROOT.PAGE_403;
	die;
}
/*
 * 分析URL参数
 */
$urlInfo = UrlUtil::analyseUrlParam($_GET);
// var_dump($urlInfo);
$_SESSION['urlInfo'] = $urlInfo;
/*
 * 选择性地执行全局过滤器
 */
FilterUtil::globalFilter($urlInfo);

/*
 * 根据URL信息调度 到 对应的[模块——操作]中
 */
if(! ActionUtil::invokeAction($urlInfo)){
// 	echo ("<hr/>如果你看到这一行提示，则说明没有被调度到ActionUtil : : numOfShellArgs(){}所注册的Action方法。<hr/>");

// 	var_dump($urlInfo);

	/* 这里可再添加其它调度方式的代码【已被常规调度的有：0,1,3,4。剩余的参数状态有：2，5和 [没有注册shell或shell参数个数不符] 的状态3】。
	 * 一般人没必要利用这个地方，你要是读懂了源代码，就任你用！
	* 本框架的设计者对源码很熟悉，所以就把这个分支用作普通模板输出啦！
	* 如果你输入的链接[ http://server.com/ItemName/?xxx=abc ]中的abc不是URL指令，
	* 而且还执行到此处的时候，系统就认定这个abc是用来访问abc.php这个页面的。
	* 本系统用于输出到客户端的页面默认存放于/PUBLIC_DIR_NAME/core/tpl/other/ 目录，要自定义存放位置，则需配置 [ 模板目录路径 OTHER_TEMPLATE_DIR  @  /core/lib/base/env__.php ]。
	* 以上输入的链接将默认访问到 /PUBLIC_DIR_NAME/core/tpl/other/abc.php 。
	* 以上得到的参数'abc'的取法：$urlInfo['params'][0]。取出该参数之前，要确保$urlInfo['params']非null，并且也要确保count($urlInfo['params'])非0，以及$urlInfo['params'][0]非null.
	* 接下来的就是页面[模板]输出的代码：
	*/
	if(null != $urlInfo['params'] && 0 != count($urlInfo['params']) && null != $urlInfo['params'][0]){
		$param = $urlInfo['params'][0];
		if(false === UrlUtil::isUrlShell($param)){
			$param = trim(trim($param), '-/');
			$path = '';
			foreach (explode('-', $param) as $p){
				$p = trim($p,'/ \\');
				if(strcmp('', $p))
					$path .= '/'.$p;
			}
			$path = trim($path, '/');
			/*包含的文件除了可以是abc.php之类的以外，也可以是abc/def/ghi.php之类的。
			 * 只要请求的文件名参数能够被系统识别出a-b-c-d的格式，就可以默认访问到PUBLIC_DIR_NAME/core/tpl/a/b/c/d.php。
			* 这样的文件名参数格式，在本系统中称之为标准格式。其中的“-”将被替换为“/”。
			* 对于【'r_^-fds \\--/gh'】之类的文件名参数，本系统可以将之解析为【r_^/fds/gh】（原参数中，有“-”和“--”，于是结果就有两条“/”。至于原参数中的“\\”和“/”，将被清除）。
			* 这种掺入无关字符（“\”、“/”）的做法，可以起到障眼的作用，使文件路径不易被猜解。
			*/
			if(file_exists(APPROOT.OTHER_TEMPLATE_DIR.$path.'.php')){
				require_once APPROOT.OTHER_TEMPLATE_DIR.$path.'.php';
				exit;
			}
		}
	}
}else{
	exit;
}
// echo "请求的页面不存在，这里可以设计403页面";
require_once APPROOT.PAGE_403;
exit;
