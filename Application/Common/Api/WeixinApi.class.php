<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
/**
 * 微信高级接口设置  json内的中文记得【urlencode】
 * @param api('所在模块[默认为Common模块]/接口标识',type[Token类型Oauth Shop Tiot Common])
 * @uses  【用法1】 api('Weixin','Common',false)->方法();//直接执行接口
 * @uses  【用法2】 $api = api('Weixin','Common',false);//实例化
 *                  $api->方法();     //直接执行接口
 *                  $api->getStatus();//获取最后状态
 */
namespace Common\Api;
class WeixinApi {
    public $Commonparam         = null;
    public $funcname            = null;
    public $Status              = array();
    public $Common_appid        = null;
    public $Common_appsecret    = null;
    public $Common_astoken      = array();
    public $Common_token        = null;
    public $Common_token_time   = 7190;
    public $ShopApiurl   = array();
    public $OauthApiurl  = array();
    public $TiotApiurl   = array();
    public $CommonApiurl = array(
        //基础Token
            'get_CommonToken'      => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential',  //获取access_token
            'get_CallbackIp'       => 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=',         //获取服务器IP
        //资源上/下载
            'upload_Media'         => 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=',      //上传资源
            'download_Media'       => 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=',         //下载资源
        //推广工具
            'create_Ticket2Qr'     => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=',         //二维码设置
            'get_Qr2Ticket'        => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=',                   //二维码设置 
            'search_Content'       => 'https://api.weixin.qq.com/semantic/semproxy/search?access_token=',      //语义理解请求
            'create_ShortUrl'      => 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=',              //长转短链接
        //自定义菜单
            'create_CommonMenu'    => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=',           //创建菜单
            'delete_CommonMenu'    => 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=',           //删除菜单
            'get_CommonMenu'       => 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=',              //查看菜单
        //消息群发    
            'send_CustomMsg'       => 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=',   //发送客服消息
            'send_MassGroup'       => 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=',  //群发消息  分组
        //模板消息
            'send_TemplateMsg'     => 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=', //获得模板ID
        //用户组
            'create_Groups'        => 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=',         //创建用户分组
            'get_GroupsList'       => 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=',            //查询用户分组列表
            'get_IdinGroups'       => 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=',          //查询用户所在分组
            'update_GroupsName'    => 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token=',         //修改分组名
            'update_MembersGroups' => 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=', //移动用户分组
        //获取用户基本信息（包括UnionID机制）
            'get_MembersInfo'      => 'https://api.weixin.qq.com/cgi-bin/user/info?lang=zh_CN&access_token=',
            'marks_Members'        => 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=',//备注名 微信认证
            'get_MembersList'      => 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=',              //获取关注者列表
    );
   /**
     * 架构函数 微信公众号 AP  AK
     * 状态判断 $this->Status
     */
    public function __construct($type='Common') {
        $account = S('NOW_ACCOUNT');
        if(empty($account)){
            //获取公众号的APPID SECRET
            $account = M('Account')->where(array('account_default'=>'default'))->field('id,account_appid,account_secret,account_astoken')->find();
            $this->Common_appid     = $account['account_appid'];
            $this->Common_appsecret = $account['account_secret'];
            $this->Common_astoken   = explode('|', $account['account_astoken']);
        } else {
            $this->Common_appid     = $account[0];
            $this->Common_appsecret = $account[1];
            $this->Common_astoken   = $account[2];
        }
        if(empty($this->Common_appid)||empty($this->Common_appsecret)){
            return $this->Status = array(false,'请配置微信公众号的APPID和SECRET');
        }
        //缓存账号信息
        S('NOW_ACCOUNT',array($this->Common_appid,$this->Common_appsecret,$this->Common_astoken));
        //读取token Oauth[网页授权] Tiot[智能硬件] Common[通用授权] Shop[微信小店]
        switch ($type) {
            case 'OAuth':
                $this->get_OauthToken();
                break;
            case 'Common':
                $this->get_CommonToken();
                break;
            case 'Tiot':
                $this->get_TiotToken();
                break;
            case 'Shop':
                $this->get_TiotToken();
                break;
            default:
                return $this->Status = array(false,'选择正确Api接口:Oauth[网页授权]Tiot[智能硬件]Common[通用授权]Shop[微信小店]');
                break;
        }
        if(empty($this->Common_token)){
            return $this->Status = array(false,'Token无效！');
        }
    }
    public function getStatus(){
        return $this->Status;
    }
    //获取通用token 获取token 采用数据库表储存
    public function get_CommonToken() {
        $cachename          = md5($this->Common_appid.'_ACCESS_TOKEN');
        $this->Common_token = S($cachename);
        // 测试 TOKEN变化过程
        // echo 'OLD:'.$this->Common_token.'<br>';
        // echo 'AP :'.$this->Common_appid.'<br>';
        // echo 'AK :'.$this->Common_appsecret.'<br>';
        //判断Common_token缓存是否存在 可能缓存清空  可能无法使用
        if(empty($this->Common_token)||$this->Common_token=='notoken'){
            //获取公众号的APPID SECRET
            $nowtime    = time();
            $duringt    = $nowtime-$this->Common_astoken[1];
            //如果access_token为空 或者 超过有效期限
            if(empty($this->Common_astoken[0])||$duringt>$this->Common_token_time){
                //获取TOKEN
                $api  = $this->CommonApiurl['get_CommonToken']."&appid=".$this->Common_appid."&secret=".$this->Common_appsecret;
                $json = file_get_contents($api);
                $JT   = json_decode($json);
                    $this->Common_token = $JT->access_token;
                    if(empty($this->Common_token)){
                        $this->Status = array(false,$JT->errmsg);
                    } else {
                        //再次存入数据库并写入缓存
                        $this->Status = array(true,$JT->access_token);
                    }
            } 
        }
        // echo 'NEW:'.$this->Common_token.'<br>';
                    $map['account_appid']    = $this->Common_appid;
                    $map['account_secret']   = $this->Common_appsecret;
                    $data['account_astoken'] = $this->Common_token.'|'.$nowtime;
                    M('Account')->where($map)->save($data);
                    S($cachename,$this->Common_token,$this->Common_token_time);     
    }
    //统一入口 分为Oauth[网页授权应用] Tiot[物联网智能硬件] Common[通用授权] Shop[微信小店]
    public static function get_ShopToken() {
        $this->Status = array(true,'正在完善微信小店接口');
        return $this->Status;
    }
    public static function get_OauthToken() {
        $this->Status = array(true,'正在完善Oauth接口');
        return $this->Status;
    }
    public static function get_TiotToken() {
        $this->Status = array(true,'正在完善硬件接口');
        return $this->Status;
    }
/**
 * 查看菜单
 */ 
    public function get_CommonMenu($menujson) {
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($menujson);
        $url = $this->CommonApiurl['get_CommonMenu'].$this->Common_token;
        $JR  = $this->request($url,'','en');
        if(!empty($JR)){
            $this->Status = array(true,'查看菜单：'.$JR);
        }
            return $this->Status;
    }
/**
 * 设置菜单
 * @param menujson 菜单json
 */
    public function create_CommonMenu($menujson){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($menujson);
        if(empty($menujson)){
            return $this->Status = array(false,'菜单json不能为空');
        }
        $url     = $this->CommonApiurl['create_CommonMenu'].$this->Common_token;
        $JR      = $this->request($url,$menujson);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 清除菜单
 */
    public function delete_CommonMenu(){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $url = $this->CommonApiurl['delete_CommonMenu'].$this->Common_token;
        $JR  = $this->request($url);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 上传至素材媒体库
 * @param groupname用户分组名 确保分组名不重复
 */
    public function upload_Media($type,$filepath){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($type,$filepath);
        if(empty($type)||empty($filepath)){
            return $this->Status = array(false,'资源类型,资源路径不能为空');
        }
        $param['type']  = $type;
        $param['media'] = $filepath;
        $content        = json_encode($param,true);
        $url     = $this->CommonApiurl['upload_Media'].$this->Common_token.'&type='.$type;
        $JR      = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 下载素材
 */
    public function download_Media($downloadpath,$mediaid){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($downloadpath,$mediaid);
        if(empty($downloadpath)||empty($mediaid)){
            return $this->Status = array(false,'资源存放路径,资源ID不能为空');
        }
        $url = $this->CommonApiurl['download_Media'].$this->Common_token.'&media_id='.$mediaid;
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        //TODO  根据头部Content-Type  进行相关抓取  preg_match('/Content-Type:(.*);/iU',$content,$str); 
        //header('Content-type: image/jpg');//抓取图片
        return $this->Status = array(false,'通常不会下载资源');
    }
/**
 * 用户备注
 * @param groupname用户分组名 确保分组名不重复
 */
    public function marks_Members($openid,$remark){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($openid,$remark);
        if(empty($openid)||empty($remark)){
            return $this->Status = array(false,'用户标识,用户备注名不能为空');
        }
        $param['openid'] = $openid;
        $param['remark'] = urlencode($remark);
        $content = urldecode(json_encode($param,true));
        $url     = $this->CommonApiurl['marks_Members'].$this->Common_token;
        $JR      = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 获取单用户信息
 * @param openid 用户标识
 */
    public function get_MembersInfo($openid){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($openid);
        if(empty($openid)){
             return $this->Status = array(false,'用户标识不能为空');
        }
        $url = $this->CommonApiurl['get_MembersInfo'].$this->Common_token.'&openid='.$openid;
        $JR  = $this->request($url,'');
        if(!empty($JR['errmsg'])){
            return $this->Status = array(false,$JR);
        } else {
            return $this->Status = array(true,$JR);
        }
    }
/**
 * 获取用户列表
 * @param next_openid 下一页标识
 */
    public function get_MembersList($next_openid){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($next_openid);
        $url = $this->CommonApiurl['get_MembersList'].$this->Common_token.'&next_openid='.$next_openid;
        $JR  = $this->request($url,'');
        if(!empty($JR['errmsg'])){
            $this->Status = array(false,$JR);
        } else {
            if($JR['total']>$JR['count']){
                $newlist = array();
                $this->openidlist = array();
                $newlist['total']          = $JR['total'];
                $newlist['data']['openid'] = array_merge($JR['data']['openid'],$this->getopenidlist($JR['next_openid']));
                return $this->Status = array(true,$newlist);
            } else {
                return $this->Status = array(true,$JR);
            }
        }
    }
    public function getopenidlist($next_openid){
        $url = $this->CommonApiurl['get_MembersList'].$this->Common_token.'&next_openid='.$next_openid;
        $JR  = $this->request($url,'');
        if(empty($JR['errmsg'])){
            if($JR['total']>$JR['count']){
                $this->openidlist = array_merge($this->openidlist,$this->getopenidlist($JR['next_openid']));
            } else {
                $this->openidlist = array_merge($this->openidlist,$JR['data']['openid']);
            }
        }
    }
/**
 * 创建用户分组
 * @param groupname用户分组名 确保分组名不重复
 */
    public function create_Groups($groupname){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($groupname);
        if(empty($groupname)){
            return $this->Status = array(false,'用户分组名称不能为空');
        }
        //避免汉字被转换
        $postdata['group']['name'] = urlencode($groupname);
        $content = urldecode(json_encode($postdata));
        $url     = $this->CommonApiurl['create_Groups'].$this->Common_token;
        $JR      = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 查询用户分组列表
 * @param $sort DESC用户数从大到小  ASC用户数从小到大
 */
    public function get_GroupsList($sort='DESC'){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($sort);
        $url = $this->CommonApiurl['get_GroupsList'].$this->Common_token;
        $JR  = $this->request($url,'');
        if(!empty($JR['errmsg'])){
            $this->Status = array(false,$JR);
        } else {
            if($sort=='ASC'){
                $newdata = array();
                $nums    = count($JR['groups']);
                $newnums = 0;
                foreach ($JR['groups'] as $key => $value) {
                    $newdata['groups'][$newnums++] = $JR['groups'][--$nums];
                }
                return $this->Status = array(true,$newdata);
            } else {
                return $this->Status = array(true,$JR);
            }
        }
    }
/**
 * 查询用户所在分组
 * @param $openid 用户标识
 */
    public function get_IdinGroups($openid){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($openid);
        if(empty($openid)){
            return $this->Status=array(false,'用户openid不能为空');
        }
        $content = json_encode(array('openid'=>$openid),true);
        $url = $this->CommonApiurl['get_IdinGroups'].$this->Common_token;
        $JR  = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }    
    }
/**
 * 更改用户组名称
 * @param $groupid 用户组ID  $newgroupname 新用户组名称
 */
    public function update_GroupsName($groupid,$newgroupname){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($groupid,$newgroupname);
        if(empty($newgroupname)||empty($groupid)){
            return $this->Status = array(false,'用户组ID,用户组名称不能为空');
        }
        $postdata['group']['id']   = $groupid;
        //避免汉字被转换
        $postdata['group']['name'] = urlencode($newgroupname);
        $content = urldecode(json_encode($postdata));
        $url     = $this->CommonApiurl['update_GroupsName'].$this->Common_token;
        $JR      = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 移动用户进新分组
 * @param $openid 用户标识  $groupid要移动的组id
 */
    public function update_MembersGroups($openid,$groupid){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($openid,$groupid);
        if(empty($openid)||empty($groupid)||!is_numeric($groupid)){
            return $this->Status = array(false,'用户ID,用户组ID不能为空');
        }
        $postdata['openid']     = $openid;
        //避免汉字被转换
        $postdata['to_groupid'] = $groupid;
        $content = json_encode($postdata,true);
        $url     = $this->CommonApiurl['update_MembersGroups'].$this->Common_token;
        $JR      = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 单点消息
 * @param touser 接收者 msgtype消息类型  jsoncontent严格按照微信官方格式
 */
    public function send_CustomMsg($touser='',$msgtype='', $content=''){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($touser,$msgtype,$content);
        if(empty($touser)||empty($msgtype)||empty($content)){
            return $this->Status=array(false,'消息类型不能为空;接收者不能为空;消息体不能为空');
        }
        $url = $this->CommonApiurl['send_CustomMsg'].$this->Common_token;
        $JR  = $this->request($url,$content);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }    
    }
/**
 * 分组群发  TODO no good
 * @param groupid 分组id msgtype消息类型  jsoncontent严格按照微信官方格式[注意中文 urlencode]
 */
    public function send_MassGroup($groupid,$msgtype,$jsoncontent){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $this->Commonparam = array($groupid,$msgtype,$jsoncontent);
        if(empty($groupid)||empty($msgtype)||empty($jsoncontent)){
            return $this->Status=array(false,'分组ID不能为空;消息类型不能为空;消息体不能为空');
        }
        $url = $this->CommonApiurl['send_MassGroup'].$this->Common_token;
        $JR  = $this->request($url,$jsoncontent);
        if(!empty($JR['errmsg'])){
                return $this->Status = array(false,$JR);
        } else {
                return $this->Status = array(true,$JR);
        }
    }
/**
 * 查看服务器IP
 */
    public function get_CallbackIp(){
        $this->funcname = __FUNCTION__;
        if(false===$this->Status[0]){
            return $this->Status[1];
        }
        $url = $this->CommonApiurl['get_CallbackIp'].$this->Common_token;
        $JR  = $this->request($url);
        if(!empty($JR['errmsg'])){
            $this->Status = array(false,$JR);
        } else {
            $this->Status = array(true,$JR);
        }
            return $this->Status;
    }
    //清除缓存和数据库中Access_Token
    protected function del_CommonToken(){
            $cachename = md5($this->Common_appid.'_ACCESS_TOKEN');
            S($cachename,'');
            $map['account_appid']    = $this->Common_appid;
            $map['account_secret']   = $this->Common_appsecret;
            $data['account_astoken'] = '|0';
            M('Account')->where($map)->save($data);
    }
/*** 通用请求工具  **/
    protected function request($url,$post,$result='de'){
        if(empty($post)){
            $output = file_get_contents($url);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $output = curl_exec($ch);
            curl_close($ch);
        }
            //echo $this->funcname;
            $status   = json_decode($output,true);
            //超过频率限制的 无法通过新Token进行请求【无能为力的请求】
            if(in_array($status['errcode'],array('48001','50001','45009','45028'))){
                return array($status['errcode'],'超过使用频率限制或者暂无使用该接口的权限');
            } else {
                //无法自动刷新Token  故采用回调函数
                //access_token无效的  重新发起请求
                if(in_array($status['errcode'],array('40014','40001','41001','41002','41004','42001'))&&!empty($this->funcname)){
                    //清除ACCES_TOKEN
                    $this->del_CommonToken();
                    //重置ACCES_TOKEN
                    $this->get_CommonToken();
                    call_user_func_array(array($this,$this->funcname),$this->Commonparam);
                } else {
                    return ($result=='de') ? $status : $output;
                }
            }
    }
}