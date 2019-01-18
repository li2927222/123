<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Controller;
use think\Db;
use think\Session;
class Index extends Base
{

    //访问登陆界面方法
    public function index()
    {
       return view('index');
    }
    //登陆账号密码校验
    public function login(){ 
        $user = Db::table('v_user_info')->where('user',$_POST['user'])->find();
        $pwd = md5($_POST['pwd']);
        if($user==null){
            $arr['val']=3;
            echo json_encode($arr);
            exit();
        }
        if($pwd==$user['pwd']){
            //将用户账号使用人和区域ID存入SESSION
            Session::set('user',$user['user']);
            Session::set('area_id',$user['area_id']);
            Session::set('ban',$user['ban']);
            Session::set('area',$user['area']);
            Session::set('level',$user['level']);

            //回传val代码为1代表成功
            $arr['val']=1;
            echo json_encode($arr);
        }else{
            //回传val代码为0代表账号密码错误
            $arr['val']=0;
            echo json_encode($arr);
        }
    }

    //访问CRM主界面方法
    public function main(){
        if(Session::get('level')=='1'){
             $data_area = Db::table('area_area')->select();
             $flag = 1;
        }else{
            $data_area = Db::table('v_user_info')->where('user',Session::get('user'))->find();
            $flag = 2;
        }
        $this->assign('flag',$flag);
        $this->assign('data_area',$data_area);
        return view('main');
    }

    //访问新增账户界面
    public function add_user(){
        return view('adduser');
    }

    //访问修改权限界面
    public function levelchange(){
        if(Session::get('level')=='1'){
            $data = Db::table('v_user_info')->where('level','>',1)->select();
        }else{
            $data = Db::table('v_user_info')->where('level','>',Session::get('level'))->where('superior_id',Session::get('area_id'))->select();
    }
        
        
        $this->assign('data',$data);
        return view('levelchange');
    }
    //访问主界面
    public function CRM(){
               //计算今天起15天后的日期
        $now = date('Y-m-d');
        $deadline = date('Y-m-d',strtotime('+15 day'));
        $sql = Db::table('order_level_v')
        ->where('time_exchange','>=',$now)
        ->where('time_exchange','<=',$deadline)
        ->select();

        if($sql){
            echo "<script>alert('有订单即将过期！')</script>";
        }
        
        return view('CRM');
    }

    //新订单提交到info方法，方法内生成订单号并保存表单基本信息
    //渲染详细订单界面
    public function info(){
        // print_r($_POST);
        //计算linkman和booster的长度 从而的出linkman和booster的个数
        $linkman_num = sizeof($_POST['linkman-name']);
        $booster_num = sizeof($_POST['booster-name']);

        //判断最大值对应年份是否与当前年份相同，不同则重置
        $max = Db::table('number_info')->find();
        Db::table('number_info')->where('max',$max['max'])->update(['max'=>($max['max']+1)]);
        $year = date('y');
        if($max['year']!=$year){
            $max['max']=1;
        }
        //生成订单号为 D+年份+max值用0补齐的6位数
        $num = str_pad($max['max'],6,'0',STR_PAD_LEFT);
        $order_id = "D".$year.$num;
        //把订单号存入session 在子订单中添入表头
        Session::set('order_id',$order_id);
        $level = Db::table('area_area')->where('area_id',input('area_id'))->find();
        $data_order = [
            'order_id'=>$order_id,
            'area_id'=>input('area_id'),
            'order_name'=>input('order_name'),
            'user_company'=>input('user_company'),
            'info_origin'=>input('info_origin'),
            'info_remark'=>input('info_remark'),
            'operator'=>Session::get('user'),
            'level' =>$level['level'],
            'area' =>$level['area'],
        ];
        //插入订单基本信息
        Db::table('order_base')->insert($data_order);
        //插入关键人信息
        for($i=0;$i<$linkman_num;$i++){
            $data_linkman = [
                'order_id'=>$order_id,
                'name'=>$_POST["linkman-name"][$i],
                'sex'=>$_POST['linkman-sex'][$i],
                'phone'=>$_POST['linkman-phone'][$i],
                'position'=>$_POST['linkman-position'][$i],
                'function'=>$_POST['linkman-function'][$i],
                'effect'=>$_POST['linkman-effect'][$i],
            ];
            Db::table('linkman_info')->insert($data_linkman);
        }
        //插入助推联系人信息
        for($i=0;$i<$booster_num;$i++){
            $data_booster = [
                'order_id'=>$order_id,
                'name'=>$_POST['booster-name'][$i],
                'sex'=>$_POST['booster-sex'][$i],
                'phone'=>$_POST['booster-phone'][$i],
                'position'=>$_POST['booster-position'][$i],
                'function'=>$_POST['booster-function'][$i],
                'effect'=>$_POST['booster-effect'][$i],
            ];
            Db::table('booster_info')->insert($data_booster);
        }
        //重定向到childorder方法
        return redirect('childorder');
    }
    //子订单页面
    public function childorder(){
        //订单表头信息
        $order_base = Db::table('order_base')->where('order_id',Session::get('order_id'))->find();
        //手机相关信息
        $data_phone = Db::table('wWaresData')->where('Identifier','-1')->select();
        $this->assign('data_phone',$data_phone);
        $this->assign('order',$order_base);
        return view('childorder');
    }

    //即将到期订单页面
    public function dueorder(){

        $deadline = date('Y-m-d',strtotime('+15 day'));
        $now = date('Y-m-d');
        //查询当前账号级别自身的订单
        $self = Db::table('order_level_v')
        ->field('order_id,order_name,user_company,area,info_origin')
        ->distinct(true)
        ->where('time_exchange','>=',$now)
        ->where('time_exchange','<=',$deadline)
        ->where('area_id',Session::get('area_id'))
        ->select();
        //如果权限等级为1则查询全部权限等级大于1的订单
        //否则 查询上级区域id为当前区域id的（即本区域的下级区域） 区域订单
        if(Session::get('level')=='1'){
            $child = Db::table('order_level_v')
            ->field('order_id,order_name,user_company,area,info_origin')
            ->distinct(true)
            ->where('time_exchange','>=',$now)
            ->where('time_exchange','<=',$deadline)
            ->where('level','>',Session::get('level'))
            ->select();
        }else{
            $child = Db::table('order_level_v')
            ->field('order_id,order_name,user_company,area,info_origin')
            ->distinct(true)
            ->where('time_exchange','>=',$now)
            ->where('time_exchange','<=',$deadline)
            ->where('superior_id',Session::get('area_id'))
            ->select();
        }
        
        $this->assign('self_info',$self);
        $this->assign('child_info',$child);
        return view('Index/dueorder');
    }

    //查看历史订单界面
    public function backorder(){
        $now = date('Y-m-d');

         $self = Db::table('order_level_v')
        ->field('order_id,order_name,user_company,area,info_origin')
        ->distinct(true)
        ->where('time_exchange','<=',$now)
        ->where('area_id',Session::get('area_id'))
        ->select();

        //如果权限等级为1则查询全部权限等级大于1的订单
        //否则 查询上级区域id为当前区域id的（即本区域的下级区域） 区域订单
        if(Session::get('level')=='1'){
            $child = Db::table('order_level_v')
            ->field('order_id,order_name,user_company,area,info_origin')
            ->distinct(true)
            ->where('time_exchange','<=',$now)
            ->where('level','>',Session::get('level'))
            ->select();
        }else{
            $child = Db::table('order_level_v')
            ->field('order_id,order_name,user_company,area,info_origin')
            ->distinct(true)
            ->where('time_exchange','<=',$now)
            ->where('superior_id',Session::get('area_id'))
            ->select();
        }
        
        $this->assign('self_info',$self);
        $this->assign('child_info',$child);
        return view('Index/backorder');
    }
}