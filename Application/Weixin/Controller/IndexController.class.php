<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com> 'text','image','voice','video','location','link'
// +----------------------------------------------------------------------
namespace Weixin\Controller;

class IndexController{
	protected $event_safe  = array('text','image','voice','video','location','link');
    protected $event_alias = array('text'=>'Common');

	    public function __construct() {
	    	$Baseclass = "Weixin\Controller\\BaseController";
            new $Baseclass();
	    }

		public function index(){
			//判断是否存在模式锁定
			global $_W;
			$event       = empty($_W['event'])?self::discrevent($_W['msgtype']):$_W['event'];
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
	            $Bundleclass = "Weixin\Bundle\\".$_W['post']."Bundle";
	            $Bundle      = new $Bundleclass();
	            $Bundle->run();
			}
		}

		protected function discrevent($event){
			//TODO 此处 若腾讯新出接口  可以增加数组新元素
            if(in_array($event, $this->event_safe)){
            	return strtr(strtolower($event), $this->event_alias);
            } else {
            	wx_error('Sorry,暂无此类相关的处理Bundle');
            }
		}
}