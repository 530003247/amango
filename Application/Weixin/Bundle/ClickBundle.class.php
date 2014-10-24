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

class ClickBundle extends Bundle{
    public function run(){
        global $_W;
        global $_P;
        //获取微信  加载个人菜单
        $menuid = $this->locked('click');
        $gtmenu = array();
        //初始化菜单
        $menu_key = M('Clickmenu')->where(array('status'=>1))->Field('sqlmenu')->find();
        $menukey  = json_decode($menu_key['sqlmenu'],true);

        if(is_numeric($menuid)){
            $menu_key = M('Clickmenu')->where(array('id'=>$menuid))->Field('sqlmenu')->find();
            $gtmenu    = json_decode($menu_key['sqlmenu'],true);
        } else {
            if(is_string($menuid)){
                $gtmenu   = $this->ex_clickmenu($menuid);
            }
        }
        $menukey  = array_merge($menukey,$gtmenu);
        $responseid = $menukey[$_W['eventkey']];
        //判断取消/上一页/菜单模式转换/
        if(empty($responseid)){
                $this->autoreply('auto');
        } else {
            if(is_numeric($responseid)){
                $this->response('#',$responseid);
            } else {
                switch (strtoupper($responseid)) {
                    case 'CANCEL':
                        $this->lock('model','');
                        $this->lock('keyword','');
                        $this->lock('click','');
                        break;
                    default:
                        $this->autoreply('auto');
                        break;
                }
                wx_success('编辑操作成功');
            }
        }
    }
    //解析Bundle中自定义菜单
    protected function ex_clickmenu($menuid){
        //响应插件自定义菜单格式   按钮:键值;按钮2:键值2
        $menuid  = str_replace('，', ',', $menuid);
        $newmenu = explode(';', $menuid);
        //获取所有新增
        $suballmenu = array();
        foreach ($newmenu as $key => $value) {
            $submenu = array();
            if(!empty($value)){
                $submenu = explode(':', $value);
                $suballmenu[strtoupper($submenu[0])] = $submenu[1];
            }
        }
        //判断 左 中 右
        $new_menu = array();
        foreach ($suballmenu as $key => $value) {
            if(in_array($key, array('CENTER','LEFT','RIGHT'))){
                $i   = 0;
                $new_menu[$key] = $value;
                for ($i=0; $i <= 4 ; $i++) { 
                    $new_menu[$key.$i] = $value;
                }
            } else {
                    $new_menu[$key]    = $value;
            }
        }
            unset($suballmenu);
            return $new_menu;
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