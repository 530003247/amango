<?php
//主题信息
return  array(
	//主题信息
	'INFO'   => array(
		//主题名称
            'title'    => '门户主题', 
            'author'   => '开发者',
            'version'  => 'v1.0',
        //主题简介      
            'description'    => '现代主题主题简介',
		),
	//主题配置参数
	'CONFIG' => array(
		//浏览器限制 Auto 全部兼容  Weixin 仅限微信浏览器  Tablet 仅限平板电脑  Pc 仅限PC浏览器 Mobile 仅限移动端
		    'browser_limit' => 'Auto' ,
		//主题内资源路径 
            'assetpath'     => 'ASSET',
		),
);
//主题中用到的函数方法