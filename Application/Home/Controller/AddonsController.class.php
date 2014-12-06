<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;

/**
 * 扩展控制器
 * 用于调度各个扩展的URL访问需求
 */
class AddonsController extends Controller{
    protected $view   = null;
	protected $addons = null;

    public function __construct(){
        $this->view         =   \Think\Think::instance('Think\View');
        // $this->addon_path   =   ONETHINK_ADDON_PATH.$this->getName().'/';
        // $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
        // $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__ . '/Addons/'.$this->getName();
        // C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
    }

	public function execute($_addons = null, $_controller = null, $_action = null){
		if(C('URL_CASE_INSENSITIVE')){
			$_addons = ucfirst(parse_name($_addons, 1));
			$_controller = parse_name($_controller,1);
		}
            A('Home/Amangotheme')->init_config('addons');
		if(!empty($_addons) && !empty($_controller) && !empty($_action)){
			//插件状态是否启用判断
			$addon_status = M('Addons')->where(array('name'=>ucfirst($_addons),'status'=>1))->count();
			if($addon_status==0){
			    $this->error('请先确认该插件是否安装！');
			}
			$addonsmodel  = A("Addons://{$_addons}/{$_controller}");
			//判断该操作是否存在
			$actionlist   = get_class_methods($addonsmodel);
			if(!in_array($_action, $actionlist)){
                $this->error('非法操作！');
			}
			//登陆操作权限
			$login_action = array_change_key_case($addonsmodel->login_action,CASE_LOWER);
			if(!empty($login_action[$_action])&&is_array($login_action)){
					$this->is_login($login_action[$_action]['errormsg'],$login_action[$_action]['errorurl']);
			}
			//禁止操作权限
            $deny_action  = $addonsmodel->deny_action;
            $deny_action = array_change_key_case($addonsmodel->deny_action,CASE_LOWER);
			if(!empty($deny_action)&&is_array($deny_action)){
				if(in_array($_action, $deny_action)){
					$this->error('请勿执行非法操作！');
				}
			}

			defined ( '_ADDONS' ) or define ( '_ADDONS', $_addons );
			defined ( '_CONTROLLER' ) or define ( '_CONTROLLER', $_controller );
			defined ( '_ACTION' ) or define ( '_ACTION', $_action );
		    $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
            $TMPL_PARSE_STRING['__ADDONROOT__'] = __ROOT__.'/Addons/'.$_addons.'/Public';
            $TMPL_PARSE_STRING['ADDON_PUBLIC']  = __ROOT__.'/Addons/'.$_addons.'/Public';
            C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
			$Addons = $addonsmodel->$_action();
		} else {
			$this->error('没有指定插件名称，控制器或操作！');
		}
	}

	protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
		$templateFile = $this->getAddonTemplate ( $templateFile );
		$this->view->display ( $templateFile, $charset, $contentType, $content, $prefix );
	}

	function getAddonTemplate($templateFile = '') {
		if (file_exists ( $templateFile )) {
			return $templateFile;
		}
		$templatefile = $templateFile;
		if (empty($templateFile)) {
			//默认为本操作
			$templateFile = T('Addons://'._ADDONS.'@'._CONTROLLER.'/'._ACTION );
		} elseif (stripos($templateFile,'/Addons/') === false && stripos($templateFile,THINK_PATH) === false) {
			if (stripos ($templateFile,'/') === false) {
				//单操作
				$templateFile = T ( 'Addons://' . _ADDONS . '@' . _CONTROLLER . '/' . $templateFile );
			} elseif (stripos ($templateFile,'@') === false) { 
				//控制器/操作方法
				$templateFile = T ('Addons://'._ADDONS.'@'.$templateFile);
			}
		}

		if (stripos($templateFile,'/Addons/') !== false && ! file_exists($templateFile)) { 
			$templateFile = !empty($templatefile) && stripos($templatefile,'/')===false ? $templatefile : _ACTION;
		}
		return $templateFile;
	}

    final public function getName($name=__METHOD__){
        $class = get_class($this);
        $newclass = explode('\\',$class);
        return $name;
        //return $newclass[1];
    }

    final public function is_login($errormsg='请先登陆个人中心！',$errorurl=''){
    	$errorurl = empty($errorurl) ? U('User/login') : $errorurl;
        $userinfo = session('P');
        if(empty($userinfo)){
            $this->error($errormsg, $errorurl);
        }
            return true;
    }
}
