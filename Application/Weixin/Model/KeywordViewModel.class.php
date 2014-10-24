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
 * 关键词模型
 */
class KeywordViewModel extends ViewModel {
   public $viewFields = array(
     'Keyword'    =>array('*'),
     'Keywordcate'=>array('keywordcate_denyuser','keywordcate_name','status'=>'keywordcate_status','_on'=>'Keyword.keyword_group=Keywordcate.id'),
     'Response'   =>array('response_static','response_reply','response_xml','response_compos','status'=>'response_status','id'=>'response_id','_on'=>'Keyword.keyword_reaponse=Response.id'),
   );
}
?>
