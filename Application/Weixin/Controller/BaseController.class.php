<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Controller;

class BaseController{
	//唯一入口  检查请求合法性
	public function __construct() {
        G('begin');
        $newstr                     = $_GET["echostr"];
        $signature                  = $_GET["signature"];
        $timestamp                  = $_GET["timestamp"];
        $nonce                      = $_GET["nonce"];   
        $postStr                    = $GLOBALS["HTTP_RAW_POST_DATA"];
            $info = self::init_account_info($_GET["hash"]);
            $token = $info['account_token'];
            import('Weixin.ORG.Weixin');
                if (empty($token)){
                    echo 'sonmethiing is wrong';
                } else {
                    session('token',$token);  
                    $wechatObj = new \Weixin($newstr,$token,$postStr,$signature,$timestamp,$nonce);
                    $wechatinfo = $wechatObj->responseMsg();
                }
                    //用户信息检验      
                    if ($wechatinfo){
                        global $_W;
                        $_W = $wechatinfo;
                        session('from',$_W['fromusername']);
                        session('openid',$_W['fromusername']);
                        session('to',$_W['tousername']);
                        self::init_user_info();
                    } else {
                        echo '这是普通请求';
                    }  
	}
    //用户个人信息  
    public function init_user_info() {
        global $_W;
            $userinit = get_user_auth();
            if(empty($userinit['id'])){
                //注册用户
                $userinit = empty($_W['fromusername']) ? wx_error('Sorry!用户标识为空') : register_weixin(true,$_W['fromusername']);
            }
                //用户关注状态
                if($userinit['follow']==0||empty($userinit['follow'])){
                    change_user_follow($userinit['fromusername']);
                }
                //注册用户昵称
                if(preg_match("/^我叫/",$_W['content'])){
                    $str      = str_replace(" ", '', $_W['content']);
                    $nickname = str_replace('我叫', '', $str);
                    set_nickname($_W['fromusername'],$nickname );
                }
                //强制绑定昵称
                if(empty($userinit['nickname'])){
                    wx_success('发送“我叫”+您的昵称,交朋友更方便哦~');
                }

                    if($userinit['status']==0){
                        wx_error('Sorry!您的账号已被冻结,请联系管理员......');
                    }
                    if($userinit['followercate_status']==0){
                        wx_error('Sorry!您所在的用户组【'.$userinit['followercate_title'].'】已被冻结,请联系管理员......');
                    }
                self::init_auto_keyword();
                /*用户资料初始化*/
                global $_P;
                       $_P = $userinit;
    }
    //公众号信息  暂不启用缓存
    protected function init_auto_keyword() {
        global $_W;
        //判断是否为 微笑  发送 UC 自动登录链接
        if(preg_match('/^\/微笑$/',$_W['content'])){
            $autoinfo = M('Weixinmember')->where(array('fromusername'=>session('from')))->field('nickname,ucusername,ucpassword')->find();
            $autolink = U('Home/User/login',$autoinfo,'',true);
            $other    = "【内置关键词】\n发送“我叫***”即可改昵称";
            wx_success("亲爱的{$autoinfo['nickname']}\nUC账号:{$autoinfo['ucusername']}\nUC密码:{$autoinfo['ucpassword']}\r\n<a href='{$autolink}'>自动登录网页版</a>\n".$other);
        }
    }

    //公众号信息  暂不启用缓存
    public function init_account_info($account_oldid) {
        $info = S('Accountinfo');
        if(empty($info)){
            $info = M("Account")->where(array('account_oldid'=>$account_oldid))->find();
            S('Accountinfo',$info);
        }
        global $_G;
               $_G = $info;
            return $info;
    }
}

?>