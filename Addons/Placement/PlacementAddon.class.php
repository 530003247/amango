<?php

namespace Addons\Placement;
use Common\Controller\Addon;

/**
 * 信息植入插件
 * @author 拉开让哥单打
 */

    class PlacementAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'Placement',
            //插件名称
            'title'=>'信息植入',
            
            //插件是否含有微信Bundle 1/0
            'weixin'=>'1',
            //插件前台含有个人中心 1/0
            'has_profile'=>'0',
            //插件描述
            'description'=>'信息植入插件,使用方法:在tag界面选择该插件,在参数中输入相应的id:调用ID',
            //插件状态
            'status'=>1,
            //插件作者
            'author'=>'拉开让哥单打',
            //插件版本
            'version'=>'0.1',
            //插件LOGO
            'logo'=>'logo.jpg',
        );

        public $admin_list = array(
            'model'=>'Addonsplacement',		//要查的表
			'fields'=>'*',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'id desc',		//排序,
			'listKey'=>array( 		//这里定义的是除了id序号外的表格里字段显示的表头名
                'placement_start'     =>'开始时间',
                'placement_end'       =>'结束时间',
                'placement_addtype'   =>'类型',
                'placement_add'       =>'植入信息',
                'placement_keygroup'  =>'关键词组',
                'placement_usergroup' =>'用户组'
			),
        );
        public $custom_adminlist = 'placementlist.html';
        public $custom_config    = 'placementadmin.html';
        public function install(){
            $install_sql = './Addons/Placement/install.sql';
            if (file_exists($install_sql)) {
                execute_sql_file($install_sql);
            }
            return true;
        }

        public function uninstall(){
            $uninstall_sql = './Addons/Placement/uninstall.sql';
            if (file_exists($uninstall_sql)) {
                execute_sql_file($uninstall_sql);
            }
            return true;
        }

        //实现的amango钩子方法
        public function amango($param){

        }

    }