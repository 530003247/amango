<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------

return array(
    'app_begin'     =>  array(
        'Weixin\Behavior\WxReadHtmlCache', // 关闭html缓存
        '_overlay'=>1
    ),
    'app_init'     =>  array(
        'Weixin\Behavior\WxReadHtmlCache', // 关闭html缓存
        '_overlay'=>1
    ),
    'view_template' =>  array(
        'Weixin\Behavior\WxLocationTemplate', // 关闭定位模板文件
        '_overlay'=>1
    ),
    'view_parse'    =>  array(
        'Weixin\Behavior\WxParseTemplate', // 关闭模板引擎
        '_overlay'=>1
    ),
    'view_filter'   =>  array(
        'Weixin\Behavior\Wxfilter', // 关闭模板输出替换
        '_overlay'=>1
    ),
    'view_end'   =>  array(
        'Weixin\Behavior\WxShowTrace', // 关闭模板输出替换
        '_overlay'=>1
    ),
    // 微信入口数据过滤
    'weixin_begin_filter'   =>  array(
        'Weixin\Behavior\WeixinFilter',
    ),
    // 微信系统预处理
    'weixin_begin'   =>  array(
        'Weixin\Behavior\WeixinInit',
    ),
     // 微信系统内容过滤
    'weixin_xml_filter'   =>  array(
        'Weixin\Behavior\WeixinXmlFilter',
    ),
);