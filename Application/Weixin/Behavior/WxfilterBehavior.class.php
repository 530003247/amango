<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Behavior;
use Think\Behavior;
defined('THINK_PATH') or exit();

class WxfilterBehavior extends Behavior {
    public function run(&$return){
        $return = true;
    }
}