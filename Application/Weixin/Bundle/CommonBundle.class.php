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

class CommonBundle extends Bundle{
    
    public function run(){
        global $_W;
        global $_P;
        //$this->trace($_W);
                $keyword_count = get_posttype_nums($_W['msgtype']);
                if ($keyword_count == 0){
                    //$this->log();
                    $this->error('Sorry!查询到0条有关【'.$_W['msgtype'].'】类型的请求');
                }
                    $preg_keword = get_keyword_match(true);
                    if(empty($preg_keword['id'])){
                        //TODO  切换到默认回复
                        $this->autoreply('auto');
                    }
                    //关键词组和用户权限判断
                    get_keyword_user_auth(true);
                    $this->limit_top();
                    $this->cache($preg_keword['id'],'',$preg_keword['keyword_cache'],true);
                    $this->response();
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