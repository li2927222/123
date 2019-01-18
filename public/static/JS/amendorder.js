$(function(){
	//计算初始页面 linkman表单的数据长度 确保在删除时不会被误删
	var len_linkman = $("#tab-linkman tr").length;
	var len_booster = $("#tab-booster tr").length;
	var len_phone = $("#tab-phone tr").length;
	//点击提交，loading防止多次提交
	$("#submit").click(function(){
		var $btn = $(this).button('loading');
		$("#a").submit();
	});
	//点击重置按钮刷新该界面
	$("#cancel").click(function(){
		window.location.reload();
	});
	//点击添加额外的linkman
	$("#add-linkman").click(function(){
		var add_info = '<tr >'+
					'<td><input type="text" name="linkman_id[]" readonly="readonly"></td>'+
					'<td><input type="text" name="linkman_name[]"></td>'+
					'<td><input type="text" name="linkman_sex[]"></td>'+
					'<td><input type="text" name="linkman_phone[]" maxlength="11"></td>'+
					'<td><input type="text" name="linkman_position[]" maxlength="200"></td>'+
					'<td><input type="text" name="linkman_function[]" maxlength="200"></td>'+
					'<td><input type="text" name="linkman_effect[]" maxlength="200"></td>'+
					'<td>'+
						'<select name="do_linkman[]" id="" class="btn btn-default do">'+
							'<option value="0">无</option>'+
							'<option value="1">删除</option>'+
							'<option value="2">修改</option>'+
							'<option value="3">增加</option>'+
						'</select>'+
					'</td>'+
				'</tr>';
		$("#tab-linkman").append(add_info);
	});
	//点击删除新增的linkman空白栏
	$("#del-linkman").click(function(){
		//判断当前linkman表格中tr的行数是否大于页面初始化时的行数
		//如果大于则为手动添加过的行数可以删除
		//否则视为没有手动添加过，提示不能删除原始数据
		if($("#tab-linkman tr").length > len_linkman){
			$("#tab-linkman tr:last").remove();
		}else{
			alert("不能删除已有数据");
		}
	});

	//执行与Linkman相同的操作
	$("#add-booster").click(function(){
	var add_info = '<tr >'+
				'<td><input type="text" name="booster_id[]" readonly="readonly"></td>'+
				'<td><input type="text" name="booster_name[]"></td>'+
				'<td><input type="text" name="booster_sex[]"></td>'+
				'<td><input type="text" name="booster_phone[]" maxlength="11"></td>'+
				'<td><input type="text" name="booster_position[]" maxlength="200"></td>'+
				'<td><input type="text" name="booster_function[]" maxlength="200"></td>'+
				'<td><input type="text" name="booster_effect[]" maxlength="200"></td>'+
				'<td>'+
					'<select name="do_booster[]" id="" class="btn btn-default do">'+
						'<option value="0">无</option>'+
						'<option value="1">删除</option>'+
						'<option value="2">修改</option>'+
						'<option value="3">增加</option>'+
					'</select>'+
				'</td>'+
			'</tr>';
	$("#tab-booster").append(add_info);
	});
	$("#del-booster").click(function(){
		if($("#tab-booster tr").length > len_booster){
			$("#tab-booster tr:last").remove();
		}else{
			alert("不能删除已有数据");
		}
	});
});
