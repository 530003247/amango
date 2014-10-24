<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Model;
use Think\Model\ViewModel;

/**
 * 标签模型
 */
class TagslistsViewModel extends ViewModel {
   public $viewFields = array(
     'Tagslists' => array('tagslists_title','tagslists_type','tagslists_action','tagslists_param','tagslists_group'),
     'Tagscate'  => array('tagscate_type','tagscate_title','_on'=>'Tagslists.tagslists_group=Tagscate.tagscate_title'),
   );
}
?>
