<?php
/**
 * TestAction 对应的 过滤器群组
 */
class TestFilter {

	//以下的doFilter_1(),doFilter_2()构成了进入Action模块前的过滤器链，如果定义了，没必要让XxxxAction实现内置的模块单过滤器接口Filter了。
	public function doFilter_1(){
		echo substr(__CLASS__, 0, -6).'Action模块过滤器链第一环执行...<br>';
	}
	public function doFilter_2(){
		echo substr(__CLASS__, 0, -6).'Action模块过滤器链第二环执行...<br>';
	}
	
	//如果TestAction::test()仅需一个过滤器，则仅定义该方法即可。
	public function test(){
		echo substr(__CLASS__, 0, -6).'Action : : test( )方法的单过滤器执行...<br>';
	}
	//以下的test_1(),test_2()构成Action操作的过滤器链，如果定义了，就没必要定义test()了。
	public function test_1(){
		echo substr(__CLASS__, 0, -6).'Action : : test( )方法过滤器链的第一环执行...<br>';
	}
	public function test_2(){
		echo substr(__CLASS__, 0, -6).'Action : : test( )方法过滤器链的第二环执行...<br>';
	}

}