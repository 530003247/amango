<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

return array(
	'group'=>array(
		'type'=>'group',
		'options'=>array(
			'kuaidi'=>array(
				'title'=>'快递查询',
				'options'=>array(
					'kuaidiak'=>array(
						'title'=>'授权key:',
						'type'=>'text',
						'value'=>'',
						'tip'=>"请先到爱查快递<a href='http://www.ickd.cn/api/reg.html'>申请接口</a>"
					),
				)
			),
			'bus'=>array(
				'title'=>'公交查询',
				'options'=>array(
					'trainak'=>array(
						'title'=>'授权key:',
						'type'=>'text',
						'value'=>'',
						'tip'=>"请先到百度地图API<a href='http://developer.baidu.com/map/'>申请接口</a>"
					),
					'origin'=>array(
						'title'=>'默认始发地:',
						'type'=>'text',
						'value'=>'',
						'tip'=>"若用户不输入出发地点，则用该默认地点"
					),
					'region'=>array(
						'title'=>'默认终点站:',
						'type'=>'text',
						'value'=>'',
						'tip'=>"若用户不输入终点，则用该默认地点"
					),
					'where'=>array(
						'title'=>'默认省市:',
						'type'=>'text',
						'value'=>'',
						'tip'=>"必须填写一个默认所在的省市,例如:石狮，厦门"
					)
				)
			)
		)
	)
);
