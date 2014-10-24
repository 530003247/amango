<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Controller;
use Common\Controller\Bundle;
class CmdController extends Bundle{
	public $type;
    public function ping($type = 'WEIXIN'){
        $this->type = empty($type)?'WEIXIN':$type;
        return $this;
    }
    public function netstat($cmd,$param=''){
    	$cmd   = empty($cmd)?return 'empty cmd':$cmd;
        $type  = empty($this->type)? 'WEIXIN':$this->type;
        $functionname = strtoupper($type.'_'.$cmd);
        wx_error($functionname);
        //return $this->$functionname($param);
    }
    public function WEIXIN_CANCEL($param){
        $this->lock('model','');
        $this->lock('keyword','');
        $this->lock('click','');
        return true;
    }
    public function WEIXIN_CANCEL_KEYWORD($param){
        $this->lock('keyword','');
        return true;
    }
    public function WEIXIN_CANCEL_MODEL($param){
        $this->lock('model','');
        return true;
    }
    public function WEIXIN_CANCEL_CLICK($param){
        $this->lock('click','');
        return true;
    }
}
?>