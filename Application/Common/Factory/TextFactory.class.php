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
    class TextFactory extends Factory{
        //微信回复类型  
        public $_replytype  = 'text';
        //该类型下所包含的字段
    	public $_head       = null;
    	//该类型下所包含的字段
    	public $_fields     = array('Content');
    	//每个字段要植入的TAG
        public $_tags       = array(
        	                    'Content' => array('_before'=>'Contenthead', 'after_'=>'Contentend'),
        );
        //执行层数
        protected $_level      = false;

		public function run(){
                //过滤并转义
                $replyinfo              = keyword_replace_text($_POST['replyinfo']);

                //回复类型判断
                $data['response_reply'] = !empty($_POST['replytype']) ? $_POST['replytype'] : $this->ajaxReturn(array('status' => 0, 'errmsg'=>'用户请求标识非法'),'JSON'); 
                //回复体标识
                //$data['response_name']  = msubstr($replyinfo,0, 10, $charset="utf-8", $suffix=true);
                $data['response_name']    = empty($_POST['response_name']) ? msubstr($replyinfo,0, 10, $charset="utf-8", $suffix=true) : $_POST['response_name'];
                //结构组合体
                $neiron = array('type' => 'text', 'num' => 1, 'neiron' => $replyinfo, 'replace' => '');
                $data['response_compos']  = serialize($neiron);
                //生成XML
                $data['response_xml']     =  Factory('Text')->load($replyinfo)->select();
                //数据静态化
                $data['response_static']  =  1;
                $data['status']           =  1;
                return empty($data['response_xml']) ? false : $data;
		}
        //编辑模式
        public function edit($responseinfo){
            $res = unserialize($responseinfo['response_compos']);
            $res['neiron'] = str_replace("\n", '<br>', $res['neiron']);
            //更换表情
            $baseimg = '<img src="./Public/static/kindeditor/plugins/emoticons/images/%s.gif" data-ke-src="./Public/static/kindeditor/plugins/emoticons/images/%s.gif" alt="%s" data="amango">';
            $qqimage =  Array("/微笑","/撇嘴","/色","/发呆","/得意","/流泪","/害羞","/闭嘴","/睡","/大哭","/尴尬","/发怒","/调皮","/呲牙","/惊讶","/难过","/酷","/冷汗","/抓狂"
            ,"/吐","/偷笑","/愉快","/白眼","/傲慢","/饥饿","/困","/惊恐","/流汗","/憨笑","/悠闲","/奋斗","/咒骂","/疑问","/嘘","/晕","/疯了","/衰","/骷髅","/敲打","/再见","/擦汗","/抠鼻"
            ,"鼓掌","/糗大了","/坏笑","/左哼哼","/右哼哼","/哈欠","/鄙视","/委屈","/快哭了","/阴险","/亲亲","/吓","/可怜","/菜刀","/西瓜","/啤酒","/篮球","/乒乓","/咖啡","/饭","/猪头","/玫瑰","/凋谢"
            ,"/嘴唇","/爱心","/心碎","/蛋糕","/闪电","/炸弹","/刀","/足球","/瓢虫","/便便","/月亮","/太阳","/礼物","/拥抱","/强","/弱","/握手","/胜利","/抱拳","/勾引","/拳头","/差劲","/爱你","/NO","/OK"
            ,"爱情","/飞吻","/跳跳","/发抖","/怄火","/转圈","/磕头","/回头","/跳绳","/投降","/激动","/乱舞","/献吻","/左太极","/右太极");
            $newimg = array();
            foreach ($qqimage as $key => $value) {
                $newimg[$value] = sprintf($baseimg,$key,$key,str_replace('/', ':', $value));
            }
            $res['neiron'] = strtr($res['neiron'],$newimg);
            return $res;
        }
        public function setHead($type, $contentStr){
                return '';
        }
        public function setInfo($type,$detail,$originalxml,$denytag,$otherparam){
                $arraydetail = unserialize($detail);
                return array('type'=>'Text','info'=>$arraydetail['neiron']);
        }
        public function _empty(){
                return '该方法不存在';
        }
	}
