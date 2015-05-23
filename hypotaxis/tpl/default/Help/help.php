<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>欢迎使用ActionInvoker团队开发版</title>
<script type="text/javascript" src="<?php printf(JQUERY_LIB_PATH); ?>"></script>
<script type="text/javascript" src="../public/res/js/Ai/AiForm.js"></script>
</head>
<body bgcolor="#f9f9f9">
<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#title").slideDown("slow");
		$("font").click(function(){
			$("body").attr("bgcolor", "white");
			$("#title").slideUp("slow");
			//替换原文档为shelltest指令所在的新文档
			$.post('./?xxx=shelltest',function(data){
				var doc = document.open("text/html","replace");
				doc.write(data);
				doc.close();
			});
		});
	});
</script>
<h1 id="title" style="margin-top:200px;font-family:微软雅黑;display:none;" align="center">当前目录属于 ☞ <font style="cursor:help;" color="blue">成员Hypotaxis</font> 使用！</h1><br/>
<div style="width:330px;font-family:微软雅黑;bottom:5px;font-weight:bold;position:fixed;float:right;right:0px;">© 2013-7 Oreki, Inc.All rights reserved．</div>
<?php 
if(isset($_SESSION['matrix'])) { 
	//unset($_SESSION['matrix']);
?>
<script type="text/javascript">
	$.post('./?x=matrix-matrix',function(data){
		$AiForm.replaceDocWithData(data);
	});
</script>
<?php } ?>
</body>
</html>