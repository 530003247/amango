<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Controller;
use Common\Controller\Response;
/**
 * 返回模型名称
 * @uses Reply::对应方法
 */
class Reply extends Response{
    /**
     * 返回模型  以及映射的静态方法
     * @return array(模型名,有效静态方法)
     */
    protected static function replyName() { 
        return array('Amango'); 
    }
}
