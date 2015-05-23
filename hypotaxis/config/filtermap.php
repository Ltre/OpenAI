<?php
/**
 * 附加的过滤器映射规则，简称为“附加过滤规则（AFR）”
 * 这里的规则不会对XxxxAction对应的XxxxFilter造成任何影响。
 * [AFR]的叠加效果：
 * 	指令级的规则会对模块级的规则进一步细致的更改。
 */


class AiAdditionalFilterMapRule {
	
	
	/*
	 * 没有在本类中设置映射的模块或指令【需要 | 不需要】全局过滤器
	 * true为需要，false为不需要
	 * 该值设定后，还有另外一种效果，即：
	 * 	当为true时，如果某指令（如index）既需要又不需要全局过滤器，则视为需要全局过滤器
	 * 	当为false时，如果某指令（如index）既需要又不需要全局过滤器，则视为 不 需要全局过滤器
	 */
	public static $elsePrior = false;
	
	
	/*
	 * 【不需要】全局过滤器的URL指令
	 */
	public static $shellNoGlobal = array(
			'index',
			'help',
	);
	
	
	/*
	 * 【需要】全局过滤器的URL指令
	 */
	public static $shellNeedGlobal = array(
			'mysql',
	);
	
	
	/*
	 * 【不需要】全局过滤器的Action模块
	 *  该规则实际上是指令过滤规则的集合，
	 *  即：这些Action内的所有指令都不需要全局过滤器。
	 */
	public static $actionNoGlobal = array(
			'Index',
			'Help'
	);
	
	
	/*
	 * 【需要】全局过滤器的Action模块
	 * Action名称大小写均可，
	 * 必须以大写开头，后续小写，否则视为无效
	 * 该规则实际上是指令过滤规则的集合，
	 * 即：这些Action内的所有指令都不需要全局过滤器。
	 */
	public static $actionNeedGlobal = array(
			'Test',
	);
	
	
}
