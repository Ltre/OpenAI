<?php
/**
 * ��Action��ͬʱ��ģ��������Ͳ�������������Ϊ���Զ���
 */
class TestAction extends ActionUtil implements Filter{
	
	//Ҫʹtest()��ӵĹ�����������Ч������ʿ�������Ϊprotected���󶨵Ĺ����������У�TestFilter::test(),TestFilter::test_*()
	protected function test($urlInfo){
		echo "������ִ����ϣ�{ ���ڽ���TestAction : : test( )�ķ����� }<br>";
		echo '/readme.txt������Լ�У�'.intval(filesize(APPROOT.'readme.txt')/(floatval(13)/3)).'<br>';
		require_once OTHER_TEMPLATE_DIR.'othertest.php';//������ͨģ��
		echo '{ TestAction : : test( )�ķ�����ִ�н��� }<br>';
	}
	
	//ʵ��ϵͳ���õ�ģ�鵥������ Filter::doFilter()���������TestActionģ��ǰ����һ�������������ʵ��Filter�ӿڼ��ɡ�
	public function doFilter() {
		echo substr(__CLASS__, 0, -6)."Actionģ��󶨵����ù�����ִ��...<br>";
	}

}
