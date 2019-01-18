$(function() {
	$(document).keydown(function(event){ 
		if(event.keyCode==13){ 
			$("#login_bt").click(); 
		}
	});
	//用户名为空校验
	$('#user').blur(function() {
		var user = $('#user').val();
		if (user == "") {
			$("#warning").html("用户名不能为空").css('color', 'red');
		} else {
			$("#warning").html("");
		}
	});
	//登录按钮点击事件
	$('#login_bt').click(function() {
		var user = $('#user').val();
		var pwd = $('#pwd').val();
		if(user==""&&pwd==""){
			alert("账号或密码不能为空！")
			return 0;
		}
		$.ajax({
			type: 'post',
			url: "index/index/login",
			dataType: 'json',
			data: {
				'user': user,
				'pwd': pwd
			},
			success: function(data) {
				if (data.val == 1) {
					$("#warning").html("登陆成功").css('color', 'green');
					window.location.href = "index/index/CRM";
				} else if (data.val == 0) {
					$("#warning").html("账号密码不匹配").css('color', 'red');
				} else if (data.val == 3) {
					$("#warning").html("用户不存在").css('color', 'red');
				} else {
					$("#warning").html("未知错误").css('color', 'red');
				}
			},
			erro: function() {
				alert("erro");
			}
		});
	});
	//注册按钮点击跳转页面
	$('#regist_bt').click(function() {
		var user = $("#user").val();
		var pwd = $("#pwd").val();
		$.ajax({
			type: 'post',
			url: 'php/admin_check.php',
			dataType: 'json',
			data: {
				'user': user,
				'pwd': pwd
			},
			success: function(data) {
				if (data.val == 1) {
					$("#warning").html("登陆成功").css('color', 'green');
					window.location.href = 'regist.html';
				} else if (data.val == 0) {
					$("#warning").html("账号密码不匹配").css('color', 'red');
				} else if (data.val == 3) {
					$("#warning").html("用户不存在").css('color', 'red');
				} else {
					$("#warning").html("未知错误").css('color', 'red');
				}
			},
			erro: function() {
				alert("erro");
			}
		});
		
	});
});