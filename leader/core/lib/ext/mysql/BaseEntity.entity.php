<?php
/**
 * @author Oriki
 * 数据库专用的基础实体 <br>
 * 由于get_class_vars()和get_object_vars()只能在类外取得public的属性， <br>
 * 因此，为确保获得属性全部，所有与数据表字段对应的属性均应设置为public	<br>
 */
class BaseEntity {
	/*
	 * 默认主键为id。类型必须要与数据表的对应。
	 * 要么是int，要么是string
	 */
	public $id;
	
	/**
	 * 初始化有值的成员，没有值的用null代替
	 * @param array $members 键值对形式的成员参数 <br>
	 * 	如 <br>
	 * 		array(	<br>
	 * 			'username'	=>	'abc',	<br>
	 * 			'password'	=>	'def',	<br>
	 * 		)	<br>
	 */
	public function init(array $members){
		foreach ($members as $key=>$value){
			$this->$key = $value;
		}
	}
	
}