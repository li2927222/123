<?php 
namespace app\index\controller;
use app\index\controller\Base;
use think\Controller;
use think\Db;
use think\Session;
use app\index\model\Dodo;

Class Nochild extends Base
{

	//查询没有子订单的订单
	public function NoChildOrder(){
		//查询当前账号级别自身的订单
		$self = Db::table('order_base')
		->field('order_id,order_name,user_company,area,info_origin')
		->distinct(true)
		// ->order('order_id desc')
		->where('area_id',Session::get('area_id'))
		->where('has_child','0')
		->select();
		//如果权限等级为1则查询全部权限等级大于1的订单
		//否则 查询上级区域id为当前区域id的（即本区域的下级区域） 区域订单
		if(Session::get('level')=='1'){
			$child = Db::table('order_base')
			->field('order_id,order_name,user_company,area,info_origin')
			->distinct(true)
			->order('order_id desc')
			->where('level','>',Session::get('level'))
			->where('has_child','0')
			->select();
		}else{
			$child = Db::table('order_base_v')
			->field('order_id,order_name,user_company,area,info_origin')
			->distinct(true)
			->order('order_id desc')
			->where('superior_id',Session::get('area_id'))
			->where('has_child','0')
			->select();
		}
		// print_r($child);
		$this->assign('self_info',$self);
		$this->assign('child_info',$child);
		return view('nochildorder');
	}
	//查看订单
	public function checkorder(){
		$order_id = input('get.id');
		$order_info = Db::table('order_base_v')->where('order_id',$order_id)->select();
		$linkman = Db::table('linkman_info')->where('order_id',$order_id)->select();
		$booster = Db::table('booster_info')->where('order_id',$order_id)->select();
		// print_r($order_info);
		// exit();

		$this->assign('linkman',$linkman);
		$this->assign('booster',$booster);
		$this->assign('order_info',$order_info);
		return view('nochild_checkorder');
	}

	//修改订单方法
	public function amendorder(){
		//根据传过来的订单id查询订单信息并输出
		$order_id = input('get.id');
		//主订单信息查询
		$order_info = Db::table('order_base_v')->where('order_id',$order_id)->select();
		//联系人信息查询
		$linkman = Db::table('linkman_info')->where('order_id',$order_id)->select();
		//助推联系人信息查询
		$booster = Db::table('booster_info')->where('order_id',$order_id)->select();

        //手机相关信息查询
        $data_phone = Db::table('wWaresData')->where('Identifier','-1')->select();

        $this->assign('phone',$data_phone);
		$this->assign('linkman',$linkman);
		$this->assign('booster',$booster);
		$this->assign('order_info',$order_info);
		return view('amendnochild');
	}


	public function search(){
		Session::set('order_id_info',$_POST['order_id_info']);
		Session::set('start_time',$_POST['start_time']);
		Session::set('end_time',$_POST['end_time']);
		Session::set('keyword',$_POST['keyword']);
		return redirect('search_result');
	}


	//查询数据并输出到查询结果页面

	public function search_result(){

		$order_id_info = Session::get('order_id_info');
		$start_time = Session::get('start_time');
		$end_time = Session::get('end_time');
		$keyword = Session::get('keyword');
		
		// print_r($_POST);
		//为什么要写下面这段代码？
		//因为如果他什么都不填直接查找那就是查询全部（有些没有子订单的不会被查出来，因为没有子订单就没有开始结束时间）
		//而如果只是时间没有填其他的填了 那就按有时间来查
		if($order_id_info=='' && $start_time=='' && $end_time=='' && $keyword==''){
			return redirect('NoChildOrder');
		}
		if($start_time==''){
			$start_time = date('Y-m-d',strtotime('1900-1-01'));
		}
		if($end_time==''){
			$end_time = date('Y-m-d',strtotime('+10 year'));
		}
		
		$self = Db::table('order_base')
		->field('order_id,order_name,user_company,area,info_origin')
		->distinct(true)
		->where('order_id','like','%'.$order_id_info.'%')
		->where('order_creat_time','>=',$start_time)
		->where('order_creat_time','<=',$end_time)
		->where('order_name','like','%'.$keyword.'%')
		->where('area_id',Session::get('area_id'))
		->where('has_child','0')
		->order('order_id desc')
		->select();
		if(Session::get('level')=='1'){
			$child = Db::table('order_base')
			->field('order_id,order_name,user_company,area,info_origin')
			->distinct(true)
			->where('level','>',Session::get('level'))
			->where('order_id','like','%'.$order_id_info.'%')
			->where('order_creat_time','>=',$start_time)
			->where('order_creat_time','<=',$end_time)
			->where('order_name','like','%'.$keyword.'%')
			->where('has_child','0')
			->order('order_id desc')
			->select();
		}else{
			$child = Db::table('order_base')
			->field('order_id,order_name,user_company,area,info_origin')
			->distinct(true)
			->where('superior_id',Session::get('area_id'))
			->where('order_id','like','%'.$order_id_info.'%')
			->where('order_creat_time','>=',$start_time)
			->where('order_creat_time','<=',$end_time)
			->where('order_name','like','%'.$keyword.'%')
			->where('has_child','0')
			->order('order_id desc')
			->select();
		}
		$arr = [
			'order_id_info'=>$order_id_info,
			'start_time'=>$start_time,
			'end_time'=>$end_time,
			'keyword'=>$keyword,
		];
		$this->assign('search_info',$arr);
		$this->assign('self_info',$self);
		$this->assign('child_info',$child);
		return $this->fetch('nochildorder');
		
	}
	public function SubmitAmend(){
		// print_r($_POST);
		// 获取表头相关信息存入数组
		$data_orderbase = [
			'order_id' => input('order_id'),
			'order_name' => input('order_name'),
			'user_company' => input('user_company'),
			'info_origin' => input('info_origin'),
			'info_remark' => input('info_remark'),
			'area_id' => Session::get('area_id'),
			'area' => input('area'),
			'operator' => Session::get('user'),
		];
		// print_r($_POST);
		// 更新数据库表头信息
		Db::table('order_base')->where('order_id',input('order_id'))->update($data_orderbase);

		// 测量Linkman数组长度，确定有多少个linkman
		$linkman_count = sizeof($_POST['linkman_name']);
		for($i=0;$i<$linkman_count;$i++){
			$data_linkman = [
				'order_id' => input('order_id'),
				'name' => $_POST['linkman_name'][$i],
				'sex' => $_POST['linkman_sex'][$i],
				'phone' => $_POST['linkman_phone'][$i],
				'position' => $_POST['linkman_position'][$i],
				'function' => $_POST['linkman_function'][$i],
				'effect' => $_POST['linkman_effect'][$i],
			];
			//设置数据表名称
			$dbname = "linkman_info";
			//在Model中封装一个类 进行判断是删除/修改/增加 文件位于index/model/Dodo.php
			$do = (new Dodo)->dodo($_POST['do_linkman'][$i],$data_linkman,$dbname,$_POST['linkman_id'][$i]);
			if($do){
				echo "<p style='color:green; text-align:center;'>".$_POST['linkman_name'][$i]."的数据操作成功！<br>";
			}else{
				echo $_POST['linkman_name'][$i]."的数据操作失败！<br>";
			}
		}//for
		
		// 测量Booster数组长度，确定有多少个Booster
		$booster_count = sizeof($_POST['booster_name']);
		for($i=0;$i<$booster_count;$i++){
			$data_booster = [
				'order_id' => input('order_id'),
				'name' => $_POST['booster_name'][$i],
				'sex' => $_POST['booster_sex'][$i],
				'phone' => $_POST['booster_phone'][$i],
				'position' => $_POST['booster_position'][$i],
				'function' => $_POST['booster_function'][$i],
				'effect' => $_POST['booster_effect'][$i],
			];
			$dbname = "booster_info";
			$do = (new Dodo)->dodo($_POST['do_booster'][$i],$data_booster,$dbname,$_POST['booster_id'][$i]);
			if($do){
				echo "<p style='color:green; text-align:center;'>".$_POST['booster_name'][$i]."的数据操作成功！";
			}else{
				echo $_POST['booster_name'][$i]."的数据操作失败！<br>";
			}
		}//for
	}// public function SubmitAmend()
	public function add_child(){
		   //订单表头信息
        $order_base = Db::table('order_base')->where('order_id',input('get.id'))->find();
        Session::set('order_id',input('get.id'));
        //手机相关信息
        $data_phone = Db::table('wWaresData')->where('Identifier','-1')->select();
        $this->assign('data_phone',$data_phone);
        $this->assign('order',$order_base);
        return view('childorder');
	}
	public function childorder(){
		//获取落地时间 $landtime 和 预计到期时间$endtime
	 	$landtime = $_POST['time-landing'];
	 	$months = $_POST['time-exchange'];
	
	 	$exchangetime = date('Y-m-d',strtotime("+".$months."months",strtotime($landtime)));
	 	//查询当前订单是否存在批次 如果存在批次加1 如果不存在批次为1
	 	$sql_batch = Db::table('child_order')->field('batch')->where('order_id',input('get.id'))->find();
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
	}// function childorder
}//class