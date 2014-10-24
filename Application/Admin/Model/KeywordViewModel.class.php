<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model\ViewModel;

/**
 * 关键词模型
 */
class KeywordViewModel extends ViewModel {
   public $viewFields = array(
     'Keyword'=>array('*'),
     'Response'=>array('response_reply','response_name','_on'=>'Keyword.keyword_reaponse=Response.id'),
     'Posts'=>array('status'=>'posts_status','posts_name','_on'=>'Posts.posts_title=Keyword.keyword_post'),
     'Keywordcate'=>array('keywordcate_name','_on'=>'Keywordcate.id=Keyword.keyword_group'),
   );
}
?>
