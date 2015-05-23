jQuery(document).ready(function($) {
	$("#area").css({'heigth': '640px'});
    var $json = {shell:'URL指令', afprm:'后续参数（如果有）'};
	//点击清空
	$("#shell,#afprm").click(function(e) {
		if( $json.shell == $(this).val() || $json.afprm == $(this).val() ){
			$(this).val('');
		}
    });
	//离开时候重新填充提示（为空时）
	$("#shell,#afprm").focusout(function(e) {
        if('' == $(this).val()){
			$(this).val( $json[this.id] );
		}
    });

	//测试URL指令的响应
	else_evt = function(){
		shell = encodeURIComponent( $("#shell").val() );
		afprm = encodeURIComponent($("#afprm").val());
		if(''==shell&&''==afprm){
			$('div#area').html('');
			return;
		}
		$.post('./?xxx=' + shell + ( ''==afprm?'':'|' ) + afprm, function(data){
			$("div#area").html(data);
		});
	};
	$("#shell,#afprm").keyup( else_evt );
	//输入指令时，清空右边参数
	$("#shell").keypress( function(){
		$("#afprm").val('');
	} );
});