//点击添加联系人 添加一排新数据
$("#add_linkman").click(function(){
	let linkman_info = '<tr><td class="success">联系人姓名:</td>'+
				'<td ><input type="text" class="form-control" name="linkman-name[]"></td>'+
				'<td class=" success">性别：</td>'+
				'<td >'+
				'<select name="linkman-sex[]" class="btn btn-default">'+
				'<option value="男">男</option>'+
				'<option value="女">女</option>'+
				'</select>'+
				'<td class="success">电话：</td>'+
				'<td ><input type="text" class="form-control" name="linkman-phone[]"></td>'+
				'<td class="success">岗位:</td>'+
				'<td ><input type="text" class="form-control" name="linkman-position[]"></td>'+
				'<td class="success">职能:</td>'+
				'<td ><input type="text" class="form-control" name="linkman-function[]"></td>'+
				'<td class="success">作用:</td>'+
				'<td ><input type="text" class="form-control" name="linkman-effect[]"></td>'+
				'<td><a href="#" class="del_linkman">x删除</a></td></tr>';
	$("#linkman").after(linkman_info);

	//动态添加 “删除” 超链接a标签的 点击事件
	$(".del_linkman").click(function(){
		$(this).parent().parent().remove();
	});
});

//点击添加助推联系人
$("#add_booster").click(function(){
	let booster_info = '<tr><td class="danger">联系人姓名:</td>'+
				'<td ><input type="text" class="form-control" name="booster-name[]"></td>'+
				'<td class=" danger">性别：</td>'+
				'<td>'+
				'<select name="booster-sex[]" class="btn btn-default">'+
				'<option value="男">男</option>'+
				'<option value="女">女</option>'+
				'</select>'+
				'<td class="danger">电话：</td>'+
				'<td ><input type="text" class="form-control" name="booster-phone[]"></td>'+
				'<td class="danger">岗位:</td>'+
				'<td ><input type="text" class="form-control" name="booster-position[]"></td>'+
				'<td class="danger">职能:</td>'+
				'<td ><input type="text" class="form-control" name="booster-function[]"></td>'+
				'<td class="danger">作用:</td>'+
				'<td ><input type="text" class="form-control" name="booster-effect[]"></td>'+
				'<td><a href="#" class="del_booster">x删除</a></td></tr>';
	$("#booster").after(booster_info);
	//动态添加 “删除” 超链接a标签的 点击事件
	$(".del_booster").click(function(){
		$(this).parent().parent().remove();
	});
});