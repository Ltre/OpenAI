<?php
/**
 * 单例任务群集
 * 该类中所有可见方法都被视为单例任务
 * 方法名不限制格式
 */
class SingleTasks {
	static function printtime(){
		static $i = 0;
		echo @date(
			'H : i : s',
			$i++==0 ? $_SERVER['REQUEST_TIME'] : time()
		);
		echo "<br>";
	}
	static function createfile(){
		static $i = 0;
		$file_oncetask = 'core/setting/oncetask.running';
		fopen($file_oncetask.$i++, 'w');
	}
}