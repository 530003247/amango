<?php
namespace Addons\Placement\Controller;
use Common\Controller\Bundle;

/**
 * Excelimport微信处理Bundle
 */
class WeixinController extends Bundle{
	public function index($param){
        return 123;
	}
	//解析TAG 映射  基本权限判断
	public function xmltags($id,$param){
		if(empty($id)||!is_numeric($id)){
            return '1';
		} else {
			global $_K;
			global $_P;
			//组装当前时间段
			$nowdate   = str_replace(':', '', date('H:i',time()));
			//判断时间
			$condition = array(
				'id'                  =>$id,
				'placement_status'    =>1,
				'placement_usergroup' =>array(array('eq',''),array('eq',$_P['cate_group']),'or'),   //判断用户组
				'_logic'              =>'AND',
			);
			$tagsinfo  = M('Addonsplacement')->where($condition)->find();
			//存在关键词分组时  匹配
			if(!empty($_K['keyword_group'])&&$tagsinfo['placement_keygroup']!=$_K['keyword_group']){
                return '';
			}
			//判断是否属于时间段
			if(empty($tagsinfo)||$nowdate<$tagsinfo['placement_start']||$nowdate>$tagsinfo['placement_end']){
                return '2';
			} else {
				$AmangoModel = new \Weixin\Model\AmangoModel;
                $content     = $AmangoModel->responseBlock('@',$tagsinfo['placement_add']);
				return $content;
			}
		}
	}
    //判断该TAG植入对应体
	// protected function tags_auth($id){

 //    }
	public function run(){
        return '';
	}
}