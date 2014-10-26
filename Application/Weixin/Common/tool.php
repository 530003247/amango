<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
/**
 * 循环标签匹配
 * $matches  为匹配到的内容[数组]
 * 统一隐式传参 session('from')  session('to')
 * @return string 
 */     
function xml_parse_tags($matches) {
    $taglist = array();
    $taglist = session('AMANGO_PARSETAGS');
        preg_match('/(?<=<amango:)[^>]+(?=>)/', $matches[1],$tag_name);
        $tagname = str_replace('tag', '', $tag_name[0]);
        $tagitem = $taglist[$tagname];
        if(empty($tagitem)){
            return "";
        } else {
            //wx_error(json_encode($tagitem));
            foreach ($tagitem as $key => $value) {
                    switch (strtolower($value['type'])) {
                        case 'func':
                            $funcname = $value['action'];
                            $str[$key] = $funcname($value['param']);
                            break;
                        case 'action':
                            $actionname = $value['action'];
                            $param      = array();
                            $param      = parse_config($value['param']);
                            $str[$key]  = R($actionname,$param);
                            break;     
                        case 'static':
                            $str[$key] = $value['param'];
                            break;              
                        default:
                            wx_error('标签解析不存在');
                            break;
                    }                   
            }
                $newstr = implode("\n", $str);
                return $newstr."\n";
        }
}
/**
* 获取TAG组
* @param  默认缓存时间为8个小时
* @return string 
*/
function get_tag_lists($type,$all=false) {
    $cachename = 'AMANGO_TAGLIST_'.strtoupper($type);
    $Data      = S($cachename);
    $taglist   = array();
     if(empty($Data)){
        $Condition['Tagscate.status']        = 1;
        $Condition['Tagslists.status']       = 1;
        if(fasle===$all&&!empty($type)){
            $Condition['Tagscate.tagscate_type'] = $type;
        }
            $tag_list = D('TagslistsView')->where($Condition)->order('Tagslists.sort DESC')->select();
            foreach ($tag_list as $key => $value) {
                $taglist[$value['tagscate_title']][] = array(
                   'type'   => $value['tagslists_type'],
                   'action' => $value['tagslists_action'],
                   'param'  => $value['tagslists_param'],
                );
            }
                S($cachename,$taglist,28800);
    } else {
        $taglist = $Data;
    }
        return $taglist;
}
//过滤解析后的TAG
function parse_tags($denytag,$type) {
        //获取TAG列表
        $taglist = get_tag_lists($type);
        foreach ($denytag as $key => $value) {
            if(is_numeric($key)){
                unset($taglist[$value]);
            }
            if(is_string($value)){
                unset($taglist[$key][$value]);
            }
        }
            session('AMANGO_PARSETAGS',$taglist);
                return $taglist;
}

//通用转义
function escape_common($contentStr) {
    global $_P;
    global $_G;
    $newinfo = $_P;
    //获取用户基础信息
    $sexchoose = array('美女','帅哥');
    foreach ($newinfo as $key => $value) {
        if($key=='sex'){
            $value = $sexchoose[$value];
        }
        $new_info['P_'.strtoupper($key)] = $value;
    }
    //获取周几
    $weekarray=array("日","一","二","三","四","五","六");
    //自动登陆
    $autoinfo=array('nickname'=>$newinfo['nickname'],'ucusername'=>$newinfo['ucusername'],'ucpassword'=>$newinfo['ucpassword'],'stamp'=>time());
    $pub_info = array(
                'P_OPENID'     =>$_P['fromusername'],
                'G_TIME'       =>date("G时i分"),
                'G_DATE'       =>date("n月j日"),
                'G_WEEK'       =>'星期'.$weekarray[date("w")],
                'G_WENAME'     =>$_G['account_name'],
                'G_WENICKNAME' =>$_G['account_nickname'],
                'G_WEQQ'       =>$_G['account_qq'],
                'G_WEACCOUNT'  =>$_G['account_weixin'],
                'G_WESUB'      =>$_G['account_sub'],
                'U_INDEX'      =>U('Home/Index/index',$autoinfo,'',true),
                'U_CENTER'     =>U('Home/User/login',$autoinfo,'',true),
    );
    $replace_list = array_merge($new_info,$pub_info);
    $contentStr   = strtr($contentStr,$replace_list);
    //插件链接
    $contentStr = preg_replace_callback("/(U_(.*)_ADDON)/","str_tags",$contentStr);
    return $contentStr;
}
function str_tags($matches) {
    global $_P;
    preg_match('/(U_(.*)_ADDON)/', $matches[1],$tag_name);
    $tag_name[2] = strtolower( $tag_name[2]);
    $urlparam    = explode('_', $tag_name[2]);
    if(in_array($urlparam[1], array('home','profile'))){
        $url_param  = 'Home/'.$urlparam[1];
    } else {
        $url_param  = ucfirst($urlparam[1]).'/'.strtolower($urlparam[2]);
    }
    defined ( 'AMANGO_ADDON_NAME' )   or define ( 'AMANGO_ADDON_NAME', ucfirst($urlparam[0]));
    $aimurl     = weixin_addons_url($url_param,$param=array(),$home='Home');
    $autoinfo   = array(
            'nickname'   => $_P['nickname'],
            'ucusername' => $_P['ucusername'],
            'ucpassword' => $_P['ucpassword'],
            'amangogoto' => base64_encode(base64_encode($aimurl))//双重base64 防止出现/
    );
    return U('Home/User/login',$autoinfo,'',true);
}
function weixin_addons_url($url='',$param=array(),$home='Home') {
        if(empty($url)){
            return false;
        }
        $exurl    = explode('/',$url);
        if(count($exurl)==1){
            $url = 'Home/'.strtolower($exurl[0]);
        } else {
            $url = ucfirst($exurl[0]).'/'.strtolower($exurl[1]);
        }
        return addons_url(AMANGO_ADDON_NAME.'://'.$url, $param, $home);
}
/**
* 获取请求类型  数量
* param:  $msgtype,$status=1
* @return string 
*/
 function get_keyword_match($is_return=false) {
        global $_W;
        $simple_type = array("text");
            $keywordinfo = get_posttype_list($_W['msgtype']);
            foreach ($keywordinfo as $k => $v) {
                if (in_array(strtolower($_W['msgtype']),$simple_type)&&preg_match($v['keyword_rules'],$_W['content'])){
                        session('Keywordid',$v['id']);
                        $preg_keword = $v;
                        break;
                }
            }
            global $_K;$_K = $preg_keword;
            return (true===$is_return) ? $preg_keword : true;
}

/**
* 获取请求类型  数量
* param:  $msgtype,$status=1
* @return string 
*/
function get_posttype_nums($msgtype,$status=1) {
            $keyword_count = count(get_posttype_list($msgtype));
            return $keyword_count;
}

/**
* 获取有效关键词列表 
* param:  msgtype status order  
* @return array 
*/
function get_posttype_list($msgtype,$status=1,$order) {
        $keywordlist = session('AMANGO_KEYWORDLIST');
    if(empty($keywordlist)){
        $nowtime = time();
        //用户请求
        $Condition['Keyword.keyword_post']  = strtolower($msgtype);
        $Condition['Keyword.status']        = $status;
        $Condition['Keyword.keyword_start'] = array('elt',$nowtime);
        $Condition['Keyword.keyword_end']   = array('egt',$nowtime);
        //关键词组状态
        $Condition['Keywordcate.status']    = 1;
        //回复响应状态
        $Condition['Response.status']       = 1;
        //TODO 用户组
        
        $order = empty($order)?'Keyword.sort DESC,Keyword.keyword_click DESC':$order;
        //$simple_type = array("image", "voice", "video", "location", "link", "music");
        //建议设置为数组  方便兼容语音识别体  
        //缓存模式:cache($Condition['posttype'],86400,'File')->
        // $keywordinfo = $Keyword->where($Condition)->order($order)->select(); 
        $keywordlist = D("KeywordView")->where($Condition)->order($order)->select(); 
        session('AMANGO_KEYWORDLIST',$keywordlist);
    }
        return $keywordlist;
}

/**
* 获取请求用户上文关键词
* param:  $last_do[0]  时间  $last_do[1]  关键词ID
* @return array 
*/
function get_line_top() {
    global $_P;
           $lastdo = $_P['lastkeyword'];
            $last_do = explode('|', $lastdo);
            session('user_lastkeyword',$lastdo);
            return $last_do;
}

/**
* 获取请求用户上文模式
* param:  $last_do[0]  时间  $last_do[1]  关键词ID
* @return array 
*/
function get_line_model() {
    global $_P;
           $lastdo = $_P['lastmodel'];
            $last_do = explode('|', $lastdo);
            session('user_lastmodel',$lastdo);
            return $last_do;
}

/**
* 获取单条关键词信息
* @return array 
*/
function get_single_keyword($id,$field='*') {
        $keywordinfo = D("KeywordView")->where(array('id'=>$id))->field($field)->find();
        return $keywordinfo;
}

/**
* 获取单条回复体
* @return array 
*/
function get_single_response($id,$field='*') {
        $Responseinfo = M('Response')->where(array('id'=>$id))->field($field)->find();
        return $Responseinfo;
}

/**
* 获取单条关键词信息
* param:  $last_do[0]  时间  $last_do[1]  关键词ID
* @return array 
*/
function error_type($id) {
        $info = get_single_keyword($id,$field='*');
        if($info['keyword_post']=='text'){
            return '激活【'.$info['keyword_content'].'】';
        } else {
            $error_context = array('image'=>'发送一张图片', 'voice'=>'发送一段录音', 'video'=>'发送一段视频', 'location'=>'发送位置信息', 'link'=>'发送一段收藏链接', 'music'=>'发送一首音乐');
            return $error_context[$info['keyword_post']];
        }
} 

/**
* 关键词  用户组状态
* $type : $is_echo=false
* @return echo  retrun 
*/
function get_keyword_user_auth($is_echo=false){
            global $_K;
            global $_P;
                $black_list = array();
                $black_list = explode(',', $_K['keywordcate_denyuser']);
                if(in_array($_P['followercate_title'], $black_list)){
                        if($is_echo){
                            wx_error('Sorry!您所在的用户组权限不足');
                        } else {
                            return 'denygroup';
                        }
                }
                            return true;
}

/**
* 关键词  用户组状态
* $type : $is_echo=false
* @return echo  retrun 
*/
function get_user_auth(){
        global $_W;
            // 用户信息初始化
            // 缓存模式: $userinit = $Wxuser->where($Condition)->cache(true,86400,'File')->find();
            
            $userinit = D("WeixinmemberView")->where(array('fromusername' => $_W['fromusername']))->find();
            // 调试模式  只为管理组开启   默认为admin组
            $admin_group = array('admin');
                if(in_array($userinit['followercate_title'], $admin_group)){
                         defined('WEIXIN_TRACE',TRUE);
                }
            return $userinit;
}

/**
* 更改用户关注状态
* param : $fromusername
* @return echo  id 
*/
function change_user_follow($fromusername){
    $sql    = "update ".C('DB_PREFIX')."weixinmember set follow=(follow+1)%2 where fromusername='$fromusername'";
    $res    = M('Weixinmember')->execute($sql);
    return $res;  
}

/**
* 注册用户信息
* param : $fromusername
* @return echo  id 
*/
function register_weixin($is_auto=false, $fromusername, $username, $password, $email){
    $nowtime = time();
    //默认密码
    $password = empty($password) ? rand_simplekeys(9) : $password; 
    //默认用户名
    
    if($is_auto){
        $username = rand_simplekeys(9);
        //默认邮箱
        $email    = empty($email) ? $username.'@qq.com' : $email; 
    } else {
        //默认邮箱
        $email    = empty($email) ? rand_simplekeys(9).'@qq.com' : $email; 
    }
        //同步注册Uenter
        $User = new \User\Api\UserApi;
        $uid  = $User->register($username, $password, $email);
        if(0 < $uid){ //注册成功
            //以后 发送注册成功邮件
            $userdata = array(
                'fromusername'=>$fromusername,
                'group'       =>'general',
                'follow'      =>1,
                'status'      =>1,
                'sex'         =>1,
                'lasttime'    =>$nowtime,
                'regtime'     =>$nowtime,
                'ucmember'    =>$uid,
                'ucusername'  =>$username,
                'ucpassword'  =>$password,
            );
            // //写入 微信用户关注表
            M('Weixinmember')->add($userdata);
            $userinfo = D('WeixinmemberView')->where(array('ucmember'=>$uid))->find();
            return $userinfo;
        } else {
            //注册失败，显示错误信息
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
            wx_error($error);
        }
}

/**
* 注册用户信息
* param : $fromusername
* @return echo  id 
*/
function set_nickname($fromusername, $username){
    $res = M('Weixinmember')->where(array('fromusername'=>$fromusername))->save(array('nickname'=>$username));
    wx_success($username.'小主,我们收到咯!');
}

/**
* 随机字符串
* @return echo  id 
*/
function rand_simplekeys($length){
     $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ_#!'; //字符池
     $key     = '';
     for($i=0;$i<$length;$i++){
            $key.=$pattern[mt_rand(0,60)];//生成php随机数
     }
            return $key;
}

/**
* 执行行为
* @return echo  id 
*/
function excute_behavior($behavior){
    if(!empty($behavior)){
        wx_error('执行行为');
    }
}
/**
* 执行锁定
* @return echo  id 
*/
function excute_lock($model,$content){
        $nowtime = time();
        switch ($model) {
            case 'model':
                $info = empty($content) ? array('lastmodel'=>'') : array('lastmodel'=>$content.'|'.$nowtime); 
                break;
            case 'click':
                $info = empty($content) ? array('lastclick'=>'') : array('lastclick'=>$content.'|'.$nowtime); 
                break;            
            default:
                $info = empty($content) ? array('lastkeyword'=>'') : array('lastkeyword'=>$content.'|'.$nowtime);
                break;
        }

               $fromusername = session('from');
               $a = M('Weixinmember')->where(array('fromusername'=>$fromusername))->save($info);
}
/**
* 执行缓存
* @return echo  id 
*/
function excute_cache($keyid,$content,$cache_time,$is_echo=false){
    if(!empty($keyid)){
        $keyword_cachename = session('from').'cache'.$keyid;
        if(empty($content)){
            $cache_contxt = S($keyword_cachename);
            if(!empty($cache_contxt)){
                if($is_echo){
                    echo $cache_contxt;die;
                } else {
                    return $cache_contxt;
                }
            }
        } else {
            if($cache_time>0){
                $cache_contxt = S($keyword_cachename,$content,$cache_time);
            }           
        }
    }
}
?>