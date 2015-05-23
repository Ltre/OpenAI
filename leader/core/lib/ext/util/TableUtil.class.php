<?php

/**
 * 与数据表有关的实用类
 * @author Oriki
 * @since 2014-3-28
 */

class TableUtil {
	/**
	 * 根据条件筛选记录，并在结果集中取第一条记录中某个字段的值
	 * @invoked 多处调用
	 * @param string $table 去除前缀的表名
	 * @param string $dest 目标字段名（必须与实体类中的成员命名完全对应）
	 * @param array(AiMySQLCondition) $conditions 筛选条件的集合
	 * @return mixed 返回目标字段的值，失败时返回false。	<br>
	 * 		如果所取字段是boolean类型，则该方法无法判别是否成功获取到数值。
	 */
	static function getValueFromField($table, $dest, $conditions){
		$rs = AiMySQL::queryEntity($table, $conditions);
		$r = null;
		if($rs) foreach ($rs as $r);
		return $r ? $r->$dest : false;
	}
	
	/**
	 * 根据某个唯一性字段，获取实体
	 * @invoked 多处调用
	 * @param string $table 去除前缀的表名
	 * @param array(AiMySQLCondition) $conditions 筛选条件的集合
	 * @return BaseEntity mixed 返回目标实体，失败时返回null。	<br>
	 */
	static function getEntityFromField($table, $conditions){
		$rs = AiMySQL::queryEntity($table, $conditions);
		$r = null; if($rs) foreach ($rs as $r);
		return $r;
	}
	
	/**
	 * AiMySQL::insert()的改良方法，用于插入一条数据（目前测试成功）
	 * 使用时，注明哪些字段值在SQL中需要引号
	 * @param BaseEntity $table 已经初始化了的实体
	 * @param array 在插入SQL新增语句时需要加引号的字段名 的 集合
	 * 	例如：array('username', 'password')  
	 * 	注意：
	 * 		1、对象属性名要与实体字段名的大小写要一致。
	 * 		2、需要引号的字段有：*char, date*, *text 等等。
	 * 		3、记住，一定要指明需要引号的字段，否则插入不会成功。
	 * @return PDO插入数据后返回的常规值，具体自查PHP文档（受影响的行数，没有更改时返回0行，要注意特别判断）
	 */
	static function insert(BaseEntity $entity, array $needQuot){
		$conn = AiMySQL::connect();
		$conn->beginTransaction();
		$table = strtolower( get_class($entity) );//从实体中取表名
		$props = null;
		$values = null;
		foreach ( get_object_vars($entity) as $name => $value ){
			if(null===$value) continue;//如果该字段没被赋初始值
			//将字段值中的双引号转义
			if(false!==strpos($value, '"'))
				$value = str_replace('"', "\\\"", $value);
			//将字段值中的单引号转义
			if(false!==strpos($value, "'"))
				$value = str_replace("'", "\\'", $value);
			$quot = in_array($name, $needQuot) ? '"' : '';
			$props .= $name.',';
			$values .= $quot.$value.$quot.',';
		}
		$props = trim($props, ',');
		$values = trim($values, ',');
		$affect = $conn->exec("insert into ".AiDBConfiguration::$table_prefix."$table ( $props ) values( $values );");
		$conn->commit();
		return $affect;
	}
	
	
	/**
	 * TableUtil::insert()的改良方法，参数改用SQL预处理，用于插入一条数据（目前测试成功）
	 * @param BaseEntity $entity 已经初始化了的实体
	 * @return boolean PDO预处理插入数据后返回的常规值，具体自查PHP文档（true or false）
	 */
	static function insert_with_prepare(BaseEntity $entity){
		$conn = AiMySQL::connect();
		$conn->beginTransaction();
		$table = strtolower( get_class($entity) );//从实体中取表名
		$props = null;
		$values = null;
		$preprms = array();//SQL预处理参数数组
		foreach ( get_object_vars($entity) as $name => $value ){
			if(null===$value) continue;//如果该字段没被赋初始值
			$props .= $name.',';//属性名用逗号隔开
			$values .= ':'.$name.',';
			$preprms[$name] = $value;
		}
		$props = trim($props, ',');
		$values = trim($values, ',');
		$presql = "insert into ".AiDBConfiguration::$table_prefix."$table ( $props ) values( $values );";
		$stmt = $conn->prepare($presql);
		$affect = $stmt->execute($preprms);
		$conn->commit();
		return $affect;//BOOL
	}	
	
	/**
	 * TableUtil::insert_with_prepare()的增强方法，参数改用SQL预处理，用于插入一组数据（目前测试成功）
	 * @param array(BaseEntity) $entity 已经初始化了的实体的数组
	 * @return boolean 由PDO预处理插入数据后返回的常规值决定，具体自查PHP文档（true or false）
	 */
	static function insertMulti_with_prepare(array $entitys){
		$conn = AiMySQL::connect();
		$conn->beginTransaction();
		$i = 0;
		$flag = true;
		foreach ($entitys as $entity){
			$preprms = array();//SQL预处理参数数组
			$table = strtolower( get_class($entity) );//从实体中取表名
			$props = null;
			$values = null;
			foreach ( get_object_vars($entity) as $name => $value ){
				if(null===$value) continue;//如果该字段没被赋初始值
				$props .= $name.', ';//属性名用逗号隔开
				$values .= ':'.$name.$i.', ';
				$preprms[$name.$i] = $value;
			}
			$props = trim($props, ', ');
			$values = trim($values, ', ');
			$presql = "insert into ".AiDBConfiguration::$table_prefix."$table ( $props ) values( $values );";
			$stmt = $conn->prepare($presql);
			$flag = $stmt->execute($preprms) ? $flag : false;
			$i++;
		}
		$conn->commit();
		return $flag;//BOOL
	}
	
	/**
	 * AiMySQL::update()的改良方法，采用SQL预处理，用于更新一条数据
	 * @param BaseEntity $entity
	 */
	static function update_with_prepare(BaseEntity $entity){
		$conn = AiMySQL::connect();
		$conn->beginTransaction();
		$table = strtolower( get_class($entity) );//从实体中取表名
		$expr = null;
		$preprms = array();//SQL预处理参数数组
		foreach ( get_object_vars($entity) as $name => $value ){
			if(null===$value) continue;//如果该字段没被赋初始值
			$expr .= $name.' = :'.$name.',';
			$preprms[$name] = $value;
		}
		$expr = trim($expr, ',');
		$presql = "update ".AiDBConfiguration::$table_prefix."$table set $expr where id = ".$entity->id;
		$stmt = $conn->prepare($presql);
		$affect = $stmt->execute($preprms);
		$conn->commit();
		return $affect;//BOOL
	}
	
	/**
	 * AiMySQL::queryCustom()和AiMySQL::queryCustomByParams()的改良方法
	 * @param string $preSql SQL预处理语句
	 * @param array $params 参数数组，如array('id'=>$id, 'name'=>$name)
	 * @param int $num 所需结果集个数，传入0或不传值，则默认为全部结果集
	 * @param int $start 结果集游标，传入0或不传值，则默认为从0开始
	 * @param int $fetchtype PDO结果集的遍历方式，默认为PDO::FETCH_OBJ
	 * @return 对象数组，其结构视SQL语句而定
	 * @tutorial
	 * 		$list = AiMySQL::queryCustomByParams(	<br>
	 *			AiSQL::getSqlExpr('getBookSell'), //预处理的SQL	<br>
	 *			array('leftuser'=>$user->id, 'rightuser'=>$user->id) //参数对 <br>
	 *		);	<br>
	 *		var_dump($list); <br>
	 */
	static function queryCustom($preSql, array $params = array(), $num=0, $start=0, $fetchtype=PDO::FETCH_OBJ){
		$List = array();
		$conn = AiMySQL::connect();
		if(0>$num) $num=0;
		if(0>$start) $start=0;
		$flag = false;
		if($num){
			$preSql .= " limit $start,$num";
		}else{
			$flag = true;
		}
		$stmt = $conn->prepare($preSql);
		$stmt->execute($params);
		$i = 0;
		while( @$result = $stmt -> fetch($fetchtype)){
			if($flag){
				if($i >= $start){
					$List[] = $result;
				}
			}else{
				$List[] = $result;
			}
			$i ++;
		}
		return $List;
	}
	
}