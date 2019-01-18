<?php 
namespace app\index\controller;
use app\index\controller\Base;
use think\Controller;
use think\Db;
use think\Session;

class Adduser extends Base {
	//查询二级 市城市
	public function adduser() {
		$level = $_POST['level'];
		if(Session::get('area_id')=='600001'){
			$rel = Db::table('area_area')->where('level',$level)->select();
		}
		// else{
		// 	$rel = Db::table('area_area')->where('level',$level)->where('area_id',Session::get('area_id'))->select();
		// }
		
		echo json_encode($rel);
	}
	//查询对应二级的三级门店
	// public function area3_info(){
	// 	$area2 = $_POST['area2'];
	// 	$rel = Db::table('area_area')->where('superior_id',$area2)->select();
	// 	echo json_encode($rel);
	// }
	//添加账号重复校验
	public function user_check(){
		$user = $_POST['user'];
		$rel = Db::table('user_info')->where('user',$user)->select();
		if($rel){
			$arr['val']=0;
		}else{
			$arr['val']=1;
		}
		echo json_encode($arr);
	}

	//提交注册信息到数据库
	public function submit(){
		$data = [
			'user' => input('user'),
			'pwd' => md5(input('pwd1')),
			'phone' => input('phone'),
			'level' => input('area_level'),
			'user_name' => input('user_name'),
		];
		$data1 = [
			'user' => input('user'),
			'area_id' => input('area_id'),
			'area' => input('area'),
		];
		$sql1 = Db::table('user_info') -> insert($data);
		$sql2 =Db::table('user_area') -> insert($data1);
		if($sql1 && $sql2){
			$arr['val']=1;
		}else{
			$arr['val']='注册失败';
		}
		echo json_encode($arr);
	}

	//修改账号是否禁用
	public function is_ban(){
		$user = input('user');
		$ban = input('ban');
		if($sql= Db::table("user_info")->where('user',$user)->update(['ban'=>$ban])){
			$rel['val']=1;
		}else{
			$rel['val']=0;
		}
		echo json_encode($rel);
	}
}