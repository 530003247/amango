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

    class DantwFactory extends Factory{

        //微信回复类型  
        public $_replytype  = 'news';
        //该类型下所包含的字段
    	public $_head       = 'ArticleCount';
        //顶部标签字段
        public $_top        = array(
                                 'Articles' => array('head' => 'Articlesdantwhead', 'end' => 'Articlesdantwend')
        );
        //XML组标签
        public $_group      = array(
                                 'item' => array('head' => 'Itemhead', 'end' => 'Itemend')
        );
    	//该类型下所包含的字段
    	public $_fields     = array('Title','Description','PicUrl','Url');
    	//每个字段要植入的TAG
        public $_tags       = array(
        	                    'Title'       => array('_before'=>'Itemtitlehead', 'after_'=>'Itemtitleend'),
                                'Description' => array('_before'=>'Itemdescriptionhead', 'after_'=>'Itemdescriptionend'),
        );
        //执行层数
        protected $_level      = true;

		public function run(){
            $draw     = $_POST['yireply'];
            $replace  = $_POST['replace'];
            //分解 引用内容
            $yinyong  = explode(',', $draw);
            //分解 替换内容
            $replaceinfo    = explode(',', $replace);
            $replaceinfo[1] = empty($replaceinfo[1]) ? '' : get_cover_pic($replaceinfo[1]);
            if($yinyong[0]=='null'||$yinyong[1]=='null'){
                
                $new_articles[0]['Title']       = $replaceinfo[0];
                $new_articles[0]['Description'] = $replaceinfo[2];
                $new_articles[0]['PicUrl']      = $replaceinfo[1];
                $new_articles[0]['Url']         = $replaceinfo[3];
                $data['response_static']        =  1;
            } else {
                //判断是否为最新或者随机
                if(in_array(strtolower($yinyong[1]), array('rand','news'))){
                    $order   = $yinyong[1];
                    $info[0] = strtoupper($order).'0';
                    $info[2] = strtoupper($order).'2';
                    $info[1] = strtoupper($order).'1';
                    $info[3] = strtoupper($order).'3';
                    $data['response_static']  =  0;
                } else {
                    //原始帖子内容
                    $info = get_tiezi_info($yinyong[0],$yinyong[1]);
                    $data['response_static']  =  1;
                }
                //资源替换
                $newinfo = array_to_array($replaceinfo,$info);
                $new_articles[0]['Title']       = $newinfo[0];
                $new_articles[0]['Description'] = $newinfo[2];
                $new_articles[0]['PicUrl']      = $newinfo[1];
                $new_articles[0]['Url']         = $newinfo[3];
            }
                $data['response_xml']     = addslashes(Factory('Dantw')->load($new_articles)->select());
                $data['response_compos']  = serialize(array('type' => 'articles', 'num' => 1, 'neiron' => $draw, 'replace' => $replace));
                $data['response_reply']   = 'Dantw';
                $data['status']  =  1;

                //标识
                //$data['response_name']    = '分类ID:'.$yinyong[0].'|类型:'.$yinyong[1];
                $data['response_name']    = empty($_POST['response_name']) ? '分类ID:'.$yinyong[0].'|类型:'.$yinyong[1] : $_POST['response_name'];
                return empty($data['response_xml']) ? false : $data;
		}
        //编辑模式
        public function edit($responseinfo){
            $res        = unserialize($responseinfo['response_compos']);
            $yingyong   = explode(',', $res['neiron']);
            $staticinfo = explode(',', $res['replace']);
            $lists      = api('Category/get_category_list',array('cateid'=>$yingyong[0],'field'=>'l.id,l.title','order'=>'l.level DESC,l.id DESC'));
            $staticinfo['tiezi_list'] = $lists;
            $staticinfo['Tiezi']      = $yingyong;
            return $staticinfo;
        }
        //设置头部
        public function setHead($type, $contentStr){
            $connum = substr_count($contentStr,'<item>');
            return '<ArticleCount>'.$connum.'</ArticleCount>';
        }
        //设置动态内容
        public function setInfo($type,$detail,$originalxml,$denytag,$otherparam){
            $arraydetail = unserialize($detail);
            $readparam   = explode(',', $arraydetail['neiron']);
                //要读取的字段
                $readfields = D('Flycloud')->where(array('data_type' => 'category', 'data_table'=>$readparam[0]))->find();
                //读取详情  判断是否为最新或随机
                $artype     = strtolower($readparam[1]);
                if(in_array($artype, array('rand','news'))){
                    $lists = api('Category/get_category_'.$artype,array('cateid'=>$readparam[0],'field'=>$readfields['data_fields'],'limit'=>1));
                    //完整XML格式信息
                    $serslists = array_values($lists[0]);
                    $url       = is_numeric($serslists[1]) ? D('Picture')->where(array('id' => $serslists[1]))->getField('path') : $serslists[1];
                    $prefix    = strtoupper($readparam[1]);
                    $replacedate = array(
                        $prefix.'0'  => $serslists[0],
                        $prefix.'1'  => get_cover_pic($url),
                        $prefix.'2'  => $serslists[2],
                        $prefix.'3'  => Amango_U('Article/detail?id='.$serslists[3]),
                    );
                    $newcontent = strtr($originalxml,$replacedate);
                } else {
                    $newcontent = $originalxml;
                }

                     return array('type'=>'Dantw','info'=>$newcontent);
        }

        public function _empty(){
                return '该方法不存在';
        }
	}
