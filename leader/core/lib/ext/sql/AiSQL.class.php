<?php
class AiSQL {
	/**
	 * 从/core/setting/sql/目录按照xxx.sql获取sql语句
	 * @param string $key 文件名。
	 * 		例如：“getQuery”将定位到“/core/setting/sql/getQuery.sql”
	 * @return string $sql 查询的sql语句字符串  获取失败则返回false
	 * @tutorial
	 * 		if(false!==(@$sql=AiSQL::getSqlExpr('getQuery')))
	 * 			$rs = AiMySQL::queryCustom($sql);
	 * 		else
	 * 			echo "获取sql语句失败";
	 */
	static function getSqlExpr($key){
		return file_get_contents(APPROOT."core/setting/sql/$key.sql");
	}
	
	/**
	 * 编辑中，不要使用！！
	 * 通过id值获取/core/setting/sql.xml中的SQL语句
	 * @param string $id 
	 */
	private static function getSqlExprFromXml( $id ){
		$xml = simplexml_load_file(APPROOT.'core/setting/sql.xml');
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->load(APPROOT.'core/setting/sql.xml');
		
		/* $root = $dom->getElementsByTagName('sql')->item(0);
		foreach ($root->childNodes as $index=>$node){
			if(XML_TEXT_NODE == $node->nodeType){
				
				$node->attributes->getNamedItem("id")->nodeValue;
			}
		} */
		
		$root = $dom->getElementById('getBookSell');
	}
	
	/*
	 * 本类测试方法
	 */
	static private function test(){
		if(false!==(@$sql=AiSQL::getSqlExpr('getQuery')))
			$rs = AiMySQL::queryCustom($sql);
		else
			echo "获取sql语句失败";
		
		
		
		$list = AiMySQL::queryCustomByParams(
				AiSQL::getSqlExpr('getBookSell'),
				array('leftuser'=>$user->id, 'rightuser'=>$user->id)
		);
		var_dump($list);
	}
}
