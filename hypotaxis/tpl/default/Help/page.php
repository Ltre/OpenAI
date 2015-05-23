<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- 测试AJAX提交 -->
<head>
<script type="text/javascript" src="<?php printf(APPROOT.JQUERY_LIB_PATH); ?>"></script>
</head>
<body bgcolor="#749fdf">
	测试DEMO指令的响应：<input type="text" id="val" />
	<input type="button" id="bu" value="测试" />
	<br/>测试其它指令：<br/>
	URL指令：<input type="text" id="shell" />
	后续参数（如果有）：<input type="text" id="afprm" />
	<input type="button" id="go" value="测试" />
	
	<div style="width:960px;height:640px;margin:80px auto;border:5px groove black;background-color:gray;text-align:center;"></div>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		//测试DEMO指令的响应
		demo_evt = function(){
			val = $("#val").val();
			if(''==val){
				$('div').html('');
				return;
			}
			$.post('./?xxx=demo|'+val, function(data){
				$("div").html(data);
			});
		};
		$("#val").keyup( demo_evt );
		$("#bu").click( demo_evt );
		//测试其它指令
		else_evt = function(){
			shell = $("#shell").val();
			afprm = $("#afprm").val();
			if(''==shell&&''==afprm){
				$('div').html('');
				return;
			}
			$.post('./?xxx=' + shell + ( ''==afprm?'':'|' ) + afprm, function(data){
				$("div").html(data);
			});
		};
		$("#shell,#afprm").keyup( else_evt );
		$("#go").keyup( else_evt );
	});
	</script>
</html>
</body>