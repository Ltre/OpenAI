<?php
/**
 * 框架中最核心最关键的算法
 * @author Oreki
 * @date 2013-12-5
 */

class AiCore {
	/**
	 * TODO: 包含目录内所有层次子目录的*.php文件
	 */
	public static function includePhpWithEveryLayer( $dir ){
		foreach (explode('|', $dir) as $path){
			if( in_array($path, array('','./','../')) || false !== strpos($path, './'))
				continue;
			$path = trim(trim($path),'/');
			$path .= '/';
			self::parsePhpDirFromLibDirAndAutoIncludeTheir( APPROOT . $path );
		}
	}
	/**
	 * TODO: 自动检测某层目录内指定格式的文件，并包含之。
	 * 例如：Xxx.action.php，Xxx.filter.php, Xxx.entity.php
	 * @param string $xxxFormatDir 目录路径，要求以“/”结尾。例如：APPROOT.PUBLIC_DIR_NAME.'/core/entity/'
	 * @param string $secondExtname 显现特性的第二扩展名，例如：“AbcObject.obj.php”中的“obj”。
	 * @param string $suffix 显现特性的后缀，例如“AbcObject”中的“Object”。一般不推荐用这个参数，原因在于不简洁。
	 */
	public static function parseXxxFormatFromDirAndAutoIncludeTheir( $xxxFormatDir, $secondExtname, $suffix=null){
		foreach (glob(APPROOT.$xxxFormatDir.'{A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z}*' . $suffix . '.' . $secondExtname . '.php', GLOB_BRACE) as $xxxFormatDirFile){
			require_once $xxxFormatDirFile;
		}
	}
	
	/*
	 * 自动检测开发者在  指定的  目录自行添加的库文件或目录（或者其它php常量配置文件），并包含其内任何层次目录的[*.php]。
	 * 参数要求：格式如：$path = APPROOT.PUBLIC_DIR_NAME.'/core/lib/'
	 * 			必须从项目根目录开始指定
	 * 			不能以“/”开头
	 * 			要以“/”结尾
	 * 参数$layer是层次，以输入路径为第0层，每递归一次便自增，用于测试，可以查看递归了多少次。
	 */
	private static function parsePhpDirFromLibDirAndAutoIncludeTheir($path, $layer=0){
		//echo "=====进入第($layer)层=====<br>";
		if( is_dir($path) && ($dh=opendir($path)) ) {
			while(false !== ($file=readdir($dh))){
				if(in_array($file, array('.','..')))
					continue;
				if(is_dir($path.$file)){
					//echo "第($layer)层：目录 - ".$path.$file."/<br>";
					self::parsePhpDirFromLibDirAndAutoIncludeTheir($path.$file.'/', $layer+1);
				}else{
					//echo "第($layer)层：文件 - ".$path.$file."<br>";
					if(0!==preg_match('/\.php$/', $file))
						require_once $path.$file;
				}
			}
			closedir($dh);
			//echo "=====跳出第($layer)层=====<br>";
		}
	}
}