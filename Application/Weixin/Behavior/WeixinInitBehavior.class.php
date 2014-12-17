<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Behavior;
use Think\Behavior;
defined('THINK_PATH') or exit();
class WeixinInitBehavior extends Behavior {
    static private $weixin_post = array();
    public function run(&$wechatinfo){
        global $_W;
        self::$weixin_post = $_W;
        //初始化用户信息
        self::init_user_info();
        //初始化系统关键词
        self::init_auto_keyword();
        return true;
    }
    static private function init_user_info(){
            $userinit = get_user_auth();
            if(empty($userinit['id'])){
                //注册用户
                $userinit = empty(self::$weixin_post['fromusername']) ? wx_error('Sorry!用户标识为空') : register_weixin(true,self::$weixin_post['fromusername']);
            }
                //用户关注状态
                if(empty($userinit['follow'])){
                    change_user_follow($userinit['fromusername']);
                }
                //注册用户昵称
                if(preg_match("/^我叫/",self::$weixin_post['content'])){
                    $str      = str_replace(" ", '', self::$weixin_post['content']);
                    $nickname = str_replace('我叫', '', $str);
                    set_nickname(self::$weixin_post['fromusername'],$nickname );
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
                /*用户资料初始化*/
                global $_P;$_P = $userinit;
    }
    static protected function init_auto_keyword() {
        //判断是否为 微笑  发送 UC 自动登录链接
        if(preg_match('/^\/微笑$/', self::$weixin_post['content'])){
            $autoinfo = M('Weixinmember')->where(array('fromusername'=>session('from')))->field('nickname,ucusername,ucpassword')->find();
            $autolink = U('Home/User/login',$autoinfo,'',true);
            $other    = "【内置关键词】\n发送“我叫***”即可改昵称";
            wx_success("亲爱的{$autoinfo['nickname']}\nUC账号:{$autoinfo['ucusername']}\nUC密码:{$autoinfo['ucpassword']}\r\n<a href='{$autolink}'>自动登录网页版</a>\n".$other);
        }
    }
}