<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Common\Api;
class SystemApi {
    /**
     * 获取主题信息
     * @return array 主题信息数组
     */
    public static function getThemeinfo($themename,$config=''){
	    $themename = empty($themename) ? C('default_theme') : $themename;
    	$themepath = AMANGO_FILE_ROOT . '/Application/Home/'.C('default_v_layer').'/'.$themename;
    	//获取主题参数
    	$themepath = $themepath.'/Config.php';
    	if(file_exists($themepath)){
    		$themeconfig = include_once($themepath);
    	}
    	    $themeconfig['INFO']['preview'] = __ROOT__.'/Application/Home/'.C('default_v_layer').'/'.$themename.'/preview.jpg';
    	    $themeconfig['INFO']['name']    = $themename;
    	if(in_array($config,array('INFO','CONFIG'))){
            return $themeconfig[$config];
    	} else {
            return $themeconfig;
    	}
    }
}