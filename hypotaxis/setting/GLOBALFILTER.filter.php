<?php
class GLOBALFILTER{
	/**
	 * 全局过滤器
	 * 首先从doFilter开始执行，
	 * 而后执行后续过滤器链doFilter_*()，“*”代表数字1,2,3,4...
	 * 过滤器链中相邻两个方法的名称的数字部分也必须相邻，
	 * 否则将在“断链”处终止过滤，直接进入后续业务。
	 * @param $urlInfo
	 */
	public static function doFilter( $urlInfo ){
		/* 这里填充全局过滤的过程，例如会话验证（拦截成功后可使用die()终止脚本） */
		echo '全局过滤器：'.__CLASS__.'::'.__METHOD__.'()执行<br>';
		
	}
	public static function doFilter_1($urlInfo){
		echo '全局过滤器：'.__CLASS__.'::'.__METHOD__.'()执行<br>';
	}
	public static function doFilter_2($urlInfo){
		echo '全局过滤器：'.__CLASS__.'::'.__METHOD__.'()执行<br>';
	}
	public static function doFilter_3($urlInfo){
		echo '全局过滤器：'.__CLASS__.'::'.__METHOD__.'()执行<br>';
	}
}