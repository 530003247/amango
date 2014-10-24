<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

return array(
	'excel_type'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'导入文件类型:',	 //表单的文字
		'tip'=>'通用版仅支持Excel2003以上版本',
		'type'=>'select',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'excel'=>'Excel',		 //值=>文字
		),
		'value'=>'excel',			 //表单的默认值
	),
	'excel_path'=>array(//配置在表单中的键名 ,这个会是config[random]
			'title'=>'选择上传文件:',
			'type'=>'file_union',
			'value'=>''
	),
	'group'=>array(
		'type'=>'group',
		'options'=>array(
			'base_config'=>array(
				'title'=>'基本信息',
				'options'=>array(
					'excel_tablename'=>array(
							'title'=>'生成表名:',
							'tip'=>'成功导入时,系统将新建该表名自动并将excel数据导入该表',
							'type'=>'text',
							'value'=>'tablename'
					),
					'excel_readxls'=>array(
						'title'=>'读取起始工作表',
						'tip'  =>'默认读取第一个工作表',
						'type' =>'text',
						'value'=>'1'
					),
					'excel_currentrow'=>array(
						'title'=>'读取起始行',
						'tip'  =>'默认从第二行开始读取',
						'type' =>'text',
						'value'=>'2'
					),
					'excel_parxhtml'=>array(
						'title'=>'是否过滤Html标签:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'过滤',
							'0'=>'不过滤'
						),
						'value'=>'0'
					),
				)
			),
			'prax_config'=>array(
				'title'=>'解析配置',
				'options'=>array(
					'excel_param'=>array(//配置在表单中的键名 ,这个会是config[random]
							'title'=>'解析配置:',
							'tip'  =>'多个字段请换行，格式: 存表键名|位置信息[例如:  ziduan1|A]',
							'type' =>'textarea',
							'value'=>'ziduan|A'
					)
				)
			)
		)
	)
);
