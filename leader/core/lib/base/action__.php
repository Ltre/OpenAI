<?php
/*
 * 这里配置所有Action的方法所对应的参数，以及配置如何调度Actions
 * 可以手动配置的位置：
 * 		1、numOfShellArgs(){}
 * 		2、self::$status_not_allowed
 */
class ActionUtil {
	//不被调度的URL参数状态，不建议修改此处。
	private static $status_not_allowed = array(2, 5);
	//进行模板输出前需要设定的参数，一般是数组形式，可供两种模板（Action/Other）使用。
	protected static $tpl_args = null;
	//调用XxxxAction的私有方法之前是否已经调用了__call()方法
	protected static $hasCalledTheMagic__callBeforeCallPrivateFunc = false;
	/**
	 * 返回所注册URL SHELL对应的Action方法的参数个数【方法名=>参数个数】
	 * 如果URL提供的后续参数个数     符合   所注册SHELL所需的参数个数，则会调用这里的Action方法
	 * 注意：即时可以分Action定义方法，
	 * 		也不要定义不属于同Action却同名的方法(否则出错)。
	 * 	 URL SHELL指：Action中可以有URL参数直接调用的方法名。
	 */
	public static function numOfShellArgs() {
		return AiUrlShell::$shellArgs;
	}
	/**
	 * 输出Action默认模板的方法
	 * Action模板默认位置：/PUBLIC_DIR_NAME/core/tpl/default/。如需改变，见 ACTION_TEMPLATE_DIR @ /core/lib/base/env__.php
	 * 在调用该方法之前，一般要先为self::$tpl_args赋值，如果没什么参数可传的，就不用填写参数。
	 * 如果找不到相应的模板文件，则不会输出任何内容，但会返回false作为标志。
	 * 输出成功则返回true。
	 */
	public static function tpl( $tpl_args = null ) {
		//获取上次调用者的Action名和function名
		$backtrace = debug_backtrace();
		$action = substr($backtrace[1]['class'], 0, -6);
		$func = $backtrace[1]['function'];
		self::setTplArgs($tpl_args);
		//拼接对应的模板文件路径
		$tpl = APPROOT.ACTION_TEMPLATE_DIR.$action.'/'.$func.'.php';
		if(file_exists($tpl)){
			require_once $tpl;
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 设置模板文件参数，要在输出模板文件之前执行。
	 */
	public static function setTplArgs($tpl_args){
		self::$tpl_args = $tpl_args;
	}
	/**
	 * 获取默认模板的参数，一般在模板文件中首先获取，再被用于后续脚本。
	 */
	public static function getTplArgs(){
		return self::$tpl_args;
	}
	/**
	 * 根据URL指令对应的方法名定位到所在的Action
	 * 参数：
	 * 		1、URL指令，如 test;
	 * 		2、ActionUtil::numOfShellArgs()的返回值
	 * 返回：所属Action的名称，如 Test。查无结果，则返回null
	 */
	public static function getActionNameWhereFuncIn($shell, $shellConf){
		$action = null;
		foreach ( $shellConf as $index=>$value ){
			foreach ( $value as $i=>$v ){
				if(0 == strcasecmp($shell, $i)){
					$action = $index;
					break;
				}
			}
		}
		return $action;
	}
	
	/**
	 * 判断Action名称（模块名称）是否在AiUrlShell类中注册过
	 * 必须以大写开头，后续小写，否则会造成无法匹配的情况
	 * @param string $actionName Action名称，例如：Index、Test
	 * @return boolean true 存在  false 不存在
	 */
	public static function isActionExist( $actionName ){
		foreach (AiUrlShell::$shellArgs as $key => $value) {
			if(0==strcmp($actionName, $key))
				return true;
		}
		return false;
// 		return in_array($actionName, AiUrlShell::$shellArgs);
	}
	
	/*
	 * 根据UrlUtil::analyseUrlParam()得到的URL参数信息
	* 计算  如果进入调度后，可用的参数  个数。
	* 可用的参数是：去除指令参数后，剩余的参数
	* 返回：
	* 		如果能进入调度状态，则返回个数（0,1,2...）；
	* 		否则，返回 -1。
	*/
	private static function getNumOfAvailableArgs($urlInfo){
		if(! in_array($urlInfo['status'], self::$status_not_allowed)){
			if(0==$urlInfo['status'] && 0==count($urlInfo['params']))
				return 0;	//没有输入任何参数，形如http://server.com/项目名
			return count($urlInfo['params']) - 1;
		}
		return -1;
	}
	/**
	 * TODO: 该方法将废弃
	 * 自动检测开发者在 FILTER_DIR 目录自行添加的XxxxFilter.class.php，并包含之。
	 */
// 	public static function parseXxxFilterFromFilterDirAndAutoIncludeTheir(){
// 		foreach (glob(APPROOT.FILTER_DIR.'{A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z}*Filter.class.php', GLOB_BRACE) as $filterFile){
// 			require_once $filterFile;
// 		}
// 	}
	/**
	 * TODO: 该方法将废弃
	 * 自动检测开发者在 ACTION_DIR 目录自行添加的XxxxAction.class.php，并包含之。
	 */
// 	public static function parseXxxActionFromActionDirAndAutoIncludeTheir(){
// 		foreach (glob(APPROOT.ACTION_DIR.'{A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z}*Action.class.php', GLOB_BRACE) as $actionFile){
// 			require_once $actionFile;
// 		}
// 	}
	/**
	 * TODO : 该方法将废除。
	 * 自动检测开发者在  指定的  目录自行添加的库文件或目录（或者其它php常量配置文件），并包含其内任何层次目录的[*.php]。
	 * 参数要求：格式如：$path = APPROOT.PUBLIC_DIR_NAME.'/core/lib/'
	 * 			必须从项目根目录开始指定
	 * 			不能以“/”开头
	 * 			要以“/”结尾
	 * 参数$layer是层次，以输入路径为第0层，每递归一次便自增，用于测试，可以查看递归了多少次。
	 */
// 	public static function parseUserLibraryFromLibDirAndAutoIncludeTheir($path, $layer=0){
// 		//echo "=====进入第($layer)层=====<br>";
// 		if( is_dir($path) && ($dh=opendir($path)) ) {
// 			while(false !== ($file=readdir($dh))){
// 				if(in_array($file, array('.','..')))
// 					continue;
// 				if(is_dir($path.$file)){
// 					//echo "第($layer)层：目录 - ".$path.$file."/<br>";
// 					self::parseUserLibraryFromLibDirAndAutoIncludeTheir($path.$file.'/', $layer+1);
// 				}else{
// 					//echo "第($layer)层：文件 - ".$path.$file."<br>";
// 					if(0!==preg_match('/\.php$/', $file))
// 						require_once $path.$file;
// 				}
// 			}
// 			closedir($dh);
// 			//echo "=====跳出第($layer)层=====<br>";
// 		}
// 	}
	
	/**
	 * 监听对XxxxAction中不可见方法的调用
	 * 可以将需要附加过滤器的Action方法设置为protected，
	 * 这样就可以在任何位置调用这些Action方法时先执行过滤器。
	 */
	public function __call($method, $vars){
		//先判断要执行的Action方法是否存在
		if(! in_array($method, get_class_methods($this))){
			return;
		}
		//执行Action绑定的过滤器，如果有。
		FilterUtil::doFilterIfHasIt(substr(get_class($this), 0, -6), $method);
		
		//获取上级调用信息
		$backtrace = debug_backtrace();
		if(0 == strcasecmp($backtrace[1]['function'], 'eval')){
		}else if(! in_array($method, get_class_methods($backtrace[1]['class']))){
			//echo "在".$backtrace[1]['class']."中，不存在$method()方法！<br>";
			return;
		}
		//构造参数表的字符串代码
		$args = array();
		$args_word = '';
		$i = 0;
		foreach ($vars as $var){
			$args[] = $var;
			$args_word .= '$args['.$i.'],';
			$i ++;
		}
		$args_word = rtrim($args_word, ',');
		$execute = '$this->'.$method.'('.$args_word.');';
		eval($execute);
	}
	
	/**
	 * 实例化一个Action模块
	 * 参数：Action名称，如'Index','Test'
	 * 返回：XxxxAction示例
	 */
	public static function action($action){
		$execute = '$a = new '.$action.'Action();';
		eval($execute);
		return $a;
	}
		
	/**
	 * 根据UrlUtil::analyseUrlParam()得到的URL参数信息,调度到相应的控制器
	 * 不对 不含指令(URL SHELL)的状态(值2/5)进行调度。
	 * 参数要求：
	 * array (
	 * 		'status'=>	0~5 ,	//参数状态
	 * 		'shell'		=>	'URL命令/null',
	 * 		'params'	=>	array (被“|”分隔的参数集合) ,
	 * 		'ids'	=>	array(被“-”分割的id数组，不以“零”项开头，且不包含“零”项)
	 * )
	 * 返回：
	 * 		如果被调度到numOfShellArgs()所注册的action，则返回true；
	 * 		否则返回false
	 */
	public static function invokeAction($urlInfo){
		//过滤掉不符合 self::$status_not_allowed指定的状态
		if(-1 == self::getNumOfAvailableArgs($urlInfo))
			return false;
		$status = $urlInfo['status'];
		$shell = $urlInfo['shell'];
		//不调度
		if(in_array($status, self::$status_not_allowed))
			return false;
		//取 URL SHELL 的参数个数表
		$argNum = self::numOfShellArgs();
		//没输入URL SHELL（即http://server.com/项目名），则调入默认的SHELL【特殊调度，状态0】
		if(0 == $urlInfo['status']){
			$action_shell = explode('-', DEFAULT_URL_SHELL);
			if(self::getNumOfAvailableArgs($urlInfo) != $argNum[$action_shell[0]][$action_shell[1]])
				return false;
// 			$execute = $action_shell[0].'Action::'.$action_shell[1].'($urlInfo);';
// 			$execute = '$a=new '.$action_shell[0].'Action();$a->'.$action_shell[1].'($urlInfo);';
			$execute = '$a=new '.$action_shell[0].'Action();$a->__call("'.$action_shell[1].'",array($urlInfo));';
			eval($execute);
			return true;
		}
		//根据URL指令找到所属的Action
		$action_name = self::getActionNameWhereFuncIn($shell, $argNum);
		//可用参数与shell方法所需参数个数不相等时，不允许调度
		if(self::getNumOfAvailableArgs($urlInfo) != $argNum[$action_name][$shell])
			return false;
		//以上关卡通过后，才会发生常规调度【1/3/4状态】
// 		$execute = $action_name.'Action::'.$shell.'($urlInfo);';
// 		$execute = '$a=new '.$action_name.'Action();$a->'.$shell.'($urlInfo);';
		$execute = '$a=new '.$action_name.'Action();$a->__call("'.$shell.'",array($urlInfo));';
		eval($execute);
		return true;
	}
	
}