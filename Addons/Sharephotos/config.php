<?php
return array(
	'title'=>array(
		'title'=>'图吧顶部标识:',
		'type'=>'text',
		'value'=>'芒果图吧'
	),
	'pagenums'=>array(
		'title'=>'每页显示数量:',
		'type'=>'text',
		'value'=>'8'
	),
	'shareitmes'=>array(
		'title'=>'每天分享限制:',
		'type'=>'text',
		'value'=>'20'
	),
	'random'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'是否开启审核:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'0'=>'开启',		 //值=>文字
			'1'=>'关闭',
		),
		'value'=>'1',			 //表单的默认值
	),
);