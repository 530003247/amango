<?php
namespace Addons\Excelimport\Controller;
use Common\Controller\Bundle;

/**
 * Excelimport微信处理Bundle
 */
class WeixinController extends Bundle{
	public function filter_mark($text){ 
if(trim($text)=='')return ''; 
$text=preg_replace("/[[:punct:]\s]/",' ',$text); 
$text=urlencode($text); 
$text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",' ',$text); 
$text=urldecode($text); 
return trim($text); 
} 

	public function index($param){
        global $_W,$_K;
        $param           = $this->param;
        $content         = $_W['content'];
        $keyword_content = $_K['keyword_content'];
        //过滤后的数据
        $xuehao = str_replace($keyword_content, '', $content);
        //去除所有符合
        $xuehao = $this->filter_mark($xuehao);

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
                $this->error("亲,暂无查询到与".$xuehao."相关的信息");
            } else {
            	$Str    = '';
                $fields = array_map(function($a){ 
                            return '['.strtoupper($a).']';
                         },explode(',', $table_info['fileds']));
            	foreach ($info as $key => $value) {
            		$Str .= str_replace($fields,$value,$fir['tpl']);
            	}
            }
		        $this->assign('Text',str_replace(PHP_EOL, "\n", $Str));
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
