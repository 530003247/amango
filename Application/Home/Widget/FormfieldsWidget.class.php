<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 * 用法  在模板任意位置插入
 * {:W('Formfields/show', array(常用参数数组[type,name,extra], 编辑数据,'add(edit)',辅助参数[class,id,style],默认值))}
 * 例子
 * {:W('Formfields/show', array(array('type'=>输出类型,name=>字段提交的name值,'extra'=>调用数据模型名), '','add',array('class'=>'input-medium','style'=>'width:250px;'),''))}
 * 
 */

class FormfieldsWidget extends Controller{
	/* 显示指定分类的同级分类或子分类列表 */
	public function show($field, $data,$type = 'add',$style='',$default=''){
        $default = empty($default) ? $field['value'] : $default;
		$this->assign('type', $type);
		$this->assign('field', $field);
		$this->assign('data', $data);
		$this->assign('style', $style);
		$this->assign('default', $default);
		$this->display('Formfields/'.$field['type']);
	}
}
