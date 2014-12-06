<?php
namespace Home\Controller;
use Think\Controller;
class AmangothemeController extends Controller {
    public function init_theme($themename,$modulename){
        if(MODULE_NAME=='Home'){
            $themename = empty($themename) ? C('default_theme') : $themename;
            $themepath = AMANGO_FILE_ROOT . '/Application/Home/'.C('default_v_layer').'/'.$themename;
            //获取主题参数
            $themeconfig = $themepath.'/Config.php';
            if(file_exists($themeconfig)){
                $thememparam = include_once($themeconfig);
            }
            //主题运行环境
            $browserstatus = $this->check_browser($thememparam['CONFIG']['browser_limit']);
            if(true!==$browserstatus){
                echo '<script language="javascript">alert("'.$browserstatus.'");</script>'.$browserstatus;die;
            }
            //TODO 主题预定义参数
            // if(!empty($thememparam['CONFIG'])){
            // }
            //主题资源路径 默认为ASSET文件夹
            $assetpath   = empty($thememparam['CONFIG']['assetpath']) ? 'ASSET' : $thememparam['CONFIG']['assetpath'];
            $assetpath   = __ROOT__.'/Application/Home/'.C('default_v_layer').'/'.$themename.'/'.$assetpath;
            $reset_tmpl_parse = array(
                'TMPL_PARSE_STRING' => array(
                    '__PUBLIC__' => $assetpath,
                    '__STATIC__' => $assetpath . '/static',
                    '__CSS__'    => $assetpath . '/css',
                    '__JS__'     => $assetpath . '/js',
                ),
            );
            if($modulename!='addons'){
                defined('THEME_NAME') or define('THEME_NAME', $themename);
                defined('THEME_PATH') or define('THEME_PATH', $themepath.'/');
                $reset_tmpl_parse['DEFAULT_THEME'] = $themename;
            }
            C($reset_tmpl_parse);
            return true;
        }
    }
    protected function check_browser($allowtype='Auto'){
    	//判断 PC 移动浏览器
	    $detect   = import('Home.ORG.Mobile_Detect','','.php');
	    $detect   = new \Mobile_Detect;
	    $isMobile = $detect->isMobile();
	    $isTablet = $detect->isTablet();
	    if($isMobile||$isTablet){
	    	session('browser_type','wap');
	    } else {
	    	session('browser_type','pc');
	    }
    	switch ($allowtype) {
    		case 'Weixin':
				if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')==false) {
					    $errormsg = '请在微信浏览器中打开';
				}	
    			break;
    		case 'Pc':
    		    if(true===$isMobile||true===$isTablet){
                    $errormsg = '请在PC浏览器中打开';
    		    } 
    			break;
    		case 'Mobile':
    		    if(false===$isMobile&&false===$isTablet){
                    $errormsg = '请在手机或平板浏览器中打开';
    		    } 
    			break;
    		case 'Tablet':
    		    if(false===$isTablet){
                    $errormsg = '请在平板浏览器中打开';
    		    } 
    			break;
    	}
    	        return empty($errormsg) ? true : $errormsg;
    }
    public function init_config($modulename){
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        //添加配置
        C($config);

            $this->init_theme(C('WEB_SITE_THEME'),$modulename);
            if(!C('WEB_SITE_CLOSE')){
                $this->error('站点已经关闭，请稍后访问~');
            }
            //浏览器参数
            $this->assign('browser_type',session('browser_type'));
            //申明全局变量   默认网站信息
            global $_W;
                   $_W    = $config;
            $accountmodel = M('Account');
            $map = array();
            $defaultlist  = $accountmodel->where(array('account_default'=>'default'))->find();
                $map['account_default']  = array('neq','default');
            $otherlist    = $accountmodel->where($map)->select();
            //申明全局变量   默认微信公众号信息
            global $_K;
                   $_K['DEFAULT'] = $defaultlist;
                   $_K['OTHER']   = $otherlist;
            return true;
    }
}
