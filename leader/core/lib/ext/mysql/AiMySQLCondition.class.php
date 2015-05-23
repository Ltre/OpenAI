<?php

/**
 * SQL的单个条件
 * @author Oriki
 *
 */

class AiMySQLCondition {
	public $field;		//数据表字段名
	public $relate;	//关系：>、<、>=、<=、!=、=、in、like等等
	public $value;		//值或值的集合、正则表达式等等，值的类型要严格转换后才能传入
	/**
	 * 构造一个SQL条件
	 * @param string $field
	 * @param string $relate
	 * @param string $value
	 */
	public function __construct($field, $relate, $value) {
		$this->field = $field;
		$this->relate = $relate;
		$this->value = $value;
	}
	/**
	 * 返回：“字段  关系  值”
	 */
	 public function __toString(){
		$quot = is_string($this->value) ? "\"" : '';
		if(null===$this->value)
			$this->value = 'null';
		else
			$this->value = $quot.$this->value.$quot;
		return $this->field.' '.$this->relate.' '.$this->value;
	}
}