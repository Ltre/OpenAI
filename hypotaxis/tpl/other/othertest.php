<?php
//导入任务群集文件
AiCore::includePhpWithEveryLayer('core/setting/task/');

/*
 * If you want to simulate a crontask you must call this script once 
 * and it will keep running forever (during server uptime) in the background 
 * while "doing something" every specified seconds (= $interval): 
 */

$file_oncetask = 'core/setting/oncetask.running';
echo "当前会话id：".session_id().'<br>';
//已经拿到session id，如何根据id终止其它会话（终止后就顺便把单例任务结束掉了）？
//如何停止正在后台执行的脚本
//。。。。。

//如果标志文件存在，则视为已经启动了单例任务；否则，将创建该文件，并启动任务
if( ! file_exists($file_oncetask) ){
	fopen($file_oncetask, 'w');
	ai_exec_once();//启动
}else{
	echo "该任务只允许单例运行";
}


/**
 * 仅需要被启动一次的任务群集
 */
function ai_exec_once(){
	ignore_user_abort(true);	//忽略客户端中断连接
	set_time_limit(0);			//设置脚本执行时间不限
	$interval = 1;				//睡眠周期：1秒
	do {
		foreach (get_class_methods('SingleTasks') as $method){
			eval('SingleTasks::'.$method.'();');
		}
		sleep($interval);
	}while(@$i++ < 10);
}

