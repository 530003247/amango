<?php

namespace Addons\Exam;
use Common\Controller\Addon;

/**
 * 微考试插件
 * @author 拉开让哥单打
 */

    class ExamAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'Exam',
            //插件名称
            'title'=>'微考试',
            //插件是否含有微信Bundle 1/0
            'weixin'=>'1',
            'weixinkeyword'=>array(
                    'post'     =>array(
                                0=>array(
                                    'keyword_post' =>'text',
                                    'keyword_rules'=>'/^考试/',
                                ),
                    ),
                    'response' =>array(
                                0=>array(
                                    'response_name' =>'微考试插件-考试',
                                    'param'         =>'',
                                ),
                    ),
                    'group'    =>array(
                         '0'=>'0' 
                    ),
            ),
            //插件前台含有个人中心 1/0
            'has_profile'=>'1',
            //插件描述
            'description'=>'微考试',
            //插件状态
            'status'=>'1',
            //插件作者
            'author'=>'拉开让哥单打',
            //插件版本
            'version'=>'0.1',
            //插件LOGO
            'logo'=>'logo.jpg',
        );

        public $admin_list = array(
			'model'     =>'Addonsexam',		//要查的表
			'fields'    =>'*',			//要查的字段
			'map'       =>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'     =>'id desc',		//排序,
			'listKey'   =>array(       //这里定义的是除了id序号外的表格里字段显示的表头名
						'title'     =>'标题',
						'has_share' =>'微信分享',
						'has_name'  =>'实名制',
						'keyword'   =>'关键词',
						'author'    =>'作者',
						'desc'      =>'描述',
						'score'     =>'总分',
						'views'     =>'参考人数'
            ),
        );
        public $custom_adminlist = 'testlist.html';
        public $custom_config    = 'admin.html';
        public function install(){
            $install_sql = './Addons/Exam/install.sql';
            if (file_exists($install_sql)) {
              execute_sql_file($install_sql);
            }
            return true;
        }
        public function uninstall(){
            $uninstall = './Addons/Exam/uninstall.sql';
            if (file_exists($uninstall)) {
               execute_sql_file($uninstall);
            }
            return true;
        }
        //实现的amango钩子方法
        public function amango($param){

        }
}