<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {

	/* 用户中心首页 */
	public function index(){
		
	}
	/* 注册页面 */
	public function register($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}
			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			
			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				$this->success('注册成功！',U('login'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
			$this->display();
		}
	}

	/* 登录页面 */
	public function login($username = '', $password = '', $verify = ''){
		if(IS_POST){ //登录验证
			/* 检测验证码 暂不检查*/
			// if(!check_verify($verify)){
			// 	$this->error('验证码输入错误！');
			// }
			// $goto = $_GET['amangogoto'];
			// $goto = base64_decode(base64_decode($goto));
   //          dump($goto);die;
			/* 调用UC登录接口登录 */
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

			        $goto = $_GET['amangogoto'];
			        $goto = base64_decode(base64_decode($goto));
			        $url  = empty($goto) ? U('Home/User/profile') : $goto;

					$this->success('正在进入',$url);
				} else {
					$this->error('请输入正确的授权账号和密码');
				}
			} else { //登录失败
				switch($uid) {
					case -1: $error = 'UC用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = 'UC密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}

		} else { //显示登录表单
			$userinfo = session('user_auth');
			if(!empty($userinfo)){
		        $goto = $_GET['amangogoto'];
		        $goto = base64_decode(base64_decode($goto));
		        $url  = empty($goto) ? U('Home/User/profile') : $goto;
		        redirect($url);
			}
	        $shareurl  = Amango_U('User/login','','',true);
	        $content   = '芒果,是一种校园生活方式';
	        $Shareinfo = array(
						'ImgUrl'     =>'',
						'TimeLink'   =>$shareurl,
						'FriendLink' =>$shareurl,
						'WeiboLink'  =>$shareurl,
						'tTitle'     =>'同一个芒果,演绎不同的精彩',
						'tContent'   =>$content,
						'fTitle'     =>'同一个芒果,演绎不同的精彩',
						'fContent'   =>$content,
						'wContent'   =>$content
	        	        );
	        $this->assign('Share',$Shareinfo);
			empty($_GET['nickname']) || $this->assign('autonickname',$_GET['nickname']);
			empty($_GET['ucusername']) || $this->assign('autoucusername',$_GET['ucusername']);
			empty($_GET['ucpassword']) || $this->assign('autoucpassword',$_GET['ucpassword']);
			$this->display();
		}
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			global $_P;
			$_P = '';
			session('p', null);
			session('P', null);
	        session('user_auth', null);
	        session('user_auth_sign', null);
	        session(null);
			$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}


    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
        if ( IS_POST ) {
        	$userinfo = session('P');
        	if(empty($userinfo['id'])){
                $this->error('请确定您修改的身份');
        	}
        	$nickname = I('nickname');
        	if(empty($nickname)){
                $this->error('昵称不能修改为空');
        	}
            $data['nickname'] = $nickname;
            $data['qq']       = I('qq');
            $data['sex']      = I('sex');
            $data['location'] = I('address');
            $status = M('Weixinmember')->where(array('id'=>$userinfo['id']))->save($data);
            //TODO   UC绑定的数据
            if($status){
            	$Member = M('Weixinmember')->where(array('id'=>$userinfo['id']))->find();
		        $auth = array(
		            'uid'             => $Member['id'],
		            'username'        => $Member['nickname'],
		            'last_login_time' => time(),
		        );
		        session('P', $Member);
		        session('user_auth', $auth);
		        session('user_auth_sign', data_auth_sign($auth));
                $this->success('更新个人资料成功！');
            }else{
                $this->error('更新个人资料失败！');
            }
            //获取参数
            // $uid        =   is_login();
            // $password   =   I('post.old');
            // $repassword = I('post.repassword');
            // $data['password'] = I('post.password');
            // empty($password) && $this->error('请输入原密码');
            // empty($data['password']) && $this->error('请输入新密码');
            // empty($repassword) && $this->error('请输入确认密码');

            // if($data['password'] !== $repassword){
            //     $this->error('您输入的新密码与确认密码不一致');
            // }

            // $Api = new UserApi();
            // $res = $Api->updateInfo($uid, $password, $data);
            // if($res['status']){
            //     $this->success('修改密码成功！');
            // }else{
            //     $this->error($res['info']);
            // }
        }else{
            $this->display();
        }
    }

}
