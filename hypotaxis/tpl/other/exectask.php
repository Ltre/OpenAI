<?php
/**
 * 单例后台任务————简易实现
 * @author Oreki
 * @since 2014-3-9
 */

$file_oncetask = 'core/setting/oncetask.running';
//如果标志文件存在，则视为已经启动了单例任务；否则，将创建该文件，并启动任务
if( ! file_exists($file_oncetask) ){
	fopen($file_oncetask, 'w');
	ai_exec_once( 10 );//启动，并执行10秒
}else{
	echo "该任务只允许单例运行";
}

/**
 * 任务启动器（仅启动一次）
 * @param $duration 持续时间（秒）
 */
function ai_exec_once( $duration ){
	ignore_user_abort(true);	//忽略客户端中断连接
	set_time_limit(0);			//设置脚本持续执行时间不限
	$interval = 1;				//睡眠周期：1秒
	do {
		foreach (get_class_methods('SingleTasks') as $method){
			eval('SingleTasks::'.$method.'();');
		}
		sleep($interval);
	}while(@$i++ < $duration);
}

/**
 * 单例任务群集
 * 该类中所有可见方法都被视为单例任务
 * 方法名不限制格式
 */
class SingleTasks {
	/*
	 * 任务1
	 */
	static function printtime(){
		static $i = 0;
		echo @date(
				'H : i : s',
				$i++==0 ? $_SERVER['REQUEST_TIME'] : time()
		);
		echo "<br>";
	}
	/*
	 * 任务2：每秒钟创建一个新文件
	 */
	static function createfile(){
		static $i = 0;
		$file_oncetask = 'core/setting/oncetask.running';
		fopen( $file_oncetask . $i++, 'w' );
	}
}