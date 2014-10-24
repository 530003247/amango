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

class ImageBundle extends Bundle{
    
    public function run(){
        global $_W;
        global $_P;
        $keywordinfo = get_posttype_list('image');
        if(empty($keywordinfo)){
            $new_articles['Title']       = '亲,您发送的图片很不错哦';
            $new_articles['Description'] = 'Sorry,我们暂无处理该图片的方法';
            $new_articles['PicUrl']      = $_W['picurl'];
            $new_articles['Url']         = "";
            $this->assign('dantw',$new_articles);
            $this->display();
        } else {
            global $_K;$_K = $keywordinfo[0];
            get_keyword_user_auth(true);
            $this->limit_top();
            $this->cache($_K['id'],'',$_K['keyword_cache'],true);
            $this->response();
        }
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