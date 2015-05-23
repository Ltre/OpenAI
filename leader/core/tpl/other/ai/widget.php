<html>
<head>
<script type="text/javascript" src="<?php printf(APPROOT.JQUERY_LIB_PATH); ?>"></script>
<script type="text/javascript" src="leader/res/js/Ai/AiForm.js"></script><!-- 导入AI表单支持库 -->
<!-- AI表单编写完毕后，开始自定义数据处理，并绑定（注意：此段代码必须在文档加载完成后才能执行，否则将失效） -->
<script type="text/javascript">
	//编写：AI表单对应的反馈数据处理方法集合
	jQuery(document).ready(function($){
		/**
		 * 【表单】
		 * 验证表单验证过程的集合
		 */
		$AiFormValidates = {
			//示例【指令名称：function(args){处理过程。。。}】
			aiFormLogin : function (args){
				alert("第一个参数是："+args[1]+"\r\n第二个参数是："+args[2]);
				//假定验证成功
				if(true){
					alert("表单数据通过验证，数据准备提交...");
					return true;
				}else{
					alert("表单数据不合法！");
					return false;
				}
			},
			form5 : function(args){
				alert("第一个参数是："+args[1]);
				return true;
			},
			form6 : function(args){
				alert("第一个参数是："+args[1]+"\r\n第二个参数是："+args[2]);
				return true;
			},
			form7 : function(args){
				alert("第一个参数是："+args[1]+"\r\n第二个参数是："+args[2]+"\r\n第三个参数是："+args[3]);
				return true;
			}
		};
		/**
		 * 【表单】
		 * 处理ajax反馈数据的过程集合
		 */
		$AiFormDealResults = {
			//示例【指令名称 : function(data){处理过程。。。}】
			aiFormLogin : function(data){
				alert("现在处理的是aiFormLogin指令反馈的数据："+data);
			},
			wocao : function(data){
				alert("现在处理的是wocao指令反馈的数据："+data);
			},
			help : function(data){
				alert("现在处理的是help指令反馈的数据（并发生AJAX页面跳转）："+data);
				var doc = document.open("text/html","replace");
				doc.write(data);
				doc.close();
			},
			shua : function(data){
				var inter;
				var i=0;
				$AiFormDealResults.shua.post = function(j){
					$.post("?x=shua",function(data){
						//alert(data);
						$("#shua .ai-submit").val("正在刷第 " + j + "个");
					});
					clearInterval(inter);
				};
				while(++i <= 2){
					alert(i);
					inter = setInterval('$AiFormDealResults.shua.post('+i+')', 1000);
				}
			},
			form5 : function(data){
				alert("form5反馈的数据：\r\n"+data);
			},
			form6 : function(data){
				alert("form6反馈的数据：\r\n"+data);
			},
			form7 : function(data){
				alert("form7反馈的数据：\r\n"+data);
			}
		};
		/**
		 * 【ai-ajax控件】
		 * 绑定ajax通信前验证过程
		 */
		$AiAjaxValidates = {
			//示例【指令名称 : function( ){处理过程。。。}】
			abc : function(){
				alert("ai-ajax控件 abc 验证通过！");
				if(true)
					return true;
				else
					return false;
			},
			abc_1 : function(){
				alert("ai-ajax控件 abc_1 验证通过！");
				if(true)
					return true;
				else
					return false;
			},
			abc_2 : function(){
				alert("ai-ajax控件 abc_2 验证通过！");
				if(true)
					return true;
				else
					return false;
			}
		};

		/**
		 * 【ai-ajax控件】
		 * 绑定ajax通信后处理反馈数据的过程
		 */
		$AiAjaxDealResults = {
			abc : function(data){
				alert("ai-ajax控件 abc 通信结果："+data);
			},
			abc_1 : function(data){
				alert("ai-ajax控件abc_1 通信结果："+data);
			},
			abc_2 : function(data){
				alert("ai-ajax控件abc_2 通信结果："+data);
			}
		};
		//为一类自己指定的控件绑定统一的提交前验证过程和ajax后数据处理过程
		$AiForm.bindCommonAjaxValidateAndFeedback(
				$("[class~=myselector1]"),
				function(){
					alert('$("[class~=myselector1]")所选控件的验证过程');
					return true;//使用false则视为验证不通过
				},
				function(data){
					alert("统一处理ajax反馈的数据："+data);
				}
		);
		//将表单验证和数据处理方法集合 绑定到 对应表单
		$AiForm.aiFormBindFeedBack( $AiFormValidates, $AiFormDealResults );
		$AiForm.aiAjaxBindFeedBack( $AiAjaxValidates, $AiAjaxDealResults );
		/**
		 * 普通控件部分
		 */
		//使类为ai-skip的普通控件具有跳转功能
		$AiForm.replaceDocWithClickAiSkip();
	});
</script>
</head>


<body>
=================================控件测试============================<br/>
1、ai-skip控件测试<br>
<button class="ai-skip" shell="index">点击跳转</button><br>
说明：class="ai-skip" id|shell="指令或不带'-'的普通模板路径值"<br>
由于一个页面中不只一个控件访问同一个指令，因此，使用id会造成第一个使用该id的控件之后的所有控件都无法失去AI效果。<br>
故强烈建议：使用shell属性来指定所访问的指令<br>
<br>
2、ai-ajax控件测试<br>
<button id="abc" class="ai-ajax" shell="abc" params="1|2|3">点击AI-AJAX控件 abc</button><br>
<button id="abc_1" class="ai-ajax" shell="abc" params="1|2|3">点击AI-AJAX控件 abc_1</button><br>
<button id="abc_2" class="ai-ajax" shell="abc" params="1|2|3">点击AI-AJAX控件 abc_2</button><br>
说明：class="ai-ajax" id|shell="指令或不带'-'的普通模板路径值（用路径值，则params参数将失效）" params="参数1|参数2|参数3"<br>
由于一个页面中不只一个控件访问同一个指令，因此，使用id会造成第一个使用该id的控件之后的所有控件都无法失去AI效果。<br>
故强烈建议：使用shell属性来指定所访问的指令<br>
3、为自己指定的控件绑定统一提交前验证过程和ajax通信后数据处理过程<br>
<button class="myselector1" shell="abc" params="1|2|3">$("[class~=myselector1]")所选控件</button><br>
<a href="#" class="myselector1" shell="bcd" params="1|2" >A标签</a><br>
<button class="myselector1" shell="fgh">范德萨范德萨</button>
<br>
=================================表单测试============================<br/>
表单一（aiFormLogin）：
<form shell="aiFormLogin" class="form-inline ai-form">
	<!-- 后续参数集合：ai-args-n形式，必须连续且从1开始，“断链”处将放弃后续参数的解析，注意注意！ -->
	<input type="text" class="ai-args-1" value="Oreki" >
	<input type="password" class="ai-args-2" value="orikiltre" >
	<!-- 提交器：type绝对不能使用submit -->
	<input type="button" class="ai-submit" value="登录">
</form>
说明：form的id推荐用shell代替。

表单二（wocao）：
<form shell="wocao" class="ai-form">
	<input type="text" class="ai-args-1"value="Oreki" >
	<input type="password" class="ai-args-2" value="orikiltre" >
	<input type="button" class="ai-submit" value="登录">
</form>
表单三（help）：
<form shell="help" class="ai-form" style="margin-left:307px;">
	<input type="button" class="ai-submit" value="登录">
</form>
表单四：刷BBS好友（shua）
<form shell="shua" class="ai-form" style="margin-left:307px;">
	<input type="button" class="ai-submit" value="刷！！">
</form>
表单五、六、七：都访问同一个指令，但各个表单的验证过程和反馈数据过程不同
<form id="form5" shell="form" class="ai-form">
	<input type="text" class="ai-args-1" value="这是表单5的参数1" >
	<input type="button" class="ai-submit" value="表单5">
</form>
<form id="form6" shell="form" class="ai-form">
	<input type="text" class="ai-args-1" value="这是表单6的参数1" >
	<input type="text" class="ai-args-2" value="这是表单6的参数2" >
	<input type="button" class="ai-submit" value="表单6">
</form>
<form id="form7" shell="form" class="ai-form">
	<input type="text" class="ai-args-1" value="这是表单7的参数1" >
	<input type="text" class="ai-args-2" value="这是表单7的参数2" >
	<input type="text" class="ai-args-3" value="这是表单7的参数3" >
	<input type="button" class="ai-submit" value="表单7">
</form>
</body>
</html>