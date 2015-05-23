<?php
class HelpAction extends ActionUtil{
	
	public function help($urlInfo){
		self::tpl(null);	//测试Action模板
	}
	
	//测试【TestAction绑定的过滤器】的代码
	//测试链接：http://server.com/?xxx=demo
	public function demo($urlInfo){
		echo "<br>===============当前进入IndexAction模块的demo( )方法，准备测试...===============<br><br>";
		echo "开始测试TestAction : : test( )的过滤器<br>";
		echo "<font color=red>";print_r($urlInfo);echo "</font><br>";
		$this->action('Test')->test($urlInfo);
		$d = new Demo();
		$d -> prop = 'demo';
		echo '$d->prop = '.$d->prop;
		echo "<br>===============当前跳出IndexAction模块的demo( )方法，结束测试...===============<br><br>";
	}
	
	//测试页面AJAX
	public function page($urlInfo){
		self::tpl(null);
	}
	//测试MySQL
	public function mysql($urlInfo){
		$d = new Demo();
		$d -> id = $urlInfo['params'][1];
		$d -> prop = intval( $urlInfo['params'][2] );
		//新增举例
		echo AiMySQL::insert( $d ) ? "插入成功<br>" : "没有插入<br>";
		//修改举例
		$d -> prop = rand(0, 99999);
		echo AiMySQL::update($d) ? "修改成功<br>" : "没有修改<br>";
		//删除举例
		echo AiMySQL::delete($d) ? "删除成功<br>" : "没有删除<br>";
		//实体查询举例
		$c[] = new AiMySQLCondition('id', '=', "123yfds");
		$c[] = new AiMySQLCondition('prop', '=', 60919);
		//select * from demo where id = "123yfds" and prop = 60919 order by id desc limit 2 , 5 
		$rs = AiMySQL::queryEntity('Demo',$c,AiMySQLCombination::COMB_AND,'id',AiMySQLOrderBy::DESC,5,2);
		if(false !== $rs)
			foreach ($rs as $demo) {
				var_dump($demo);echo "<br>";
			}
		//任意查询举例
		$sql = 'select distinct id "主键", count(*) "个数" from fm_demo group by id desc ';
		$rs = AiMySQL::queryCustom($sql);
		foreach ($rs as $r) {
			echo $r['主键'] . ' => ' . $r['个数'] . '<br>';
		}
	}
	
	//测试模块指令
	public function shelltest($urlInfo){
		self::tpl(null);
	}
	
	//查看执行信息
	public function debug_trace(){
		/* foreach(debug_backtrace() as $items){
			foreach($items as $item){
				var_dump($item);
				echo "  ||||";
			}
			print("<br/><br/><br/>");
		} */
		OrekiUtil::var_dump_array(debug_backtrace());
	}
	
	//测试FTP扩展
	public function ftp_test($urlInfo){
		AiFtp::test();
	}
	
}