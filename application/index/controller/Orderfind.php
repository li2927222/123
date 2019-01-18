<?php 
namespace app\index\controller;
use app\index\controller\Base;
use think\Controller;
use think\Db;
use think\Session;
use app\index\model\Dodo;

class Orderfind extends Base
{
	public function orderfind(){
		//查询当前账号级别自身的订单
		$self = Db::table('order_level_v')->field('order_id,order_name,user_company,area,info_origin')->distinct(true)->where('area_id',Session::get('area_id'))->select();
		//如果权限等级为1则查询全部权限等级大于1的订单
		//否则 查询上级区域id为当前区域id的（即本区域的下级区域） 区域订单
		if(Session::get('level')=='1'){
			$child = Db::table('order_level_v')->field('order_id,order_name,user_company,area,info_origin')->distinct(true)->where('level','>',Session::get('level'))->select();
		}else{
			$child = Db::table('order_level_v')->field('order_id,order_name,user_company,area,info_origin')->distinct(true)->where('superior_id',Session::get('area_id'))->select();
		}

		$this->assign('self_info',$self);
		$this->assign('child_info',$child);
		return view('orderfind');
	}
	//NND 这个问题想了两天终于解决了
	//这个方法是用来把传递过来的查询条件赋值给SESSION 然后重定向到search_result方法 目的是为了提交后重定向路由防止在刷新或者后退时提示表单重复提交
	public function search(){
		Session::set('order_id_info',$_POST['order_id_info']);
		Session::set('start_time',$_POST['start_time']);
		Session::set('end_time',$_POST['end_time']);
		Session::set('keyword',$_POST['keyword']);
		return redirect('search_result');
	}


	//查询数据并输出到查询结果页面
	//说实话这段我是真的不想加注释。。。头发掉了一大把了
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
			return redirect('orderfind');
		}
		if($start_time==''){
			$start_time = date('Y-m-d',strtotime('1900-1-01'));
		}
		if($end_time==''){
			$end_time = date('Y-m-d',strtotime('+10 year'));
		}
		
		$self = Db::table('order_level_v')
		->field('order_id,order_name,user_company,area,info_origin')
		->distinct(true)
		->where('order_id','like','%'.$order_id_info.'%')
		->where('time_landing','>=',$start_time)
		->where('time_landing','<=',$end_time)
		->where('order_name','like','%'.$keyword.'%')
		->where('area_id',Session::get('area_id'))
		->select();
		if(Session::get('level')=='1'){
			$child = Db::table('order_level_v')
			->field('order_id,order_name,user_company,area,info_origin')
			->distinct(true)
			->where('level','>',Session::get('level'))
			->where('order_id','like','%'.$order_id_info.'%')
			->where('time_landing','>=',$start_time)
			->where('time_landing','<=',$end_time)
			->where('order_name','like','%'.$keyword.'%')
			->select();
		}else{
			$child = Db::table('order_level_v')
			->field('order_id,order_name,user_company,area,info_origin')
			->distinct(true)
			->where('superior_id',Session::get('area_id'))
			->where('order_id','like','%'.$order_id_info.'%')
			->where('time_landing','>=',$start_time)
			->where('time_landing','<=',$end_time)
			->where('order_name','like','%'.$keyword.'%')
			->select();
		}
		// $arr = [
		// 	'order_id_info'=>$order_id_info,
		// 	'start_time'=>$start_time,
		// 	'end_time'=>$end_time,
		// 	'keyword'=>$keyword,
		// ];
		// $this->assign('search_info',$arr);
		$this->assign('self_info',$self);
		$this->assign('child_info',$child);
		return $this->fetch('orderfind');
		
	}

	public function checkorder(){
		$order_id = input('get.id');
		$order_info = Db::table('order_level_v')->where('order_id',$order_id)->select();
		$linkman = Db::table('linkman_info')->where('order_id',$order_id)->select();
		$booster = Db::table('booster_info')->where('order_id',$order_id)->select();
		// print_r($order_info);
		// exit();

		$this->assign('linkman',$linkman);
		$this->assign('booster',$booster);
		$this->assign('order_info',$order_info);
		return view('checkorder');
	}

	//修改订单方法
	public function amendorder(){
		//根据传过来的订单id查询订单信息并输出
		$order_id = input('get.id');
		//主订单信息查询
		$order_info = Db::table('order_level_v')->where('order_id',$order_id)->select();
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
		return view('amendorder');
	}
	//修改订单提交后方法
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

		// 测量phone数组长度，确定有多少个 手机型号
		$phone_count = sizeof($_POST['phone']);
		for($i=0;$i<$phone_count;$i++){
			$data_phone = [
				'order_id' => input('order_id'),
				'batch' => $_POST['batch'][$i],
				'WaresID' => $_POST['phone'][$i],
				'phone_count' => $_POST['phone_count'][$i],
				'phone_price' => $_POST['phone_price'][$i],
				'phone_cost' => $_POST['phone_cost'][$i],
				'is_operator' => $_POST['is_operator'][$i],
				'combinedbag' => $_POST['combinedbag'][$i],
				'competitor' => $_POST['competitor'][$i],
				'usage' => $_POST['usage'][$i],
				'time_landing' => $_POST['time_landing'][$i],
				'time_exchange' => $_POST['time_exchange'][$i],
				
			];
			$order_id = input('order_id');
			$dbname = "child_order";
			$flag = 0;
			$do = (new Dodo)->dodo($_POST['do_phone'][$i],$data_phone,$dbname,$_POST['phone_id'][$i]);
			if($do){
				$flag += 1;
				echo "<p style='color:green; text-align:center;'> ID为".$_POST['phone_id'][$i]."的数据操作成功！";	
			}else{
				$flag += -1000;
				echo "<p style='color:red; text-align:center;'> ID为".$_POST['phone_id'][$i]."的数据操作失败！";	
			}
			

		}//for
			$modify = Db::table('child_order')->field('modify_number')->where('order_id',input('order_id'))->find();
			// var_dump($modify);
			// exit();
			if($modify['modify_number']>0 && $flag >0){
				// $modify_number = $modify['modify_number'] - 1;
				Db::execute("UPDATE child_order SET modify_number = modify_number-1 WHERE order_id = '$order_id'");
			}else{
				echo "<hr><p style='color:red; text-align:center;'>数据操作失败！可修改剩余次数不足";
			}
	}//SubmitAmend()

}//class

