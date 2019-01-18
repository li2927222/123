$(function(){
	$("#bt_select").click(function(){
		var order_id_info = $("#order_id_info").val();
		var start_time = $("#start_time").val();
		var end_time = $("#end_time").val();
		var keyword = $("#keyword").val();
		
		$.ajax({
			type:'post',
			url:'../Orderfind/search_result',
			dataType:'json',
			data:{
				'order_id_info':order_id_info,
				'start_time':start_time,
				'end_time':end_time,
				'keyword':keyword,
			},
			success:function(data){
				
			},
		});//ajax
	});//bt_select click function
});//function