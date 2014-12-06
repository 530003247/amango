<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Widget;
use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class CategoryWidget extends Action{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($cate, $child = false, $filter = false){
		$field = 'id,name,pid,title,link_id,icon';
		if($child){
			$category = D('Category')->getTree($cate, $field);
			$category = $category['_'];
		} else {
			$category = D('Category')->getSameLevel($cate, $field);
		}
		//芒果新增 分类为空筛选
		if($filter){
			$Documents = M('Document');
			foreach ($category as $key => $value) {
				$nums = $Documents->where(array('category_id'=>$value['id'],'status'=>1))->count();
				$category[$key]['category_nums'] = $nums;
				if($category[$key]['category_nums'] =='0'){
                    unset($category[$key]);
				}
			}
		}
		$this->assign('category', $category);
		$this->assign('current', $cate);
		$this->display('Category/lists');
	}
	
}
