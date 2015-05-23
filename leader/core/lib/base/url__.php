<?php
/*
 * 这里用于配置URL静态化、URL重写。
 * 从URL中分析参数信息等等
 */
class UrlUtil {

	/**
	 * URL重写
	 * 暂时用不到。
	 * 使用方法：
	 * 		在$_REQUEST之前调用。例如：
	 * 		urlRewriter();
	 * 		var_dump($_REQUEST['a']);
	 * 		var_dump($_REQUEST['b']);
	 */
	public static function urlRewriter(){
		$self_url = $_SERVER['REQUEST_URI'];
		$request_str = strstr($self_url,'.php');
		$request_str = @ereg('\.(html|htm)$', $request_str);
		$request_arr = explode('/', $request_str);
		array_shift($request_arr);	//移除以“/”分割的第一项
		for( $i = 0 ; $i < count($request_arr) ; $i ++){
			if( ($i%2==0 or $i==0)and ''==$request_arr[$i+1] )
				$_REQUEST[$request_arr[$i]] = $request_arr[$i+1];
		}
	}
	
	/**
	 * 拦截对非入口文件的访问，这需要服务器支持URL重写
	 * 成功拦截非法访问，则返回true。
	 * 没有需要拦截的请求，则返回false。
	 * 如果没开启URL重写，返回false。
	 */
	public static function URIInterceptor($uri){
		if( ! ENABLE_URL_REWRITE)
			return false;
		if(false !== strpos($uri, '?')){
			$uri = substr($uri, 0, strpos($uri, '?'));
		}
		$file = $_SERVER['DOCUMENT_ROOT'].ltrim( $uri , '/');
		if( ! file_exists($file))	//如果访问脚本不存在，则拦截
			return true;
		/* 若访问脚本存在，则判断是否是入口文件
		 * [经测试，如果访问的脚本存在，就会直接访问脚本，这种情况完全不可拦截，必须使用.htaccess来完成拦截。
		 * 在这种情况下：上面的拦截代码有用，下面的拦截代码完全没有机会执行。]
		 */
		foreach (explode('|', DEFAULT_ENTER_SCRIPT) as $f){
			$first_backtrace = array_pop(debug_backtrace());
			if (filesize(APPROOT.$f) == filesize($first_backtrace['file']))
				return false;
		}
		return true;
	}
	/**
	 * 判断是不是URL指令
	 * 参数：URL参数经“|”分割后的第一个字符串
	 */
	public static function isUrlShell(&$param){
		$shells = array();
		foreach (ActionUtil::numOfShellArgs() as $action=>$funcs){
			foreach ($funcs as $func=>$argsNum){
				$shells[] = $func;
			}
		}
		if(! in_array($param, $shells)){
			$flag = 0;
			//修正有误的指令大小写，如果存在这样的情况
			foreach ($shells as $shell){
				if(0 == strcasecmp($param, $shell)){
					$param = $shell;
					$flag = 1;
				}
			}
			//指令确实不存在
			if( ! $flag)
				return false;
		}
		return true;
// 		return in_array($param, $shells);
	}
	
	/**
	 * 判断是否符合id串的形式【x】或【x-x】或【x-x-...-x】，X是任意位数
	 * 返回：id数组，如果符合；null，如果不符合。
	 */
	public static function isIdsFormat($str){
		if(! preg_match ( '/^\d[(-\d|\d)]*\d$|^\d$/', $str))
			return null;
		$ids = explode('-', $str);
		foreach ($ids as $id){
			if($id == null){
				return null;
			}
		}
		return $ids;
	}
	/**
	 * 是id数组每一项整型化
	 * 返回，整型化数组
	 */
	public static function intval_id_array($id_array){
		foreach ($id_array as $i=>$id){
			$id_array[$i] = $id = intval($id);
		}
		return $id_array;
	}
	/**
	 * 终极修正id数组：
	 * 如果第一项是0，则删除该项
	 * 判断后续是否存在“零”项。
	 * 参数：不含null项的id数组
	 * 返回情况：
	 * 	1、原数组（如果没有一项是零，或者长度为一的数组）
	 * 	2、去除第一项的数组（如果第一项是零，且长度大于一）
	 *  3、null（如果除了第一项以外，还存在“零”项；数组长度小于等于0时）
	 */
	public static function hasZeroInFollowUpOfIds($id_array){
		$len = count($id_array);
		$id_array = self::intval_id_array($id_array);//先整型化id数组
		if($len == 1){
			return $id_array;
		}else if($len > 1){
			foreach ($id_array as $i=>$id){
				if($id==0){
					if($i!=0)
						return null;
				}
			}
			if($id_array[0] == 0){
				return array_slice($id_array, 1);
			}
			else
				return $id_array;
		}
	}
	
	/**
	 * 简要：从URL中提取指令、后续参数，返回格式化的URL数组。
	 * 合法URL格式：
	 * 		[指令] | [ 参数(集) = { [参数1|参数2|...|参数n-1] | [id串={id1-id2-...-idn}] } ]
	 * 		指令和后续参数都视为URL的参数，用“|”分隔，如果尾部还有id串，则id串要用“-”分隔。
	 * 		如果对后续参数有特别要求，则需要修改此方法。
	 * 接收参数：$_REQUEST
	 * 返回数组：
	 * 		array (
	 * 			'status'=>	0~5 ,	//URL参数状态
	 * 			'shell'		=>	'URL命令/null',
	 * 			'params'	=>	array (
	 * 								被“|”分隔的参数集合
	 * 							),
	 * 			'ids'	=>	array(被“-”分割的id数组，不以“零”项开头，且不包含“零”项)
	 * );
	 * URL参数状态：
	 * 0、空（长度0）
	 * 1、只有指令（长度1）
	 * 2、只有id串（长度1）
	 * 3、全满含id尾：有指令有作为id串的尾部
	 * 4、全满无id尾：有指令没有作为id串的尾部。也包括如“dirdir|fdsfdsfd|real|1-0-1”，最后一个参数可以认为已经不是id串了。
	 * 5、非法：
	 * 		长度为1时，非指令非id串；
	 * 		长度>1时，第一项非指令;
	 * 		长度等于1时id串去除第一个后还含有“零”项；
	 * 		“xxx|xxx|xxx|xxx”URL含有空串参数，即是否含有“||”或“空串|”或“|空串”；
	 */
	public static function analyseUrlParam ($request) {
		$status = -1;
		$shell = null;
		$params = array();
		$id_array = null;
		// 接收任意参数(仅尾部参数有效)
		foreach ( $request as $param ) break;
		
		if(isset($param))
			$params = explode('|',$param);
		//无参数
		if (! count ( $request ) ) {
			return array('status'=>0,'shell'=>null,'params'=>$params,'ids'=>null);
		}
		// 检测是否含有空串参数，即是否含有“||”或“空串|”或“|空串”。【这也包括“?param=”，属于发送了参数，但是是空的】
		foreach ( $params as $pi ) {
			if ($pi == '' && $status != 0) {
				return array('status'=>5, 'shell'=>null, 'params'=>null, 'ids'=>null);
			}
		}
		//以“|”分隔的参数个数
		$len = count($params);
		if($len===1){
// 						echo "长度是1<br>";
// 						echo "最后参数=".$params[$len-1]."<br>";
			$ids = self::isIdsFormat($params[$len-1]);
			if(null != $ids){
// 								echo "符合x或x-x形式<br>";
				$id_array = self::hasZeroInFollowUpOfIds($ids);
				if(null != $id_array){
// 										echo "id不含除了第一项以外的其它0项<br>";
					$status = 2;
				}else{
// 										echo "id除了第一项以外，还含有其它0项<br>";
					$status = 5;
				}
			}else{
// 								echo "不  符合x或x-x形式<br>";
				if(self::isUrlShell($params[0])){
// 										echo "单指令<br>";
					$shell = $params[0];
					$status = 1;
				}else{
// 										echo "非指令<br>";
					$status = 5;
				}
			}
		}else if($len > 1){
// 						echo "长度大于1<br>";
// 						echo "最后参数=".$params[$len-1]."<br>";
			if(self::isUrlShell($params[0])){
// 								echo "有指令<br>";
				$shell = $params[0];
				$ids = self::isIdsFormat($params[$len-1]);
				if(null != $ids){
// 										echo "末尾参数 符合x或x-x形式<br>";
					$id_array = self::hasZeroInFollowUpOfIds($ids);
					if(null != $id_array){
// 												echo "id不含除了第一项以外的其它0项<br>";
						$status = 3;
					}else{
// 												echo "id除了第一项以外，还含有其它0项<br>";
						$status = 4;
					}
				}else{
// 										echo "末尾参数 不  符合x或x-x形式<br>";
					$status = 4;
				}
			}else{
// 								echo "非指令";
				$status = 5;
			}
		}else{
			//可能没必要加此分支
			return array('status'=>0,'shell'=>null,'params'=>null,'ids'=>null);
		}
		return array(
				'status'	=>		$status,
				'shell'		=>		$shell,
				'params'	=>		$params,
				'ids'		=>		$id_array
		);
	}
}