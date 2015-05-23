<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>欢迎使用ActionInvoker团队开发版</title>
<script type="text/javascript" src="<?php printf(APPROOT.JQUERY_LIB_PATH); ?>"></script>
</head>
<body bgcolor="#f9f9f9">
<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#title").slideDown("slow");
		$("font").click(function(){
			$("body").attr("bgcolor", "white");
			$("#title").slideUp("slow");
			$("#image").css("display", "block");
		});
	});
</script>
<h1 id="title" style="margin-top:200px;font-family:微软雅黑;display:none;" align="center">欢迎使用 ☞ <font style="cursor:help;" color="blue">ActionInvoker</font> 团队开发版！</h1><br/>
<div style="width:330px;font-family:微软雅黑;bottom:5px;font-weight:bold;position:fixed;float:right;right:0px;">© 2013-7 Oreki, Inc.All rights reserved．</div>
<img id="image" style="width:1149px;margin:0 auto;display:none;" src="<?php print'readme.jpg' ?>"/>
</body>
</html>