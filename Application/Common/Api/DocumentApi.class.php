<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Common\Api;
class DocumentApi {
    /**
     * 获取文档列表 Document
     * @param   num       $p          当前
     * @param   array|num $categoryid 所属文档
     * @param   num       $list_row   每页记录
     * @param   bool      $extend     是否读取子文档
     * @return  array  文档列表
     */
    public static function get_list($p = 1, $categoryid, $list_row, $extend=true){
    	if(empty($categoryid)){
            return '文档列表的分类属性必须为数字';
    	}
    	//获取分类属性
    	$category = is_array($categoryid) ? $categoryid : get_category($categoryid);
        //获取分类属性
    	$list_row = empty($list_row) ? $category['list_row'] : $list_row;
		/* 获取当前分类列表 */
		$Document = D('Document');
		$list = $Document->page($p, $list_row)->lists($category['id']);
		if(false === $list){
			return '获取文档列表数据失败';
		}
		//判断是否读取子文档
		if(true===$extend){
			$newlist   = array();
			$modellist = self::get_sublist($category['model']);
			foreach ($list as $key => $value) {
				$newlist[$key] = array_merge($value,$modellist[$value['id']]);
			}
		} else {
			    $newlist = $list;
		}
		return $newlist;
    }
    /**
     * 获取子文档列表 Document_
     * @param   array|num  $modelid  模型ID
     * @return  array                子模型列表
     */
    public static function get_sublist($modelid){
    	$model_id    = is_array($modelid) ? $modelid[0] : $modelid;
		$model_title = get_document_model($model_id);
		$sublist     = array();
		$newsublist  = array();
		$sublist     = M('Document'.ucfirst($model_title['name']))->select();
		//读取附属全部字段
		foreach ($sublist as $key => $value) {
			$newsublist[$value['id']] = $value;
			unset($newsublist[$value['id']]['id']);
		}
		return $newsublist;
    }
    /**
     * 获取子文档列表 Document_
     * @param   array|num  $modelid  模型ID
     * @return  array                子模型列表
     */
    public static function get_detail($id){
		$Document = D('Document');
		$info = D('Document')->detail($id);
		if(!$info){
			return array('status'=>false,'info'=>$Document->getError());
		}
		return $info;
    }
}