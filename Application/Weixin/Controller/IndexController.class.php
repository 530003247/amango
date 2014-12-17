<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Controller;

class IndexController{
	//安全的请求模型
	protected $event_safe      = array('text','image','voice','video','location','link','common');
	//事件类型别名定义
    protected $event_alias     = array('text'=>'common');
    //通用事件请求
    protected $Common_request  = array('common');
    //公众号配置
    protected $Account_info    = array();

	    public function __construct() {
	        //判断hash是否存在
	        if(empty($_GET["hash"])){
	            echo '#10000非法请求';
	        }
	        G('begin');
	        $info = S('Accountinfo');
	        if(empty($info)){
	            $info = M("Account")->where(array('account_oldid'=>$_GET["hash"]))->find();
	            $info['Weixin_trace'] = true;
	            S('Accountinfo',$info);
	        }
	        $info['Weixin_trace'] = true;
	        //判断hash是否存在
	        if(empty($info)){
	            echo '#10001账号不存在';
	        } else {
	        	$this->Account_info   = $info;
	        }
	        global $_G;
	               $_G = $info;
	        $token = $info['account_token'];
	        import('Weixin.ORG.Weixin');
	        $wechatObj = new \Weixin($_GET["echostr"],$token,$GLOBALS["HTTP_RAW_POST_DATA"],$_GET["signature"],$_GET["timestamp"],$_GET["nonce"]);
	        $wechatinfo = $wechatObj->responseMsg();
	        if (is_array($wechatinfo)&&!empty($token)){
	            session('token',$token);
	            session('from',$wechatinfo['fromusername']);
	            session('openid',$wechatinfo['fromusername']);
	            session('to',$wechatinfo['tousername']);
	            //过滤器行为
	            hook('weixin_begin_filter',&$wechatinfo);
	            global $_W;
	            $_W = $wechatinfo;
	            //预处理行为
	            hook('weixin_begin');
	        } else {
	            echo '这是普通请求';
	        }
	    }
		public function index(){
			//判断是否存在模式锁定
			global $_W;
			$event     = empty($_W['event'])?self::discrevent($_W['msgtype']):$_W['event'];
			//新增芒果自定义参数 user_post
			$_W['post']  = ucfirst(strtolower($event));            
			$lastmodel = get_line_model();
			$crosstime = time()-$lastmodel[1];
			if(!empty($lastmodel[0])&&$crosstime<=300){
				$addonparam = explode('/', $lastmodel[0]);
				defined ( 'AMANGO_ADDON_NAME' )   or define ( 'AMANGO_ADDON_NAME', ucfirst($addonparam[0]));
                $publicpath = str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', ONETHINK_ADDON_PATH.AMANGO_ADDON_NAME.'/Public/');
                defined ( 'ADDON_PUBLIC' ) or define ( 'ADDON_PUBLIC', $publicpath );
                defined ( 'ADDON_ROOT' )   or define ( 'ADDON_ROOT', ONETHINK_ADDON_PATH.AMANGO_ADDON_NAME.'/');
				Amango_Addons($addonparam[0],'',$addonparam[1],'',fasle);
			} else {
				//是否是通用请求
				if(in_array($event, $this->Common_request)){
	                $keyword_count = get_posttype_nums($_W['msgtype']);
	                if ($keyword_count == 0){
	                	Reply::trace('Sorry!查询到0条有关【'.$_W['msgtype'].'】类型的请求');
	                }
                    $preg_keword = get_keyword_match(true);
                    if(empty($preg_keword['id'])){
                        Reply::trace('查询不到关键词哦~');
                    }
                    //关键词组和用户权限判断
                    get_keyword_user_auth(true);
                    Reply::limit_top();
                    Reply::cache($preg_keword['id'],'',$preg_keword['keyword_cache'],true);
                    Reply::response();
				} else {
			        Reply::runBundle(strtolower($event));
				}
			}
		}
		protected function discrevent($event){
			$event = strtr(strtolower($event), $this->event_alias);
			//TODO 此处 若腾讯新出接口  可以增加数组新元素
			// if(file_exists(APP_PATH.'Common/Factory/'.ucfirst($event).'Factory.class.php')){
			// }
			$factory_event = array_merge(Reply::getResponseType(),$this->event_safe);

            if(in_array($event, $factory_event)){
            	return $event;
            } else {
            	Reply::trace('Sorry,暂无此类相关的处理Bundle');
            }
		}
}