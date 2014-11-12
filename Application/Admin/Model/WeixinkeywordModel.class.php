<?php
namespace Admin\Model;
use Think\Model;

/**
 * 微信关键词模型
 * @author 拉开让哥单打 <530003247@vip.qq.com>
 */

class WeixinkeywordModel extends Model {
    protected $defaultconfig = array(
		'keyword_post'     => 'text',
		'keyword_down'     => 1,
		'keyword_top'      => 0,
		'keyword_group'    => 1,
		'keyword_cache'    => 0,
		'click_model'      => '',
		'status'           => 1,
		'lock_model'       => '',
		'keyword_reaponse' => '',
    );
    /***
      *  必须提供参数 [type]请求类型 keyword_post  [rules]验证规则  keyword_rules
      ***/
    public function create_post($amangoajax){
    	if(empty($amangoajax['keyword_rules'])||empty($amangoajax['keyword_post'])){
            return false;
    	}

    	$date    = array();
    	$default = $this->defaultconfig;
		//请求类型判断
		$date['keyword_post']    = empty($amangoajax['keyword_post']) ? $default['keyword_post'] : ucfirst($amangoajax['keyword_post']);
		//下文关键词是否开启
		$date['keyword_down']    = ($amangoajax['keyword_down']==1) ? $default['keyword_down'] : 0;
		//上文继承关键词
		$date['keyword_top']     = is_numeric($amangoajax['keyword_top']) ? $amangoajax['keyword_top'] : $default['keyword_top'];
		//关键词所属分组
		$date['keyword_group']   = empty($amangoajax['keyword_group']) ? $default['keyword_group'] : $amangoajax['keyword_group'];  
		//关键词缓存
		$date['keyword_cache']   = is_numeric($amangoajax['keyword_cache']) ? $amangoajax['keyword_cache'] : $default['keyword_cache']; 
		//标签隐藏 显示 关闭
		$date['denytag_keyword'] = serialize(parse_config($amangoajax['denytag_keyword'],$type=3));
		//后续行为初始化
		$date['after_keyword']   = serialize(parse_config($amangoajax['after_keyword'],$type=3));
		//菜单模式初始化
		$date['click_model']     = is_numeric($amangoajax['click_model']) ? $amangoajax['click_model'] : $default['click_model'];
		//关键词有效期判断
		$date['keyword_start']   = time();
		$date['keyword_end']     = $date['keyword_start']+31536000;
		//默认排序
		$date['sort']            = time();
		//默认状态
		$date['status']          = $default['status'];
		//默认正则规则
		$date['keyword_rules']   = $amangoajax['keyword_rules'];
		//规则标识
		$date['keyword_reply']   = '';
		//规则标识
		$date['before_keyword']  = '';
		//锁定状态 
		$date['lock_model']      = empty($amangoajax['lock_model']) ? $default['lock_model'] : $amangoajax['lock_model'];
		//本体
		$date['keyword_content'] = empty($amangoajax['keyword_content']) ? $amangoajax['keyword_rules'] : $amangoajax['keyword_content'];
		//默认响应体
		$date['keyword_reaponse']= is_numeric($amangoajax['keyword_reaponse']) ? $amangoajax['keyword_reaponse'] : $default['keyword_reaponse'];
		//点击次数
		$date['keyword_click']  = 1;
		return $date;
    }
    /***
      *  必须提供参数 [apiid]对应的插件ID apiid  [response_name]简介名称  response_name
      ***/
    public function create_response($resdata){
    	if(!is_numeric($resdata['apiid'])||empty($resdata['response_name'])){
            return false;
    	}
    	    $data   = array();
            $param  = parse_config($resdata['param']);
            //回复类型判断
            $data['response_reply']   = 'Api'; 
            //回复体标识
            $data['response_name']    = empty($resdata['response_name']) ? '本地接口,ID'.$resdata['apiid'] : $resdata['response_name'];
            //结构组合体 
            $neiron = array('type' => 'local', 'num' => 1, 'neiron' => $resdata['apiid'], 'replace' => $param);
            $data['response_compos']  = serialize($neiron);
            //生成XML
            $data['response_xml']     =  '';
            //数据静态化
            $data['response_static']  =  0;
            $data['status']           =  1;

            return $data;
    }
}
