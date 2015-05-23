<?php

/**
 * 数据库连接配置
 */
$hhost = $_SERVER['HTTP_HOST'];
if (in_array($hhost, array(
	'localhost',
    '127.0.0.1',
))) {
    
    class AiDBConfiguration{
        //数据库类型【暂未实现其它库】
        static $dbtype = 'mysql5';
        //数据库主机名或地址
        static $host = '127.0.0.1';
        //端口
        static $port = 3306;
        //数据库名
        static $db = 'fleamarket';
        //用户
        static $user = 'fleamarket.y1525';
        //密码
        static $pwd = 'aiyowocao';
        /*
         * 数据表前缀。
        * 例如前缀为"fm_"，设置该前缀后，fm_xxxx表将与名为Xxxx的实体对应。
        * 如果数据表不需要前缀，则设置为null或空串即可。
        */
        static $table_prefix = 'fm_';
    }
    
} elseif (in_array($hhost, array(
	'openai.sinaapp.com',
))) {
    
    class AiDBConfiguration{
        //数据库类型【暂未实现其它库】
        static $dbtype = 'mysql5';
        //数据库主机名或地址
        static $host = SAE_MYSQL_HOST_M;
        //端口
        static $port = SAE_MYSQL_PORT;
        //数据库名
        static $db = SAE_MYSQL_DB;
        //用户
        static $user = SAE_MYSQL_USER;
        //密码
        static $pwd = SAE_MYSQL_PASS;
        /*
         * 数据表前缀。
        * 例如前缀为"fm_"，设置该前缀后，fm_xxxx表将与名为Xxxx的实体对应。
        * 如果数据表不需要前缀，则设置为null或空串即可。
        */
        static $table_prefix = '';
    }
    
} else {
    
    die;//环境不明确，终止
    
}
