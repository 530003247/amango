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
 * Video类型  处理
 * @return array
 * @author ChenDenlu <530003247@vip.qq.com>
 */
    class VideoFactory extends Factory{
        //微信回复类型  
        public $_replytype  = 'video';
        //该类型下所包含的字段
    	public $_head       = null;

        public $_top        = array(
                                 'Video' => array('head' => '', 'end' => '')
        );
        //该类型下所包含的字段
        public $_fields     = array('MediaId','Title','Description');

    	//每个字段要植入的TAG
        public $_tags       = array(
                                'Title'       => array('_before'=>'Videotitlehead', 'after_'=>'Videotitleend'),
                                'Description' => array('_before'=>'Videodescriptionhead', 'after_'=>'Videodescriptionend'),
        );
        //执行层数
        protected $_level      = false;

		public function run(){
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
