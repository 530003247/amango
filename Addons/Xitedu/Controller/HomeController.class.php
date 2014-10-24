<?php

namespace Addons\Xitedu\Controller;
use Home\Controller\AddonsController;

class HomeController extends AddonsController{
    public $login_action = array(
                'profile'=>array('errormsg'=>'查看我的教务信息，请先登陆！','errorurl'=>''),
                'show_yue'=>array('errormsg'=>'查看我的余额，请先登陆！','errorurl'=>''),
                'show_jieyue'=>array('errormsg'=>'查看我的借阅，请先登陆！','errorurl'=>''),
    );
    public $deny_action  = array();
    /**
     * 开发说明:三大全局变量  global $_W,$_K;            W[网站信息]  K[默认微信公众号信息]
     *                        session('P');              前台用户信息
     *          获取插件配置  get_addon_config($name);
     */
    public function show_yue(){
        $userinfo = session('P');
        $info = M('Addonseduinfo')->where(array('fromusername'=>$userinfo['fromusername']))->field('xuehao,headimg')->find();
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $Jmuedu = new \Xitlibrary();
        $group  = $Jmuedu->ykt_login($info['xuehao'],$info['headimg']);
        S('HOME_ADDONS_XIREDUMONEY',$group);
        return $group['list'][0][3];
    }
    public function show_jieyue(){
        $userinfo = session('P');
        $info = M('Addonseduinfo')->where(array('fromusername'=>$userinfo['fromusername']))->find();
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $demo = new \Xitlibrary();
        $demo->library_login($info['xuehao'],$info['headimg']);
        $booklist = $demo->getovertime();
        S('HADDONS_XIREDUBOOK',$booklist);
        $books = count($booklist);
        $books = is_numeric($books) ? $books : 0;
        return $books.'本';
    }
    public function balance(){
        $userinfo = session('P');
        $info = M('Addonseduinfo')->where(array('fromusername'=>$userinfo['fromusername']))->field('xuehao,headimg')->find();
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $Jmuedu = new \Xitlibrary();
        $moneylist  = $Jmuedu->ykt_login($info['xuehao'],$info['headimg']);

        $this->assign('yue',str_replace('￥', '', $moneylist['list'][0][3]));
        $this->assign('total',str_replace('￥', '', $moneylist['total']['total']));
        $this->assign('list',$moneylist['list']);
        $this->display();
    }
    public function borrow(){
        $userinfo = session('P');
        $info = M('Addonseduinfo')->where(array('fromusername'=>$userinfo['fromusername']))->find();
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $demo = new \Xitlibrary();
        $demo->library_login($info['xuehao'],$info['headimg']);
        $booklist = $demo->getovertime();

        $books = count($booklist);
        $books = is_numeric($books) ? $books : 0;
        foreach ($booklist as $key => $value) {
            $booklist[$key]['days'] = $this->daysbetweendates($value[1],$value[6]);
        }
        $this->assign('nums',$books);
        $this->assign('list',$booklist);
        $this->display();
    }
    public function daysbetweendates($date1, $date2){
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $days = ceil(abs($date1 - $date2)/86400);
        return $days;
    }

    //插件前台展示页面
    public function index(){
        $this->display();
    }
    //课表信息
    public function myclass(){
        $userinfo = session('P');
        $xuehao   = M('Addonseduinfo')->where(array('fromusername'=>$userinfo['fromusername']))->getfield('xuehao');
        $edumodel = M('Addonsclass');
        $eduinfo  = $edumodel->where(array('xuehao'=>$xuehao))->find();

        $neweduinfo[] = unserialize($eduinfo['week1']);
        $neweduinfo[] = unserialize($eduinfo['week2']);
        $neweduinfo[] = unserialize($eduinfo['week3']);
        $neweduinfo[] = unserialize($eduinfo['week4']);
        $neweduinfo[] = unserialize($eduinfo['week5']);
        $neweduinfo[] = unserialize($eduinfo['week6']);
        $neweduinfo[] = unserialize($eduinfo['week0']);
        $weekname = array('一','二','三','四','五','六','日');
        foreach ($neweduinfo as $key => $value) {
            $neweduinfo[$key]['wkname'] = $weekname[$key];
        }

        $this->assign('classinfo',$eduinfo);
        $this->assign('weekclass',$neweduinfo);
        $this->display();
    }
    //历史成绩
    public function scorelist(){
        $userinfo = session('P');
        $xuehao   = M('Addonseduinfo')->where(array('fromusername'=>$userinfo['fromusername']))->getfield('allscore');
        $allscore = unserialize($xuehao);
        $scorenums = 0;
        $scoretoal = 0;
        foreach ($allscore as $k => $v) {
            foreach ($v as $key => $value) {
                if(is_numeric($value[9])){
                    $scorenums = $scorenums + 1;
                    $scoretoal = $scoretoal + $value[9];
                }
            }
        }
        $num = $scoretoal/$scorenums;
        $num = ((int)($num*100))/100;
        $this->assign('jidian',$num);
        $this->assign('scorelist',$allscore);
        $this->display();
    }
    //插件Tips返回处理 return 数字
    public function run(){
        return '';
    }
    //插件首页用户后台
    public function profile(){
        // Amango_Addons_Import('Xitedu.php','Xitedu');//导入
        // $demo = new \Xitedu();
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $demo = new \Xitlibrary();
        //$demo->paraxinfo();die;
        if(IS_POST){
            //$a     = $demo->postLogin(I('username'),I('password'),I('val'));
            $pwd     = I('password');
            $oldinfo = $demo->library_login(I('username'),$pwd);
            foreach ($oldinfo as $key => $value) {
                $oldinfo[$key] = str_replace(array("\n","\r"), "", $value);
            }
            //判断是否登陆成功
            if(!empty($oldinfo[1])){
                $sysinfo  = session('P');
                $userinfo = array();
                //获取个人信息
                $userinfo = $demo->getUserinfo();
                if(empty($userinfo)){
                    $this->error('亲！服务器不给力，无法获取相关信息，请再登陆一次');
                }
                //获取个人照片
                //$headimg  = $demo->getUserpic($userinfo[0]);
                //获取个人考证成绩 序列化
                //$allexam  = serialize($demo->getUserexam());
                //获取个人学年成绩 序列化
                $allscore = serialize($demo->getclass());
                //获取个人课表
                $classlist = $demo->getscore();

                $data = array(
                     'fromusername' => $sysinfo['fromusername'],
                     'name'     => $userinfo[1],
                     'headimg'  => $pwd,
                     'xuehao'   => $userinfo[0],
                     'sex'      => $userinfo[3],
                     'cardid'   => $userinfo[5],
                     'birthday' => $userinfo[4],
                     'zhuanye'  => $userinfo[10],
                     'xueyuan'  => $userinfo[9],
                     'address'  => $userinfo[12],
                     'mingzu'   => $userinfo[6],
                     'shengfen' => $userinfo[8],
                     'grade'    => $userinfo[11],
                     'type'     => $userinfo[7],
                     'youbian'  => $userinfo[13]
                );
                // if(!empty($allexam)){
                //     $data['allexam'] = $allexam;
                // }
                if(!empty($allscore)){
                    $data['allscore'] = $allscore;
                }
                $Addonseduinfo = M('Addonseduinfo');
                $has_user = $Addonseduinfo->where(array('fromusername'=>$sysinfo['fromusername']))->count();
                if($has_user==1){
                    $Addonseduinfo->where(array('fromusername'=>$sysinfo['fromusername']))->save($data);
                } else {
                    $Addonseduinfo->add($data);
                }
                //插入个人课表
                if(!empty($classlist)){
                    $classdata = array(
                         'xuehao'   => $userinfo[0],
                         'itemname' => $classlist['name'][0],
                         'classname'=> $classlist['name'][1],
                         'week1' => serialize($classlist['class'][1]),
                         'week2' => serialize($classlist['class'][2]),
                         'week3' => serialize($classlist['class'][3]),
                         'week4' => serialize($classlist['class'][4]),
                         'week5' => serialize($classlist['class'][5]),
                         'week6' => serialize($classlist['class'][6]),
                         'week0' => serialize($classlist['class'][7]),
                    );

                    $Addonsclass = M('Addonsclass');
                    $has_class   = $Addonsclass->where(array('xuehao'=>$userinfo[0]))->count();
                    if($has_class==0){
                        $Addonsclass->add($classdata);
                    } else {
                        $Addonsclass->where(array('xuehao'=>$userinfo[0]))->save($classdata);
                    }
                    $this->success('恭喜您绑定学号密码成功！');
                }

            } else {
                $this->error('亲！请确保账号·密码·验证码是否填写正确');
            }
        } else {
            $userinfo = session('P');
            if(empty($userinfo)){
                $this->display();
            } else {
                $edumodel = M('Addonseduinfo');
                $eduinfo  = $edumodel->where(array('fromusername'=>$userinfo['fromusername']))->field('allscore,name,id,xuehao,birthday,shengfen,xueyuan')->find();
                if(empty($eduinfo)){
                    $this->display();
                } else {
                    //TODO  可以新增缓存
                    $allscore = unserialize($eduinfo['allscore']);
                    $allteach = 0;
                    $unteach  = 0;
                    foreach ($allscore as $key => $value) {
                        //计算总科目
                        $allteach = count($value)+$allteach;
                        foreach ($value as $k => $v) {
                            if(is_string($v[8])&&$v[8]=='不合格'){
                                $unteach = $unteach + 1;
                            }
                            if(is_numeric($v[8])&&$v[8]<60){
                                $unteach = $unteach + 1;
                            }
                        }
                    }
                    $edytese = array();
                    $teseper = array();
                    //总人数
                    $edytese['count'] = $edumodel->count();
                    //同名
                    $edytese['name']  = $edumodel->where(array('name'=>array('like',$eduinfo['name'])))->count();
                    $teseper['name']  = ($edytese['name']/$edytese['count'])*100;
                    //生日
                    $edytese['birthday']  = $edumodel->where(array('birthday'=>array('like',$eduinfo['birthday'])))->count();
                    $teseper['birthday']  = ($edytese['birthday']/$edytese['count'])*100;
                    //老乡
                    $edytese['shengfen']  = $edumodel->where(array('shengfen'=>array('like',$eduinfo['shengfen'])))->count();
                    $teseper['shengfen']  = ($edytese['shengfen']/$edytese['count'])*100;
                    //同学院
                    $edytese['xueyuan']  = $edumodel->where(array('xueyuan'=>array('like',$eduinfo['xueyuan'])))->count();
                    $teseper['xueyuan']  = ($edytese['xueyuan']/$edytese['count'])*100;
                    //同专业
                    $edytese['zhuanye']  = $edumodel->where(array('zhuanye'=>array('like',$eduinfo['zhuanye'])))->count();
                    $teseper['zhuanye']  = ($edytese['zhuanye']/$edytese['count'])*100;
                    //挂科相关
                    $edytese['guake']    = $unteach;
                    $edytese['allke']    = $allteach;
                    $teseper['guake']    = ($unteach/$allteach)*100;

                    $this->assign('eduinfo',$eduinfo);
                    $this->assign('edutese',$edytese);
                    $this->assign('teseper',$teseper);
                    $this->display('index');
                }
            }
        }
    }
}
