<?php
/**
 * 该Action被同时绑定模块过滤器和操作过滤器，作为测试对象
 */
class TestAction extends ActionUtil implements Filter{
	
	//要使test()添加的过滤器方法有效，其访问控制须设为protected。绑定的过滤器可以有：TestFilter::test(),TestFilter::test_*()
	protected function test($urlInfo){
		echo "过滤器执行完毕，{ 现在进入TestAction : : test( )的方法体 }<br>";
		echo '/readme.txt的字数约有：'.intval(filesize(APPROOT.'readme.txt')/(floatval(13)/3)).'<br>';
		require_once OTHER_TEMPLATE_DIR.'othertest.php';//测试普通模板
		echo '{ TestAction : : test( )的方法体执行结束 }<br>';
	}
	
	//实现系统内置的模块单过滤器 Filter::doFilter()。如果进入TestAction模块前仅需一个过滤器，则仅实现Filter接口即可。
	public function doFilter() {
		echo substr(__CLASS__, 0, -6)."Action模块绑定的内置过滤器执行...<br>";
	}

}
