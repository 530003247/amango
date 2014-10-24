<?php
/**
 * 贴吧获取数据插件配置
 * @author 拉开让哥单打
 */
	return array(
		'tieba_name'=>array(
			'title'=>'要抓取的贴吧名称:',
			'type'=>'text',
			'value'=>'300px'
		),
		'tieba_nums'=>array(
			'title'=>'单次回复帖子数量:',
			'type'=>'text',
			'value'=>'300px'
		),
		'tieba_jinghua'=>array(
			'title'=>'是否保留精华帖子:',
			'type'=>'select',
			'options'=>array(
				'1'=>'显示',
				'0'=>'隐藏',
			),
			'value'=>1
		),
		'tieba_extra'=>array(
			'title'=>'是否显示帖子全部参数(评论,时间,作者等等):',
			'type'=>'select',
			'options'=>array(
				'1'=>'显示',
				'0'=>'隐藏',
			),
			'value'=>1
		),
		'tieba_cache'=>array(
			'title'=>'缓存时间:',
			'type'=>'text',
			'value'=>'300px'
		),
	);
					