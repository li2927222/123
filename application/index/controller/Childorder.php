<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Controller;
use think\Db;
use think\Session;

class Childorder extends Base
{
	//获取当前子订单数据并存入数据库
	public function childorder(){

		//获取落地时间 $landtime 和 预计到期时间$endtime
	 	$landtime = $_POST['time-landing'];
	 	$months = $_POST['time-exchange'];
	 	$exchangetime = date('Y-m-d',strtotime("+".$months."months",strtotime($landtime)));

	 	//查询当前订单是否存在批次 如果存在批次加1 如果不存在批次为1
	 	$sql_batch = Db::table('child_order')->field('batch')->where('order_id',Session::get('order_id'))->find();
	 	if(!$sql_batch){
	 		$batch = 1;
	 	}else{
	 		$batch = $sql_batch['batch']+1;
	 	}

	 	//计算手机型号数组长度 获取当前的订单数据条数
	 	$count = sizeof($_POST['phone-type']);
	 	
	 	//循环插入数据
	 	for($i=0;$i<$count;$i++){
	 		$data_order = [
	 			'order_id'=>Session::get('order_id'),
	 			'batch'=>$batch,
	 			'WaresID'=>$_POST['phone-type'][$i],
	 			'is_operator'=>$_POST['is_operator'][$i],
	 			'phone_count'=>$_POST['count'][$i],
	 			'phone_price'=>$_POST['price'][$i],
	 			'phone_cost'=>$_POST['cost'][$i],
	 			'competitor'=>$_POST['competitor'][$i],
	 			'combinedbag'=>$_POST['combinedbag'][$i],
	 			'winning_factors'=>$_POST['winning-factors'][$i],
	 			'usage'=>$_POST['usage'],
	 			'time_landing'=>$landtime,
	 			'time_exchange'=>$exchangetime,
	 		];
	 		$insert = Db::table('child_order')->insert($data_order);
	 	}
	 	$update = Db::table('order_base')->where('order_id',Session::get('order_id'))->update(['has_child'=>1]);
	 	if($insert||$update){
	 		$this->success("提交成功！",'index/main');
	 	}
	}// class childorder
}//控制器