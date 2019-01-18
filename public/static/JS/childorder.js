$(".is_operator").change(function(){
	if($(this).val()=="否"){
		$(this).parent().next().next().children().val('无');
		$(this).parent().next().next().children().attr("readonly","readonly");
	}else{
		$(this).parent().next().next().children().val('');
		$(this).parent().next().next().children().removeAttr("readonly");
	}
});
//提交为空校验 及提交后按钮失效禁止重复提交
$("#sub").click(function(){
	var $btn = $(this).button('loading');
		var flag = 0;
		$(".form-control").each(function(){
			if($(this).val()==''){
				flag = 1;
			}
		});
		if(flag==1){
			alert("请填写完整信息");
			$btn.button('reset');
			return 0;
		}
		$("form").submit();
});