<?php
/**
 * MYSQL配置文件
 * 附带类似ORM的机制
 * @author Oreki
 */
class AiMySQL {
	/* -----------------------数据源配置开始==》------------------------- */

	private static $host = '';  //数据库主机名或地址
	private static $port = 3306;  //端口
	private static $db = '';	  //数据库
	private static $user = '';  //用户名 
	private static $pwd = '';   //密码
	private static $conn = null;	//单例MySQL连接
	/*
	 * 数据表前缀。
	 * 例如前缀为"fm_"，设置该前缀后，fm_xxxx表将与名为Xxxx的实体对应。
	 * 如果数据表不需要前缀，则设置为null或空串即可。
	 */
	private static $table_prefix = 'fm_';
	
	/* -----------------------数据源配置结束==》------------------------- */
	
	/*
	 * 初始化静态成员
	 */
	private static function init(){
		foreach (array('host','port','db','user','pwd','table_prefix') as $member)
			eval('self::$'.$member.' = '.'AiDBConfiguration::$'.$member.';');
	}
	
	/**
	 * 连接Mysql，返回PDO对象
	 * 本实用类中的方法已经调用了该方法。
	 * 一般只在需要获取PDO对象时才使用该方法。
	 * @return PDO
	 */
	public static function connect(){
		self::init();
		if(null != self::$conn)
			return self::$conn;
		$dsn = 'mysql:host='.self::$host.';dbname='.self::$db.';port='.self::$port;
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
		);
		@$pdo = new PDO($dsn, self::$user, self::$pwd, $options);
		return $pdo;
	}
	
	/*
	 * 获取字段的类型
	 * @param $table 表名
	 * return array 数组（格式为：字段名=>类型）
	 */
	private static function getFieldType( $table ){
		$typeList = array();
		foreach ( self::connect() -> query("desc ".self::$table_prefix."$table ;") as $row ){
			$typeList[ $row['Field'] ] = $row['Type'];
		}
		return $typeList;
	}
	
	/*
	 * 判断每个实体成员类型是否完全与数据表字段对应（忽略空值的情况）
	 * @param $member 成员名
	 * @param $value 成员值
	 * @return true 符合ORM false 不符合ORM
	 */
	private static function compareType($table, $member, $value){
		$typeList = self::getFieldType($table);
		$type = $typeList[ $member ];
		//对空值放行，在插入数据时请注意这点。
		if( is_null($value) )
			return true;
		//放行标识：1为通过，0为拒绝
		$flag = 0;
		//string与varchar、char、text、date、datetime对应
		if( is_string($value) && ( false !== stristr( $type, 'char' ) || false !== stristr( $type, 'text' ) || false !== stristr($type, 'date') ) )
			$flag = 1;
		//bool与tinyint(1)对应
		if( ( is_bool($value) && false !== stristr( $type, 'tinyint(1)' ) ) )
			$flag = 1;
		//float与字段的float或double对应
		if( ( is_float($value) && ( false !== stristr( $type, 'float') || false !== stristr( $type, 'double') ) ) )
			$flag = 1;
		//int与字段的int对应
		if( ( is_int($value) && false !== stristr( $type , 'int') ) )
			$flag = 1;
		if(1 == $flag)
			return true;
		else
			return false;
	}
	
	/**
	 * 增
	 * @param BaseEntity $be 待插入的实体
	 * @return boolean 插入成功则返回true
	 */
	public static function insert(BaseEntity $be){
		$conn = self::connect();
		$conn -> beginTransaction();
		$table = strtolower( get_class($be) );
		$props = null;
		$values = null;
		$i = 0;
		$typeList = self::getFieldType($table);
		//echo "Object : ";print_r($typeList);echo '<br>';
		foreach ( get_object_vars($be) as $name => $value ){
			//检查数据类型映射
			if( ! self::compareType($table, $name, $value) )
				return false;
			$quot = false !== stristr( $typeList[ $name ], 'char' ) 
						|| false !== stristr( $typeList[ $name ], 'text' ) 
						|| false !== stristr( $typeList[ $name ], 'date' )
					? '"' : null;
			//将字段值中的双引号替换成全角双引号，在数据库中重新取回时，又变回原来的半角形式
			if(false!==strpos($value, '"'))
				$value = str_replace('"', "＂", $value);
			if(null===$value) continue;//如果该字段没被赋初始值
			if(false===$value) $value='false';//如果字段为布尔假，则用无引号的false
			if(true===$value) $value='true';//如果字段为布尔真，则用无引号的true
			$props .= $name.',';
			$values .= $quot.$value.$quot.',';
		}
		$props = trim($props, ',');
		$values = trim($values, ',');
 		$affect = $conn->exec("insert into ".self::$table_prefix."$table ( $props ) values( $values );");
		$conn->commit();
		//echo "insert into ".self::$table_prefix."$table ( $props ) values( $values );";
		//echo $affect ? '插入成功' : '没有插入';
		return $affect ? true : false;
	}
	
	/**
	 * 删
	 * @param BaseEntity $be 待删除的实体
	 * @return boolean 插入成功则返回true
	 */
	public static function delete(BaseEntity $be){
		$conn = self::connect();
		$conn -> beginTransaction();
		$table = strtolower( get_class($be) );
		$affect = $conn -> exec("delete from ".self::$table_prefix."$table where id = " . $be->id . ";");
		$conn -> commit();
		//echo $affect ? '删除成功' : '没有删除';
		return $affect ? true : false;
	}
	
	/**
	 * 改
	 * @param BaseEntity $be 被修改的实体
	 * @return boolean 修改成功则返回true
	 */
	public static function update(BaseEntity $be){
		$conn = self::connect();
		$conn -> beginTransaction();
		$table = strtolower( get_class($be) );
		$assignment = null;
		$typeList = self::getFieldType($table);
		foreach (get_object_vars($be) as $name => $value){
			//检查数据类型映射
			if( ! self::compareType($table, $name, $value) )
				return false;
			if(null===$value)continue;
			if(false===$value) $value='false';//如果字段为布尔假，则用无引号的false
			if(true===$value) $value='true';//如果字段为布尔真，则用无引号的true
			/* $quot = false !== stristr( $typeList[ $name ], 'char' )
				? '"' : null; */
			$quot = false !== stristr( $typeList[ $name ], 'char' )
				|| false !== stristr( $typeList[ $name ], 'text' )
				|| false !== stristr( $typeList[ $name ], 'date' )
			? '"' : null;
			$assignment .= $name . '=' . $quot.$value.$quot.',';
		}
		$assignment = trim($assignment, ',');
		$affect = $conn -> exec("update ".self::$table_prefix."$table set $assignment where id = " . $be->id . ";");
		//echo "update ".self::$table_prefix."$table set $assignment where id = " . $be->id . ";<br>";
		$conn -> commit();
		return $affect ? true : false;
	}
	
	/**
	 * 查实体。
	 * 一般用于单表查询。
	 * @param string $table 实体类的名称（或表的名称，大小写均可，系统会将之转为小写）
	 * @param array<class Condition> $conditions 条件集合
	 * 		传入null或不传值，则代表无条件查询
	 * @param string $combination 条件组合方式：and、or。默认为and
	 * 		组合方式：暂时只支持不带括号的组合方式，且要么全为and，要么全为or。
	 * 		接受值：AiMySQLCombination::COMB_AND、AiMySQLCombination::COMB_OR
	 * 		如果乱传值，则视为AiMySQLCombination::COMB_AND
	 * @param string $order 排序字段。传入null或不传值，则表示表示没有order by子句。
	 * @param string $sortable 排序方式。传入null或不传值，则表示order by子句后没有指定排序方式。
	 * 		接受值：AiMySQLOrderBy::ASC、AiMySQLOrderBy::DESC
	 * 		如果乱传值，则视为AiMySQLOrderBy::DESC
	 * @param int $num 取结果集的个数。从游标$start算起，取得的个数以实际为准。
	 * 		传入0或不传则表示取得全部结果集。
	 * @param int $start 结果集开始游标。MySQL默认从0开始。
	 * 		传入0或不传值，则代表从第一个记录开始。 
	 * @return 查询成功，则以Object集合的形式返回结果集（空集也算查询成功）
	 * 		如果参数不正确，则返回false，以示查询失败（这点要注意判断）
	 */
	public static function queryEntity($table, $conditions=null, $combination=null, $order=null, $sortable=null, $num=0, $start=0){
		$List = array();
		$conn = self::connect();
		$table = strtolower($table);
		$sql = "select * from ".self::$table_prefix."$table ";
		//拼接where条件
		$len = 0;
		if( 0 != ($len = count($conditions)) )//此处$len有问题
			$sql .= 'where ';
		//条件组合方式
		$combination = null==$combination 
			? AiMySQLCombination::COMB_AND 
			: ( AiMySQLCombination::COMB_OR==$combination
				? $combination
				: AiMySQLCombination::COMB_AND
		);
		$i = 0;
		if($len > 0){
			foreach ($conditions as $c){
				//判断类型是否符合ORM标准
				if(! self::compareType($table, $c->field, $c->value))
					return false;
				$i++;
				$sql .= ' '.$c;	//c with __toString function()
				if($i < $len)
					$sql .= " $combination";
			}
		}
		//echo "<br>$sql<br>";
		//排序子句
		$orderby = null==$order
			? null
			: ' order by ' . $order .' '. ( null==$sortable
				? AiMySQLOrderBy::ASC
				: ( AiMySQLOrderBy::DESC==$sortable
					? $sortable
					: AiMySQLOrderBy::DESC
		));
		//截取游标范围
		$limit = null;
		if($num < 0){
			return false;
		}else if($num > 0){
			if($start < 0)
				return false;
			else if($start == 0)
				$limit = " limit $num";
			else if($start > 0)
				$limit = " limit $start , $num ";
		}
		
		$sql .= $orderby . $limit;
		//执行查询
		$stmt = $conn -> query($sql);
		//将简化的表名（实体名）首字母转为大写
		$first_letter = substr($table, 0, 1);//实体名（简化表名）首字母
		$table = substr_replace($table, strtoupper($first_letter), 0, 1);
		//遍历结果集到对象集合中，并确保对象的每个字段的类型为原始的
		while( @$result = $stmt -> fetch(PDO::FETCH_OBJ) ){
			eval('$object = new '.$table.'();');
			foreach (get_class_vars($table) as $var=>$value) {
				$tmp = null;
				$typeList = self::getFieldType($table);
				$type = $typeList[ $var ];
				if( false !== stristr( $type, 'char' ) )
					$tmp = strval($result->$var);
				if( false !== stristr( $type, 'tinyint(1)' ) )
					$tmp = 1==$result->$var ? true : false;
				if( ( false !== stristr( $type, 'float') || false !== stristr( $type, 'double') ) )
					$tmp = floatval($result->$var);
				if(  false !== stristr( $type , 'int') )
					$tmp = intval($result->$var);
				//$result->$var = (null != $tmp) ? $tmp : $result->$var;
				$object->$var = (null != $tmp) ? $tmp : $result->$var;
			}
			//$List[] = $result;
			$List[] = $object;
		}
		return $List;
	}
	
	/**
	 * 自定义SQL查询
	 * 一般用于多表、分组、统计、联合、排序等多种方式联合的查询。
	 * 注意：使用自定义SQL时，应该注意表名是否有设置前缀，以免出错。
	 * @param string $sql SQL语句
	 * @return 返回二维数组，一级元素表示一条记录，二级元素表示字段或伪列（可用数字索引或列名来取值）
	 */
	public static function queryCustom($sql){
		$List = array();
		$conn = self::connect();
		$stmt = $conn->query($sql);
		while( @$result = $stmt -> fetch(PDO::FETCH_BOTH)){
			$List[] = $result;
		}
		return $List;
	}
	
	/**
	 * 
	 * @param string $preSql 需要预处理的SQL语句 <br>
	 * 		如：select * from users where id = :id and name = :name
	 * @param array $params SQL预处理参数，数组形式。
	 * 		如：array(	<br>
	 * 			'id' => 5,	<br>
	 * 			'name' => 'abc',	<br>
	 * 		)
	 * @param int $num 所需结果集个数，传入0或不传值，则默认为全部结果集
	 * @param int $start 结果集游标，传入0或不传值，则默认为从0开始
	 * @return 返回二维数组，一级元素表示一条记录，二级元素表示字段或伪列（可用数字索引或列名来取值）
	 * @tutorial
	 * 		$list = AiMySQL::queryCustomByParams(	<br>
	 *			AiSQL::getSqlExpr('getBookSell'), //预处理的SQL	<br>
	 *			array('leftuser'=>$user->id, 'rightuser'=>$user->id) //参数对 <br>
	 *		);	<br>
	 *		var_dump($list); <br>
	 */
	public static function queryCustomByParams($preSql, $params, $num=0, $start=0){
		$List = array();
		$conn = self::connect();
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
		while( @$result = $stmt -> fetch(PDO::FETCH_BOTH)){
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
	
	
	/**
	 * 获取SQL查询语句的结果集个数
	 * @param string $sql 仅限不带参数的语句
	 * @return number 结果集个数
	 */
	public static function getNumsOfRs( $sql ){
		return self::connect()->query($sql)->rowCount();
	}
	
}