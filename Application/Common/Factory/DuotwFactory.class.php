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
    class DuotwFactory extends Factory{

        //微信回复类型  
        public $_replytype  = 'news';
        //该类型下所包含的字段
    	public $_head       = 'ArticleCount';
        //顶部标签字段
        public $_top        = array(
                                 'Articles' => array('head' => 'Articlesduotwhead', 'end' => 'Articlesduotwend')
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
            //快捷回复生成
            if(strtolower($_POST['replytype'])=='basic'){
                $new_articles = self::basicrun();
                $nums         = count($new_articles);
                
                $data['response_static'] = 0;
                $data['response_compos'] = serialize(array('type' => 'fastarticles', 'num' => $nums, 'neiron' => $_POST['cateid'].','.$_POST['id'], 'replace' => ''));
            } else {
            //多条图文生成
            $msgitem = $_POST['msgitem'];
            //图文替换流程
            $new_articles = array();
            $nums         = count($msgitem);
                foreach ($msgitem as $key => $value) {
                    $order  = $value['tzid'];
                    $picurl = get_cover_pic($value['conurl']);
                    //判断是否是随机  最新
                        if(in_array(strtolower($order), array('news','rand'))){
                            $type = strtoupper($order).$key;
                                $lmid = explode(',',$value['lmid']);
                                $newinfo[0] = empty($value['title']) ? $type.'0' : $value['title'];
                                $newinfo[1] = empty($value['conurl']) ? $type.'1' : $picurl;
                                $newinfo[3] = empty($value['url']) ? $type.'3' : $value['url'];
                                $replace[$key]  = $lmid[1].','.$value['tzid'];
                        } else {
                                $item[0] = $value['title'];
                                $item[1] = $picurl;
                                $item[3] = $value['url'];
                                $info    = get_tiezi_info($value['lmid'],$value['tzid']);
                                $newinfo = array_to_array($item,$info);
                        }
                    //判断是否为纯静态XML
                    if(!is_numeric($value['tzid'])||!is_numeric($value['lmid'])){
                        $is_statick = false;
                    }
                                $new_articles[] = array(
                                       'Title' => $newinfo[0],
                                 'Description' => $newinfo[0],
                                      'PicUrl' => $newinfo[1],
                                         'Url' => $newinfo[3],
                                     );
                                unset($lmid);unset($newinfo);
                }
                
            //判断是否为纯静态
                $data['response_static']  = (false===$is_statick) ? 0 : 1;

                $data['response_compos']  = serialize(array('type' => 'articles', 'num' => $nums, 'neiron' => $msgitem, 'replace' => $replace));
            }
                $data['response_reply']   = 'Duotw';
                //$data['response_name']    = '多图文:'.$_POST['replytype'].'|时间:'.date("Y-m-d");
                $data['response_name']    = empty($_POST['response_name']) ? '多图文:'.$_POST['replytype'].'|时间:'.date("Y-m-d") : $_POST['response_name'];
                $data['response_xml']     = addslashes(Factory('Duotw')->load($new_articles)->select());
                return empty($data['response_xml']) ? false : $data;
		}

        //快捷回复处理
        public function basicrun(){
            $type  = strtoupper($_POST['id']);
            $limit = ($_POST['num']>=2) ? $_POST['num'] : 2;
            $limit = ($limit<=8) ? $limit : 8;
            $replacedate = array();
                for($i=0;$i<$limit;$i++){
                        $replacedate[$i] = array(
                               'Title' => $type.$i.'0',
                         'Description' => $type.$i.'0',
                              'PicUrl' => $type.$i.'1',
                                 'Url' => $type.$i.'3',
                        );
                }
                    return $replacedate;
        }
        //编辑模式
        public function edit($responseinfo){
                $res = unserialize($responseinfo['response_compos']);
                $res['type'] = ($res['type']=='fastarticles')?'basic':'gaoji';
                //dump($res);die;
                // //快捷回复
                if($res['type']=='basic'){
                    $yingyong = explode(',', $res['neiron']);
                    $res['basic']  = array(
                             'num' =>$res['num'],
                             'cate'=>$yingyong[0],
                             'type'=>$yingyong[1]
                    );
                } else {
                    $res['gaoji']  = $res['neiron'];
                }
                return $res;
        }
        public function setHead($type, $contentStr){
            $connum = substr_count($contentStr,'<item>');
            return '<ArticleCount>'.$connum.'</ArticleCount>';
        }

        public function setInfo($type,$detail,$originalxml,$denytag,$otherparam){
            $arraydetail = unserialize($detail);
            $readparam   = explode(',', $arraydetail['neiron']);
            switch ($arraydetail['type']) {
                case 'fastarticles':
                            $readfields = D('Flycloud')->where(array('data_type' => 'category', 'data_table'=>$readparam[0]))->find();
                            //读取详情  判断是否为最新或随机
                            if(strtolower($readparam[1])=='rand'){
                                $lists = api('Category/get_category_rand',array('cateid'=>$readparam[0],'field'=>$readfields['data_fields'],'limit'=>$arraydetail['num']));
                            }
                            if(strtolower($readparam[1])=='news'){
                                $lists = api('Category/get_category_news',array('cateid'=>$readparam[0],'field'=>$readfields['data_fields'],'limit'=>$arraydetail['num']));
                            }
                    $newlist = array();
                    foreach ($lists as $key => $value) {
                        $newlist[] = array_values($value);
                    }
                    $hassource = count($newlist);
                    if($hassource===0){
                        wx_error('Sorry!暂无相关内容');
                    }
                    $writetype   = strtoupper($readparam[1]);
                    $replacedate = array();
                    for ($i=0; $i <= $arraydetail['num']-1; $i++) { 
                        $replacedate[$writetype.$i.'0'] = $newlist[$i][0];
                        $replacedate[$writetype.$i.'1'] = get_cover_pic($newlist[$i][1]);
                        $replacedate[$writetype.$i.'2'] = $newlist[$i][2];
                        $replacedate[$writetype.$i.'3'] = Amango_U('Article/detail?id='.$newlist[$i][3]);
                    }
                    $newcontent = strtr($originalxml,$replacedate);
                    //wx_error(base64_encode($newcontent));
                    //wx_error(json_encode($replacedate));
                    return array('type'=>'Duotw','info'=>$newcontent);
                    break;
                case 'articles':
                    $newcontent = self::creatArticles($type,$detail,$originalxml,$denytag,$otherparam);
                    return array('type'=>'Duotw','info'=>$newcontent);
                    break;
                default:
                    wx_error('多图文回复中未知类型~');
                    break;
            }
        }

        protected function creatArticles($type,$detail,$originalxml,$denytag,$otherparam){
            $arraydetail  = unserialize($detail);
            $arrayreplace = $arraydetail['replace'];
            $newplace = array();
            //$readparam   = explode(',', $arraydetail['replace']);
            if(!empty($arrayreplace)){
                foreach ($arrayreplace as $key => $value) {
                    $readparam  = explode(',',$value);
                    $newplace[$key]['cateid'] = $readparam[0];
                    $newplace[$key]['type']   = $readparam[1];
                    unset($readparam);
                }
                //TODO  暂时采用递归
                foreach ($newplace as $key => $value) {
                    $readfields = D('Flycloud')->where(array('data_type' => 'category', 'data_table'=>$value['cateid']))->find();
                    //读取详情  判断是否为最新或随机
                    if(strtolower($value['type'])=='rand'){
                        $lists = api('Category/get_category_rand',array('cateid'=>$value['cateid'],'field'=>$readfields['data_fields'],'limit'=>1));
                    }
                    if(strtolower($value['type'])=='news'){
                        $lists = api('Category/get_category_news',array('cateid'=>$value['cateid'],'field'=>$readfields['data_fields'],'limit'=>1));
                    }
                    $newlist = array_values($lists[0]);
                    $writetype  = strtoupper($value['type']).$key;
                    $replacedate[$writetype.'0'] = $newlist[0];
                    $replacedate[$writetype.'1'] = $newlist[1];
                    $replacedate[$writetype.'2'] = $newlist[2];
                    $replacedate[$writetype.'3'] = Amango_U('Article/detail?id='.$newlist[3]);
                    unset($newlist);unset($lists);unset($readfields);
                }
                    $newcontent = strtr($originalxml,$replacedate);
                    return $newcontent;

            } else {
                   return $originalxml;
            }
        }

        public function _empty(){
                return '该方法不存在';
        }
	}
