<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Bundle;
use Common\Controller\Bundle;

class SubscribeBundle extends Bundle{
    
    public function run(){
        $this->autoreply('hello');
    }
    //日志
    public function log(){
            return true;
    }
    //空操作
    public function _empty($type){
        wx_error('请联系管理员添加【'.$type.'】请求类型吧~');
    }
}
?>