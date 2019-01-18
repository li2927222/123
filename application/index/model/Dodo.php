<?php 
namespace app\index\model;
use think\Loader;
use think\Model;
use think\Validate;
use think\Db;

class Dodo extends Model
{
	public function dodo($val,$data,$dbname,$id)
	{
		switch ($val) {
			case '0':
				return true;
				break;
			case '1':
			echo "删除操作";
				if(Db::table("$dbname")->where('id',$id)->delete())
				{
					if(Db::table("child_order")->where('order_id',$data['order_id'])->find()){

					}else{
						Db::table('order_base')->where('order_id',$data['order_id'])->update(['has_child'=>0]);
					}
					return true;
				}else{
					return false;
				}
				
				break;
			case '2':
			echo "修改操作";
				if(Db::table("$dbname")->where('id',$id)->update($data))
				{	
					return true;
				}else{
					return false;
				}
				
				break;
			case '3':
			echo "新增操作";
				if(Db::table("$dbname")->insert($data))
				{
					return true;
				}else{
					return false;
				}
				
				break;
			default:
				# code...
				break;
		}//switch
	}//dodo()

	
}//class