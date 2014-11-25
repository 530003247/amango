<?php

namespace Addons\Exam\Controller;
use Home\Controller\AddonsController;

class HomeController extends AddonsController{
    private $examcookie = '';
    private $welcomeurl = '';
    private $userid     = array();
    private $testinfo   = array();
    public $q_typearr = array(
             'text'   => '书面题',
             'image'  => '看图题',
             'audio'  => '听力题',
             'video'  => '视频题',

        );
    public $a_typearr = array(
             'select' => '单选',
             'bool'   => '判断',
             'text'   => '填空',
             'checkbox' => '多选',

        );
    public function inint_cookie($id){
        require_once(ONETHINK_ADDON_PATH.'Exam/functions.php');
        $this->examcookie = cookie('addonsexamid');
        if(empty($this->examcookie)){
            $this->examcookie = md5('amango'.time().get_client_ip());
            cookie('addonsexamid',$this->examcookie);
        }
            $userinfo  = session('P');
            //用户唯一ID
            $this->userid = empty($userinfo)?array('fromusername'=>$this->examcookie):$userinfo;
            //考卷初始信息
            if(is_numeric($id)){
                $map['id'] = $id;
                //读取考卷配置
                $this->testinfo   = M('Addonsexam')->where($map)->find();
            }
            //初始化跳转
            $this->welcomeurl = U('Index/index');
    }
    //考卷锁定
    public function testlock(){
        if(IS_AJAX){
            $pwd     = I('post.pwd');
            $id      = session('addonsexamid');
            $this->inint_cookie($id);
            if($pwd==$this->testinfo['password']){
                session('addonsexam'.$id.'_allow',1);
                session('addonsexamid',null);
                $this->ajaxReturn(array('status'=>1,'href'=>addons_url('Exam://Home/index',array('id'=>$id),'Home')));
            }
                $this->ajaxReturn(array('status'=>0));
        } else {
            $id = I('get.id');
            $status = session('addonsexam'.$id.'_allow');
            // if($status!=1){
            //     redirect(addons_url('Exam://Home/index',array('id'=>$id),'Home'));
            // } else {
                $this->inint_cookie($id);
                if(empty($this->testinfo)){
                    $this->error('请从官方微信号发送“考试”选择考卷进入！',$this->welcomeurl);
                } else {
                    session('addonsexamid',$id);
                    $this->assign('title',$this->testinfo['title']);
                    $this->assign('HideAll','1');
                    $this->display();
                }
            //}
        }
    }
    //考卷显示
    public function index(){
        $id = I('get.id');
        $this->inint_cookie($id);
        if(empty($id)){
            if(!empty($this->userid['id'])){
                redirect(addons_url('Exam://Home/profile',array(),'Home'));
            } else {
                $this->error('请从官方微信号发送“考试”选择考卷进入！',$this->welcomeurl);
            }
        } else {
            $map['id'] = $id;
            //读取考卷配置
            $info  = $this->testinfo;
            if(empty($info)){
                $this->error('该套考卷不存在，请从官方微信号发送“考试”选择考卷进入！',$this->welcomeurl);
            }
            //判断是否需要密码进入
            if(!empty($info['password'])){
                $status = session('addonsexam'.$id.'_allow');
                if($status!=1){
                    redirect(addons_url('Exam://Home/testlock',$map,'Home'));
                }
            }
            //是否为实名制考试
            if($info['has_name']==1){
                if(empty($this->userid['id'])){
                    $this->error('本场考试需要实名制考试,请从官方微信号发送“考试'.$this->testinfo['keyword'].'”进入吧！',$this->welcomeurl);
                }
            }
            //是否唯一考试
            if($info['has_paiming']==2){
                $condition['fromusername'] = $this->userid['fromusername'];
                $nums = M('Addonsexamlog')->where($condition)->count();
                if($nums>0){
                    $this->error('您已经答过本题咯,请勿重复作答！',$this->welcomeurl);
                }
            }
            //是否分享
            $link_url  = ($info['has_share']==0)?'':addons_url('Exam://Home/index',$map,'Home');
            $common[0] = $link_url;
            //是否排名浏览
            $common[1] = ($info['has_paiming']==0)?'':addons_url('Exam://Home/paiming',$map,'Home');
            //是否错题分享
            $common[2] = ($info['has_error']==0)?'':addons_url('Exam://Home/showerror',$map,'Home');
            //排名链接
            $common[3] = addons_url('Exam://Home/setscore',$map,'Home');
            //判断随机
            $order = ($info['order']==0)?'rand()':'paixu DESC';
            
            $list  = M('Addonsques')->where(array('group'=>$id))->order($order)->select();
            //自增
            M('Addonsexam')->where($map)->setInc('views');
            $this->assign('nums',count($list));
            $this->assign('info',$info);
            $this->assign('jstpl',set_jstpl($link_url,$info,$list,$common,$id));
        }
            $this->display();
    }
    //考试排名
    public function paiming(){
        $id  = I('get.id');
        $this->inint_cookie($id);
        $condition['fromusername'] = $this->userid['fromusername'];
        if(!is_numeric($id)){
            $this->error('请查看有效考试的排行版哦！');
        }
        //联合查询 AddonsexamlogViewModel
        $model     = M('Addonsexamlog');
        $usermodel = M('Weixinmember');
        $map['testid'] = $id;
        $grouplist = $model->where($map)->order('score DESC')->limit(15)->select();
        $userscore = $model->where($condition)->order('score DESC')->find();
        foreach ($grouplist as $key => $value) {
            if($value['fromusername']==$userscore['fromusername']&&$value['score']==$userscore['score']){
                $paimingnum = $key+1;
                break;
            }
        }
        //榜单姓名
        foreach ($grouplist as $key => $value) {
            $nickname = $usermodel->where(array('fromusername'=>$value['fromusername']))->getField('nickname');
            $grouplist[$key]['nickname'] = empty($nickname)?'匿名用户':$nickname;
        }

        $selfurl = addons_url('Exam://Home/index',array('id'=>$id),'Home');
        $publicpath = str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', ONETHINK_ADDON_PATH.'Exam/Public/');
        $Shareinfo  = array(
                    'ImgUrl'     =>$publicpath.'ad1.jpg',
                    'TimeLink'   =>$selfurl,
                    'FriendLink' =>$selfurl,
                    'WeiboLink'  =>$selfurl,
                    'tTitle'     =>'我正在参加微考试，你也来吧！',
                    'tContent'   =>$content,
                    'fTitle'     =>'我正在参加微考试，你也来吧！',
                    'fContent'   =>$content,
                    'wContent'   =>$content
                    );
            $this->assign('Share',$Shareinfo);
            $this->assign('title',$this->testinfo['title']);
            $this->assign('indexurl',$selfurl);
            $this->assign('list',$grouplist);
            $this->assign('paimingnum',$paimingnum);
            $this->display();
    }
    //考试排名
    public function showerror(){
        $info = I('get.');
        if(empty($info['errorparam'])){
            $this->success('亲，你解答该套考卷木有错题哦',addons_url('Exam://Home/index',array('id'=>$info['id']),'Home'));
        } else {
            $info['errorparam'] = str_replace('X', ',', $info['errorparam']);
            $map['id'] = array('in',$info['errorparam']);
            $errolist  = M('Addonsques')->where($map)->select();
            $this->assign('errornums',count($errolist));
            $this->assign('list',$errolist);
        }
        $abcd = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $selfurl = addons_url('Exam://Home/index',array('id'=>$info['id']),'Home');
        $publicpath = str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', ONETHINK_ADDON_PATH.'Exam/Public/');
        $Shareinfo  = array(
                    'ImgUrl'     =>$publicpath.'ad1.jpg',
                    'TimeLink'   =>$selfurl,
                    'FriendLink' =>$selfurl,
                    'WeiboLink'  =>$selfurl,
                    'tTitle'     =>'我正在参加微考试，你也来吧！',
                    'tContent'   =>$content,
                    'fTitle'     =>'我正在参加微考试，你也来吧！',
                    'fContent'   =>$content,
                    'wContent'   =>$content
                    );
        $this->assign('HideAll','1');
        $this->assign('Share',$Shareinfo);
        $this->assign('indexurl',$selfurl);
        $this->assign('abcd',$abcd);
        $this->assign('q_typearr',$this->q_typearr);
        $this->assign('a_typearr',$this->a_typearr);
        $this->display();
    }
    //AJAX 统计分数记录
    public function setscore(){
        if(IS_AJAX){
            $testid = I('get.id');
            $this->inint_cookie($testid);
            if(empty($this->testinfo)){
                return false;
            }
            if($testid>0){
                $data['score']   = I('post.score');
                $data['addtime'] = time();
                $data['errors']  = str_replace('X', ',', I('post.error'));
                //判断是否为唯一  重复  关闭
                //判断是否实名制
                if($this->testinfo['has_name']==1){
                    if(empty($this->testinfo['id'])){
                        return false;
                    }
                }
                $model = M('Addonsexamlog');
                $map['fromusername'] = $this->userid['fromusername'];
                $map['testid']       = $testid;
                $countnums = $model->where($map)->count();
                if($countnums>0){
                    $model->where($map)->save($data);
                } else {
                    $data['testid']       = $testid;
                    $data['fromusername'] = $this->userid['fromusername'];
                    $model->add($data);
                }
                    return true;
            }
        }
            return false;
    }
    //插件首页用户后台
    public function profile(){
        $this->inint_cookie();
        $map['fromusername'] = $this->userid['fromusername'];
        $list = M('Addonsexamlog')->where($map)->order('addtime DESC')->select();
        $model = M('Addonsexam');
        foreach ($list as $key => $value) {
            $list[$key]['testtitle'] = $model->where(array('id'=>$value['testid']))->getField('title');
        }
        $nickname = M('Weixinmember')->where($map)->getField('nickname');
        $nickname = empty($nickname)?'匿名用户':$nickname;
        $this->assign('nickname',$nickname);
        $this->assign('nums',count($list));
        $this->assign('list',$list);
        $this->assign('HideAll','1');
        $this->display();
    }
    //插件Tips返回处理 return 数字
    public function run(){
        return '';
    }
}
