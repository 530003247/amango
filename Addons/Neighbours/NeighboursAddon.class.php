<?php

namespace Addons\Neighbours ;
use Common\Controller\Addon;

/**
 * 隔壁插件
 * @author 拉开让哥单打
 */

    class NeighboursAddon extends Addon{
        public $info = array(
            //插件标识
            'name'=>'Neighbours',
            //插件名称
            'title'=>'隔壁',
            //是否含有微信Bundle
            'weixin'=>'1',
            //是否含有个人中心
            'has_profile'=>'1',
            //插件描述
            'description'=>'芒果高校的特色功能',
            //插件状态
            'status'=>1,
            //插件作者
            'author'=>'拉开让哥单打',
            //插件版本
            'version'=>'0.1',
            //插件LOGO
            'logo'=>'logo.jpg',
        );
        public $custom_config = 'neighboursadmin.html';
        public $admin_list = array(
            'model'=>'Addonsneighbours',		//要查的表
			'fields'=>'from,location,creattime,sharetype,content,to,school,view',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'id desc',		//排序,
			'listKey'=>array( 		//这里定义的是除了id序号外的表格里字段显示的表头名
				'from'=>'发起者',
                'location'=>'地点',
                'creattime'=>'时间',
                'content'=>'内容',
                'sharetype'=>'类型',
                'to'=>'接收者',
                'school'=>'当地',
                'view'=>'浏览'
			),
        );
        public $custom_adminlist = 'neighbourslist.html';
        public function install(){
            $install_sql = './Addons/Neighbours/install.sql';
            if (file_exists($install_sql)) {
                execute_sql_file($install_sql);
            }
            return true;
        }
        public function uninstall(){
            $uninstall_sql = './Addons/Neighbours/uninstall.sql';
            if (file_exists($uninstall_sql)) {
                execute_sql_file($uninstall_sql);
            }
            return true;
        }
        //实现的amango钩子方法
        public function amango($param){

        }

    }