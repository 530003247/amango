<?php

namespace Addons\Toolbox;
use Common\Controller\Addon;

/**
 * 常用工具插件
 * @author 芒果官方
 */

    class ToolboxAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'Toolbox',
            //插件名称
            'title'=>'常用工具',
            
            //是否含有微信Bundle
            'weixin'=>1,
            //插件描述
            'description'=>'常用工具盒子:查快递，查动车，支持自主定义功能',
            //插件状态
            'status'=>1,
            //插件作者
            'author'=>'拉开让哥单打',
            //插件版本
            'version'=>'1.0',
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