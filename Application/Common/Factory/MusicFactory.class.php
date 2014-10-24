<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Common\Factory;
use Common\Controller\Factory;

/**
 * Text类型  处理
 * @return array
 * @author ChenDenlu <530003247@vip.qq.com>
 */
    class MusicFactory extends Factory{
        //微信回复类型  
        public $_replytype  = 'music';
        //该类型下所包含的字段
    	public $_head       = null;

        public $_top        = array(
                                 'Music' => array('head' => '', 'end' => '')
        );
        //该类型下所包含的字段
        public $_fields     = array('Title','Description','MusicUrl','HQMusicUrl','ThumbMediaId');

    	//每个字段要植入的TAG
        public $_tags       = array(
                                'Title'       => array('_before'=>'Musictitlehead', 'after_'=>'Musictitleend'),
                                'Description' => array('_before'=>'Musicdescriptionhead', 'after_'=>'Musicdescriptionend'),
        );
        //执行层数
        protected $_level      = false;

		public function run(){
               dump($_POST);die;
               return true;
		}

        public function setHead($type, $contentStr){
                return '';
        }
        public function setInfo($type,$detail,$originalxml,$denytag,$otherparam){
                return '';
        }
        public function _empty(){
                return '该方法不存在';
        }
	}
