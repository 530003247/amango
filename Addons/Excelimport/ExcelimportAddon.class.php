<?php

namespace Addons\Excelimport;
use Common\Controller\Addon;

/**
 * Excel导入插件
 * @author 拉开让哥单打
 */

    class ExcelimportAddon extends Addon{

        public $info = array(
            //插件标识
            'name'=>'Excelimport',
            //插件名称
            'title'=>'Excel导入',
            //插件描述
            'description'=>'该插件主要用于简单的Excel表格导入进网站数据库，自动生成数据表，自定义相关字段主键等等......',
            //是否含有微信Bundle
            'weixin'=>1,
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
            'model'=>'Addonsexcel',		//要查的表
			'fields'=>'tablename,fileds,rows',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'id desc',		//排序,
			'listKey'=>array( 		//这里定义的是除了id序号外的表格里字段显示的表头名
                'tablename'=>'数据表名',
				'fileds'=>'主要字段名称',
                'rows'=>'记录数量'
			),
        );

        public $custom_adminlist = 'excelimportlist.html';
        public $custom_config    = 'excelimportadmin.html';

        public function install(){
            $install_sql = './Addons/Excelimport/install.sql';
            if (file_exists($install_sql)) {
                execute_sql_file($install_sql);
            }
            return true;
        }

        public function uninstall(){
            $tablename = array();
            $tablename = M('addonsexcel')->field('tablename')->select();
            $model = M();
foreach ($tablename as $key => $value) {
$sql = <<<sql
                DROP TABLE {$value['tablename']};
sql;
        $model->execute($sql);
}
            $uninstall_sql = './Addons/Excelimport/uninstall.sql';
            if (file_exists($uninstall_sql)) {
                execute_sql_file($uninstall_sql);
            }
            return true;
        }

        //实现的amango钩子方法
        public function amango($param){

        }

    }