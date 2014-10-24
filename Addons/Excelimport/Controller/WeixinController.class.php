<?php
namespace Addons\Excelimport\Controller;
use Common\Controller\Bundle;

/**
 * Excelimport微信处理Bundle
 */
class WeixinController extends Bundle{
	public function index($param){
        global $_W,$_K;
        $content         = $_W['content'];
        $keyword_content = $_K['keyword_content'];
        //过滤后的数据
        $xuehao = str_replace($keyword_content, '', $content);
		$fir    = M('addonsdiychaxun')->where(array($param))->find();
		$rules  = unserialize($fir['rules']);

		$parxuehao = explode($rules['weixinprx'], $xuehao);
		$condition = array();
		//查询关系
		$condition['_logic'] = strtoupper($rules['logic']);
		$conditionnum = 0;
		//是否是SQL直接执行
		if($rules['modeltype']=='sql'){
			$info = M()->query($rules['condition']);
		} else {
			//配置查询参数
			foreach ($rules['condition'] as $key => $value) {
				$condition[$key] = array($value[0],($value[1]=='SELF') ? $parxuehao[$conditionnum] : $value[1]);
				$conditionnum++;
			}
			$table_info = M('addonsexcel')->where(array('id'=>$fir['tableid']))->field('tablename,fileds')->find();
			//数据表名
			$tablename  = str_replace(C('DB_PREFIX'), '', $table_info['tablename']);
			//读取记录数
			$info       = M($tablename)->where($condition)->field($table_info['fileds'])->select();
            
            if(empty($info)){
                $this->error('亲,暂无查询到与'.$xuehao.'相关的信息');
            } else {
            	$Str    = '';
                $fields = array_map(function($a){ 
                            return '['.strtoupper($a).']';
                         },explode(',', $table_info['fileds']));
            	foreach ($info as $key => $value) {
            		$Str .= str_replace($fields,$value,$fir['tpl']);
            	}
            }
		        $this->assign('Text',$Str);
		        $this->display();
		}
	}
	public function xmltags(){
        return '这是插件tags';
	}
	public function run(){
        return '';
	}
}
