//添加二级三级公司选项和二级选项内容
$("#area_level").change(function(){
	let level = $("#area_level option:selected").val();

	//在重新选择权限等级时清空三级区域内容
	$("#opt-area3").html('');
	$("#opt-area3").append('<option value="0">选择区域</option>');

	if (level==2) {
		$("#area2").css('display','none');
		$("#area3").css('display','none');
		$("#area2").fadeIn(800);
	}
	if (level==3) {
		$("#area2").css('display','none');
		$("#area3").css('display','none');
		$("#area2").fadeIn(800);
		$("#area3").fadeIn(800);
	}
	if (level==0) {
		$("#area2").css('display','none');
		$("#area3").css('display','none');
	}
	$.ajax({
		type: 'post',
		url: '../Adduser/adduser',
		dataType: 'json',
		data: {
			'level' : 2,
		},
		success:function(data){
			$("#opt-area2").html('');
			$("#opt-area2").append('<option value="0">选择市公司</option>');
			let length = data.length;
			for(let i=0;i<length;i++){
				$("#opt-area2").append('<option value="'+data[i].area_id+'">'+data[i].area+'</option>');
			}
		},
	});
});

//添加三级公司下拉框内容
$("#opt-area2").change(function(){
	let area2 = $("#opt-area2 option:checked").val();
	$.ajax({
		type:'post',
		url:'../Adduser/area3_info',
		dataType:'json',
		data:{
			'area2':area2,
		},
		success:function(data){
			$("#opt-area3").html('');
			$("#opt-area3").append('<option value="0">选择区域</option>');
			let length = data.length;
			for(let i=0;i<length;i++){
				$("#opt-area3").append('<option value="'+data[i].area_id+'">'+data[i].area+'</option>');
			}
		},
	});
});

//内容不为空校验
$(".form-control").blur(function(){
	let info = $(this).parent().parent().find("td").eq(2);
	if ($(this).val()=='') {
		info.html("内容不能为空！").css('color','red');
	}else{
		info.html("√").css('color','green');
	}
});

//密码重复校验
$("#pwd2").blur(function(){
	let pwd1 = $("#pwd1").val();
	let pwd2 = $("#pwd2").val();
	if(pwd1!=pwd2||pwd2==''){
		$("#warning_pwd2").html("两次密码不一致").css('color','red');
	}else{
		$("#warning_pwd2").html("√").css('color','green');
	}
});

//账号重复验证
$("#user").blur(function(){
	let user = $("#user").val();
	if(user==""){
		$("#warning_user").html('请填写用户账号。').css('color','red');
		return 0 ;
	}
	$.ajax({
		type:'post',
		url:'../Adduser/user_check',
		dataType:'json',
		data:{
			'user':user,
		},
		success:function(data){
			if (data.val==1) {
				$("#warning_user").html('账号可用。').css('color','green');
			}else{
				$("#warning_user").html('账号被占用。').css('color','red');
			}
		},
	});
});
//手机号正则验证
$("#phone").blur(function(){
	 let myreg =/^[1][3,4,5,7,8][0-9]{9}$/;
	 let phone = $("#phone").val();
	 if(myreg.test(phone)){
	 	$("#warning_phone").html("√").css('color','green');
	 }else{
	 	$("#warning_phone").html('手机号格式不正确').css('color','red');;
	 }
});

//提交注册账号数据
$("#bt_submit").click(function(){
	let user = $("#user").val();
	let pwd1 = $("#pwd1").val();
	let pwd2 = $("#pwd2").val();
	let user_name = $("#user_name").val();
	let phone = $("#phone").val();
	let area_level = $("#area_level").val();
	var area_id = '0';
	var area = '';
	//判断是否选择了对应区域
	if(area_level=='2'){
		area_id = $("#opt-area2").val();
		area = $("#opt-area2 option:selected").text();
	}else if(area_level=='3' && $("#opt-area3 option:selected").val()!='0'){
		area_id = $("#opt-area3").val();
		area = $("#opt-area3 option:selected").text();
	}else{
		alert("请选择所属区域");
		return 0;
	}

	//判断是否所有信息都已经填写
	if(user!="" && pwd1!="" && pwd2!="" && user_name!="" &&phone!="" && area_level!=""){

	}else{
		return 0;
	}
	
	$.ajax({
		type:'post',
		url:'../Adduser/submit',
		dataType:'json',
		data:{
			'user':user,
			'pwd1':pwd1,
			'pwd2':pwd2,
			'user_name':user_name,
			'phone':phone,
			'area_level':area_level,
			'area_id':area_id,
			'area':area,
		},
		success:function(data){
			if(data.val==1){
				alert('注册成功');
				window.location.href='CRM';
			}else{
				alert(data.val);
			}
		},
	});
});