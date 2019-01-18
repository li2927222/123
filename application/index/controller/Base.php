<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;

class Base extends Controller
{
	 protected $beforeActionList = [
       'gosession' =>  ['except'=>'index,login'],    //tp前置方法，不管执行那个方法，都要先执行gosession ， 除了login方法
       'menu' => ['except'=>'index,login'],
    ];

    //定义前置控制器
    public function gosession()
    {   
        $user = Session::get('user');
        $ban = Db::table('user_info')->where('user',$user)->find();
        if(!$user)
        {
            $this->error('请先登录','../index');
        }
        if($ban['ban']==1){
            $this->error('本账号权限已被禁用，请联系管理员！','../index');
        }
    }

    public function menu(){
		$user = Session::get('user');
        $data = Db::table('user_info')->where('user',$user)->find();
        $this->assign('user',$data);
        return view('public/menu');
	}
}