<?php
namespace Weixin\Model;
use Think\Model\ViewModel;

/**
 * 关键词模型
 */
class WeixinmemberViewModel extends ViewModel {
   public $viewFields = array(
     'Weixinmember'   =>array('*'),
     'Followercate'   =>array('status'=>'followercate_status','followercate_title','_on'=>'Weixinmember.cate_group=Followercate.followercate_title'),
   );
}
?>
