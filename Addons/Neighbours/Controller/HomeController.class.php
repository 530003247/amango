<?php

namespace Addons\Neighbours\Controller;
use Home\Controller\AddonsController;

class HomeController extends AddonsController{
    public function index(){
        $map  = array('id'=>I('id'));
    	$info = M('Addonsneighbours')->where($map)->find();
    	$userinfo     = M('Weixinmember')->where(array('id'=>$info['from']))->Field('nickname,qq')->find();
        $info['from'] = $userinfo['nickname'];
        $info['qq']   = $userinfo['qq'];
        $randnum = rand(1,6);
        $randimg = 'http://'.$_SERVER['HTTP_HOST'].'/Addons/Neighbours/Public/images/default_head'.$randnum.'.png';
        $selfurl = $_SERVER['HTTP_HOST'].__SELF__;
        //随机头像
        $Shareinfo = array(
					'ImgUrl'     =>$randimg,
					'TimeLink'   =>$selfurl,
					'FriendLink' =>$selfurl,
					'WeiboLink'  =>$selfurl,
					'tTitle'     =>'来自：芒果社区-隔壁',
					'tContent'   =>$info['content'],
					'fTitle'     =>'来自：芒果社区-隔壁',
					'fContent'   =>$info['content'],
					'wContent'   =>$info['content']
        	        );
        M('Addonsneighbours')->where($map)->setInc('view',1); 
        $this->assign('Htitle','我的隔壁');
        $this->assign('Share',$Shareinfo);
        $this->assign('Info',$info);
        $this->assign('Randnum',$randnum);
        $this->display();
    }
    //显示tips
    public function tips(){
        $userinfo = session('P');
        if(!empty($userinfo)){
            $today    = strtotime(date("Y-m-d"));
            $map      = array(
                'creattime' => array('egt', $today),
                'from'      => $userinfo['id']
            );
            $newnums = M('Addonsneighbours')->where($map)->count();
            return ($newnums>0) ? $newnums : '';
        } else {
            return '';
        }

    }
    //默认首页用户后台
    public function profile(){
        $userinfo = session('P');
        if(empty($userinfo)){
            $this->error('查看我的隔壁动态，请先登陆！', U('User/login'));
        } else {
            $model      = M('Addonsneighbours');
            $tolists    = $model->where(array('to'=>$userinfo['id']))->order('creattime desc')->limit(10)->select();
            $fromlists  = $model->where(array('from'=>$userinfo['id']))->order('creattime desc')->limit(10)->select();
            $allnums    = count($fromlists);
            $callme     = count($tolists);
            $this->assign('total',$allnums);
            $this->assign('callme',$callme);
            $this->assign('fromlists',$fromlists);
            $this->assign('tolists',$tolists);
        }
        $this->display();
    }
}
