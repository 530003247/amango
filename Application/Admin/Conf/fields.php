<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
/**
 * 字段类型配置
 */
return array(
    'AMANGO_FIELDS' => array(
		        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
		        'string'    =>  array('单行文本','varchar(255) NOT NULL'),
		        'textarea'  =>  array('多行文本','text NOT NULL'),
		        'time'      =>  array('时间戳','varchar(50) NOT NULL'),
		        'datetime'  =>  array('日期时间','int(10) NOT NULL'),
		        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
		        'select'    =>  array('下拉框单选','char(50) NOT NULL'),
		    	'radio'		=>	array('单选','char(10) NOT NULL'),
		    	'checkbox'	=>	array('多选','varchar(100) NOT NULL'),
		    	'editor'    =>  array('编辑器','text NOT NULL'),
		    	'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
		    	'kingpicture'    =>  array('图片管理器','varchar(255) NOT NULL'),
                'kinguploadfile' =>  array('文件管理器','varchar(255) NOT NULL'),
		    	'file'    	=>  array('上传附件','int(10) UNSIGNED NOT NULL'),
		    	'manyselect'=>  array('多级联动','text NOT NULL'),
		    	'laiyuan'   =>  array('来源[Select]','char(50) NOT NULL'),
		    	'laiyuanbox' =>  array('来源[Checkbox]','varchar(255) NOT NULL'),
		    	'function'  =>  array('自定义函数','char(50) NOT NULL'),

    )
);
?>