<?php

namespace Addons\SnatchTieba;
use Common\Controller\Addon;

/**
 * 贴吧获取数据插件
 * @author 拉开让哥单打
 */

    class SnatchTiebaAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'SnatchTieba',
            //插件名称
            'title'=>'贴吧获取数据',
            amangotag=>array (
  'Contenthead' => 'message',
),
            //是否含有微信Bundle
            'weixin'=>1,
            //插件描述
            'description'=>'通过配置参数,自动抓取参数',
            //插件状态
            'status'=>1,
            //插件作者
            'author'=>'拉开让哥单打',
            //插件版本
            'version'=>'0.1',
            //插件LOGO
            'logo'=>'logo.jpg',
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的amango钩子方法
        public function amango($param){

        }

    }