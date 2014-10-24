<?php

namespace Addons\Sharephotos;
use Common\Controller\Addon;

/**
 * 图吧插件
 * @author 陈登禄
 */

    class SharephotosAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'Sharephotos',
            //插件名称
            'title'=>'图吧',
            
            //插件是否含有微信Bundle 1/0
            'weixin'=>'1',
            //插件前台含有个人中心 1/0
            'has_profile'=>'0',
            //插件描述
            'description'=>'随手分享图片插件',
            //插件状态
            'status'=>1,
            //插件作者
            'author'=>'陈登禄',
            //插件版本
            'version'=>'0.1',
            //插件LOGO
            'logo'=>'logo.jpg',
        );

        public $admin_list = array(
            'model'=>'Addonssharepics',		//要查的表
			'fields'=>'*',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'status asc',  //排序,
			'listKey'=>array( 		//这里定义的是除了id序号外的表格里字段显示的表头名
				'from'    =>'微信用户',
                'nickname'=>'用户昵称',
                'content'=>'分享理由',
                'picurl'=>'图片',
                'sharetime'=>'时间',
                'status'=>'状态',
			),
        );

        public $custom_adminlist = 'sharephotoslist.html';

        public function install(){
            /**
             * 当该插件含有instal.sql时候，请放置于插件根目录，并将下面4行注释去掉
             * 若无安装sql时  默认返回true
             */
            $install_sql = './Addons/Sharephotos/install.sql';
            if (file_exists($install_sql)) {
               execute_sql_file($install_sql);
            }
            return true;
        }

        public function uninstall(){
            /**
             * 当该插件含有uninstal.sql时候，请放置于插件根目录，并将下面4行注释去掉
             * 若无安装sql时  默认返回true
             */
            $uninstall_sql = './Addons/Sharephotos/uninstall.sql';
            if (file_exists($uninstall_sql)) {
               execute_sql_file($uninstall_sql);
            }
            return true;
        }

        //实现的amango钩子方法
        public function amango($param){

        }

    }