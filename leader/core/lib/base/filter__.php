<?php
class FilterUtil {
	/**
	 * 在XxxxAction模块或方法调用之前，执行绑定的过滤器（如果有）
	 * 参数：Action名称，如'Test'；方法名，如'test'.
	 * 该方法仅供ActionUtil::invokeAction()、XxxxAction__call()、ActionUtil::__call()调用，以实现调用前先过滤的功能。
	 */
	public static function doFilterIfHasIt($action, $method){
		//获取XxxxAction通过实现Filter而绑定的所有过滤器方法
		$filters = get_class_methods($action.'Action');
		if(! $filters)
			return;
		//第一步、执行内置过滤器方法(绑定在XxxxAction模块的默认过滤器)，如果有
		if($filters && in_array('doFilter', $filters))
			ActionUtil::action($action)->doFilter();
		//获取XxxxAction通过定义XxxxFilter而绑定的所有过滤器方法
		$filters = get_class_methods($action.'Filter');
		if(! $filters)
			return;
		//第二步、再执行绑定在Action模块的后续过滤器链，如果有
		$i = 0;
		while( $filters && ++$i<=count($filters) && in_array('doFilter_'.$i, $filters) )
			eval('FilterUtil::filter($action)->doFilter_'.$i.'();');
		//第三步、执行Action方法绑定的单过滤器（与方法名同名），如果有
		if( in_array($method, $filters) )
			FilterUtil::filter($action)->$method();
		//第四步、再执行绑定在Action方法的后续过滤器链，如果有
		$i = 0;
		while( $filters && ++$i<=count($filters) && in_array($method.'_'.$i, $filters) )
			eval('FilterUtil::filter($action)->'.$method.'_'.$i.'();');
	}
	/**
	 * 【该方法暂时用不到】
	 * 获取类实现的接口方法
	 * 参数：类名
	 * 返回方法名数组
	 */
	public static function getInterfaceMethodsIfHasTheir($action){
		$m = array();
		$r = new ReflectionClass($action);
		foreach ($r->getInterfaceNames() as $interface){
			foreach (get_class_methods($interface) as $method){
				if(!in_array($method, $m))
					array_push($m, $method);
			}
		}
		return $m;
	}
	/**
	 * 获得一个过滤器的实例
	 * 参数：过滤器名，如取TestFilter中的'Test'
	 * 返回XxxxFilter实例
	 */
	public static function filter($filter){
		$execute = '$f = new '.$filter.'Filter();';
		eval($execute);
		return $f;
	}
	/*
	 * self::globalFilter()内部调用的函数
	 * 填充需要和不需要全局过滤器的容器
	 * 解决其代码复用问题
	 * list_need_filter container
	 * list_no_filter container
	 * self::globalFilter_inner_tool($aneg,$sneg,$snog)
	 * self::globalFilter_inner_tool($anog,$snog,$sneg)
	 */
	private static function globalFilter_inner_tool($arule,$srule,$de_srule){
		$container = array();
		foreach ($arule as $action){
			if( ! ActionUtil::isActionExist($action))
				continue;//过滤掉不合法的Action名
			foreach (AiUrlShell::$shellArgs[$action] as $key => $value){
				$container[] = $key;
			}
		}
		foreach ($srule as $s){
			$container[] = $s;
		}
		foreach ($de_srule as $s){
			foreach(array_keys($container, $s) as $key){
				unset($container[$key]);
			}
		}
		return $container;
	}
	
	/**
	 * 根据/PUBLIC_DIR_NAME/core/config/filtermap.php的规则，决定是否执行全局过滤器
	 * 可在解析URL参数之后、进入任何模块之前执行过滤
	 * 全局过滤器的名称配置详见：GLOBAL_FILTER_NAME @ /PUBLIC_DIR_NAME/core/lib/base/env__.php
	 */
	public static function globalFilter($urlInfo){
		$executable = true;	//是否执行全局过滤器
		$shell = $urlInfo['shell'];
		if(0 == $urlInfo['status']){
			$tmp = explode('-', DEFAULT_URL_SHELL);
			$shell = $tmp[1];
		}
		$sneg = AiAdditionalFilterMapRule::$shellNeedGlobal;
		$aneg = AiAdditionalFilterMapRule::$actionNeedGlobal;
		$snog = AiAdditionalFilterMapRule::$shellNoGlobal;
		$anog = AiAdditionalFilterMapRule::$actionNoGlobal;
		//容器：放置需要过滤器的指令
		$list_need_filter = self::globalFilter_inner_tool($aneg,$sneg,$snog);
		//容器：放置不需要过滤器的指令
		$list_no_filter = self::globalFilter_inner_tool($anog,$snog,$sneg);
// 		print_r($list_need_filter);echo "<br>";
// 		print_r($list_no_filter);echo "<br>";
		if(AiAdditionalFilterMapRule::$elsePrior){
			//设置了其它没配置的需要全局过滤器，则只需考虑不需要过滤器的指令
			if(in_array($shell, $list_no_filter))
				$executable = false;
		}else{
			//设置了其它没配置的不需要全局过滤器，则只需考虑需要过滤器的指令
			if( ! in_array($shell, $list_need_filter))
				$executable = false;
		}
		//开始决定是否执行过滤器（注意URL状态status）
		if( $executable && 5 != $urlInfo['status'] ){
			$g_f_m = get_class_methods(GLOBAL_FILTER_NAME);
			if(in_array('doFilter', $g_f_m))
				eval( GLOBAL_FILTER_NAME . '::doFilter($urlInfo);' );
			$ii = 0;
			while(++$ii){
				if(! in_array('doFilter_'.$ii, $g_f_m))
					break;
				eval(GLOBAL_FILTER_NAME.'::doFilter_'.$ii.'($urlInfo);');
			}
		}
	}
	
}






/**
 * 内置的过滤器接口，该接口仅提供一个过滤方法。
 * 如果某个过程仅需一个过滤器，则可以直接实现该接口
 * 一般地，这个过滤方法专门用来绑定XxxxAction对象。
 * 凡是调用时需要通过XxxxAction对象完成的过程，只要绑定了该过滤器，就能够产生访问XxxxAction的第一关卡。
 * 使用内置过滤器的方法：直接implements Filter，然后实现doFilter()即可。
 * Otherwise:
 * 如果XxxxAction内的方法也需要过滤器，就需要定义一个过滤器群组，格式为：class XxxxFilter extends FilterUtil{...}
 * 由于不是所有的Action方法都需要过滤器，因此，需要添加过滤器的方法，一律要用protected修饰。
 * 相应地，应该声明对应的过滤器方法，
 * 格式为：public function xxxx(){...}; //“xxxx”部分等于被需过滤的方法名，如 public function index(){...};
 * Otherwise:
 * 要为同一个过程绑定多个过滤器，则需要在过滤器方法名后添加“_数字”，数字从1开始
 * 	对于Action[对象]，格式如：public function doFilter_1(){...};	//由于还有doFilter()，因此doFilter_1()是第二个过滤器。
 * 	对于Action[方法]，格式如public function index_1(){...}，表示index()方法的第一个过滤器
 */
interface Filter {
	function doFilter();
}

