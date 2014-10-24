<?php

namespace Addons\Xitedu;
use Common\Controller\Addon;

/**
 * 集大微信教务插件
 * @author 陈登禄
 */

    class XiteduAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'Xitedu',
            //插件名称
            'title'=>'微信教务',
            
            //插件是否含有微信Bundle 1/0
            'weixin'=>'1',
            //插件前台含有个人中心 1/0
            'has_profile'=>'1',
            //插件描述
            'description'=>'集大微信教务',
            //插件状态
            'status'=>1,
            //插件作者
            'author'=>'陈登禄',
            //插件版本
            'version'=>'0.1',
            //插件LOGO
            'logo'=>'logo.jpg',
        );
        
        //public $custom_adminlist = 'Xitedulist.html';
        
        public $admin_list = array(
            'model'      =>'Addonseduinfo',	//要查的表
            'fields'     =>'*',			    //要查的字段
            'map'        =>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
            'order'      =>'id desc',		//排序,
            'search_key' =>'fromusername',  //搜索字段
            'list_grid'  => array(
                'fromusername:用户',
                'name:姓名',
                'xuehao:学号',
                'sex:性别',
                'cardid:身份证',
                'zhuanye:专业',
                'shengfen:省份',
                'address:地址',
                'fromusername:操作:[EDIT]|编辑,[DELETE]|删除'
            ),
        );

        public function install(){
            $install_sql = './Addons/Xitedu/install.sql';
            if (file_exists($install_sql)) {
                execute_sql_file($install_sql);
            }
            return true;
        }

        public function uninstall(){
            $uninstall_sql = './Addons/Xitedu/uninstall.sql';
            if (file_exists($uninstall_sql)) {
                execute_sql_file($uninstall_sql);
            }
            return true;
        }

        //实现的amango钩子方法
        public function amango($param){

        }

    }