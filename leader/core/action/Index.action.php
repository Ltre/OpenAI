<?php
class IndexAction extends ActionUtil {
	//默认的Action方法，进入入口后，如果没有输入任何参数，则执行此处。自定义配置见 DEFAULT_URL_SHELL   @   /core/lib/base/env__.php
	public function index($urlInfo){
		//header('Location: ./fkb');
		ActionUtil::action('Help')->help($urlInfo);
		//$this->tpl();
	}
}