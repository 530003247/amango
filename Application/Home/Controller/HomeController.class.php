<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;
use User\Api\UserApi;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}
    protected function _initialize(){
        //初始化主题 参数
        A('Amangotheme')->init_config();
            //微信自动登陆
            if(!empty($_GET['ucusername'])&&!empty($_GET['ucpassword'])){
                $this->auto_login($_GET['ucusername'],$_GET['ucpassword']);
	         	//判断是否存在
	         	$replace_list = array();
	         	if(!empty($_GET['ucusername'])){
                    $replace_list['/ucusername/'.$_GET['ucusername']] = '';
	         	}
	         	if(!empty($_GET['ucpassword'])){
                    $replace_list['/ucpassword/'.$_GET['ucpassword']] = '';
	         	}
	         	$redirect_url   = strtr(__SELF__,$replace_list);
				redirect($redirect_url);
            }
    }
    //自动登陆
    protected function auto_login($username, $password){
        $user = new UserApi;
			$uid = $user->login($username, $password);
			if(0 < $uid){ //UC登录成功
				$Member = M('Weixinmember')->where(array('ucmember'=>$uid))->find();
				if(!empty($Member)){ //登录用户
			        $auth = array(
			            'uid'             => $Member['id'],
			            'username'        => $Member['nickname'],
			            'last_login_time' => time(),
			        );
			        session('P', $Member);
			        session('user_auth', $auth);
			        session('user_auth_sign', data_auth_sign($auth));
			        return true;
				}
			}
			    return false;
    }
	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}
}
