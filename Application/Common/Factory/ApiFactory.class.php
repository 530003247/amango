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

    class ApiFactory extends Factory{
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
                $param  = parse_config($_POST['param']);
                //回复类型判断
                $data['response_reply'] = !empty($_POST['replytype']) ? $_POST['replytype'] : $this->ajaxReturn(array('status' => 0, 'errmsg'=>'用户请求标识非法'),'JSON'); 
                //回复体标识
                $data['response_name']    = empty($_POST['response_name']) ? $_POST['apimodel'].'接口,ID'.$_POST['apiid'] : $_POST['response_name'];
                //结构组合体 
                $neiron = array('type' => strtolower($_POST['apimodel']), 'num' => 1, 'neiron' => $_POST['apiid'], 'replace' => $param);
                $data['response_compos']  = serialize($neiron);
                //生成XML
                $data['response_xml']     =  '';
                //数据静态化
                $data['response_static']  =  0;
                $data['status']           =  1;
                return empty($_POST['apiid']) ? false : $data;
		}
        //编辑模式
        public function edit($responseinfo){
            //Api配置参数
            $res = unserialize($responseinfo['response_compos']);
            $config = '';
            foreach ($res['replace'] as $key => $value) {
                if(!empty($value)){
                    $config .= $key.':'.$value."\n";
                }
            }
            $res['config'] = $config;
            //Api插件模块  第三方接口
            if($res['type']=='cloud'){
                    $cloudlist = M('webuntil')->where(array('status'=>1))->field('id,webuntil_type,webuntil_name,webuntil_backtype,webuntil_title')->select();
                    $locallist   = array();
                    foreach ($cloudlist as $key => $value) {
                        $locallist[$key] = array(
                                  'id'   => $value['id'],
                                  'title'=> $value['webuntil_name'],
                                  'name' => " 请求".$value['webuntil_type'],
                                  'description'=> "回复".$value['webuntil_backtype']
                        );
                    }
            } else {
                $locallist = M('Addons')->where(array('status'=>1,'weixin'=>1))->field('id,title,name,description,config')->select();
                foreach ($locallist as $key => $value) {
                    $locallist[$key]['description'] = msubstr($value['description'], 0, 25, $charset="utf-8", $suffix=true);
                }
            }
            $res['localapi'] = $locallist;
            return $res;
        }
        public function setHead($type, $contentStr){
                return '';
        }
        public function setInfo($type,$detail,$originalxml,$denytag,$otherparam){
            $detail = unserialize($detail);
            switch (strtolower($detail['type'])) {
                case 'cloud':
                    $apiinfo = M('Webuntil')->where(array('id'=>$detail['neiron']))->field('id,webuntil_param,webuntil_type,webuntil_backtype,webuntil_sigtype,webuntil_cache,webuntil_tag,webuntil_url,webuntil_token')->find();
                    $oldparam  = parse_config($apiinfo['webuntil_param']);
                    $newparam  = $detail['replace'];
                    //为了更好的合并参数提交
                    $apiinfo['webuntil_param'] = array_merge($oldparam,$newparam);
                    //判断是否存在缓存
                    $oldinfo    = excute_cache($apiinfo['id'],'',$apiinfo['webuntil_cache'],$is_echo=false);
                    $newcontent = empty($oldinfo) ? self::setCloud($apiinfo) : $oldinfo;
                    return $newcontent;
                    break;
                case 'local':
                    $apiinfo = M('Addons')->where(array('id'=>$detail['neiron'],'status'=>1,'weixin'=>1))->field('name,title,config')->find();
                    if(empty($apiinfo)){
                        wx_error('Sorry!该插件暂未启用或者未安装');
                    } else {
                        defined ( 'AMANGO_ADDON_NAME' )   or define ( 'AMANGO_ADDON_NAME', ucfirst($apiinfo['name']));
                    }
                    $publicpath = str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', ONETHINK_ADDON_PATH.AMANGO_ADDON_NAME.'/Public/');
                    defined ( 'ADDON_PUBLIC' ) or define ( 'ADDON_PUBLIC', $publicpath );
                    defined ( 'ADDON_ROOT' )   or define ( 'ADDON_ROOT', ONETHINK_ADDON_PATH.AMANGO_ADDON_NAME.'/');
                    Amango_Addons($apiinfo['name'],'','',$detail['replace'],true);
                    break;
                case 'behavior':
                    wx_error('Sorry!暂不支持行为动作,下个版本即将推出');
                    break;         
                default:
                    wx_error('未定义的第三方接口');
                    break;
            }
        }
        public function setCloud($apiinfo){
            global $_W;
            global $_K;
                $header [] = "Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0";
                $header [] = "Content-Type: text/xml; charset=utf-8"; // 定义content-type为xml
                $ch = curl_init(); // 初始化curl
                //判断提交方式
                //POST远程
                if($apiinfo['webuntil_type']=='post'){
                    if($apiinfo['webuntil_backtype']=='string'){
                        $post_data = http_build_query($apiinfo['webuntil_param']);
                    } else {
                        $post_data = $GLOBALS ['HTTP_RAW_POST_DATA'];
                        //判断是否过滤关键词  仅对文本请求  匹配规则为开头匹配
                        if($_W['msgtype']=='text'&&$apiinfo['webuntil_sigtype']=='yes'){
                            $content   = trim ( str_replace($_K['keyword_content'],'',$_W['content'] ) );
                            $post_data = str_replace ( '<Content><![CDATA['.$_W['content'].']]></Content>','<Content><![CDATA['.$content.']]></Content>', $post_data );
                        }
                    }
                    $url    = $apiinfo['webuntil_url'];
                    curl_setopt( $ch, CURLOPT_POST, 1 ); // 设置为POST方式
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data ); // POST数据
                } else {
                    //GET 请求
                    $getinfo = $apiinfo['webuntil_param'];
                    $getinfo['keyword'] = ($_W['msgtype']=='text'&&$apiinfo['webuntil_sigtype']=='yes') ? trim(str_replace($_K['keyword_content'],'',$_W['content'])) : $_W['content'];
                    //参数
                    $get_data  = http_build_query($getinfo);
                    $blindurl  = (false===strpos($apiinfo['webuntil_url'],'?')) ? '?'.$get_data : $get_data;
                    $url    = $apiinfo['webuntil_url'].$blindurl;
                    curl_setopt( $ch, CURLOPT_POST, 0 ); // 设置为POST方式
                }
                curl_setopt( $ch, CURLOPT_URL, $apiinfo ['webuntil_url'] ); // 设置链接
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 设置是否返回信息
                curl_setopt( $ch, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
                $response = curl_exec ( $ch ); // 接收返回信息
                
                // if($apiinfo['webuntil_tag']==0){
                //      echo $response;die;
                // }
                if (curl_errno ( $ch )) { // 出错则显示错误信息
                    wx_error(curl_error($ch));
                }
                    curl_close ( $ch );
                    $response = str_replace("<Content><![CDATA[]]></Content>", '', $response);
                    //回复处理  判断是否是XML
                    $string  =  simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
                    if(!empty($string)) {
                            // //XML转成完整数组
                            $data     =  json_decode(json_encode($string,JSON_FORCE_OBJECT),TRUE);
                            foreach ($data as $key => $value) {
                                $packet[strtolower($key)] = $value;
                            }
                            $type = $packet['msgtype'];
                            unset($packet['fromusername'],$packet['tousername'],$packet['createtime'],$packet['msgtype']);
                            //支持多图文的TAG
                            switch (strtolower($type)) {
                                case 'news':
                                    $xmlarray = $packet['articles']['item'];
                                    $nums     = $packet['articlecount'];

                                    if($nums==1){
                                        if(!is_string($xmlarray['PicUrl'])){
                                            $xmlarray['PicUrl'] = '';
                                        }
                                        $rearray[0] = $xmlarray;
                                        $info = ($apiinfo['webuntil_tag']==1) ? Factory('Dantw')->load($rearray)->select() : Factory('Dantw')->load($rearray)->deltag('*')->select();
                                       $infotype = 'Dantw';
                                    } else {
                                       $info = ($apiinfo['webuntil_tag']==1) ? Factory('Duotw')->load($xmlarray)->select() : Factory('Duotw')->load($xmlarray)->deltag('*')->select(); 
                                       $infotype = 'Duotw';            
                                    }
                                    break;

                                case 'text':
                                    $xmlarray['Content'] = $packet['content'];
                                    $info = ($apiinfo['webuntil_tag']==1) ? Factory('Text')->load($xmlarray)->select() : Factory('Text')->load($xmlarray)->deltag('*')->select();
                                    $infotype = 'Text';
                                    break;                      
                                default:
                                    $info = $xml;
                                    $infotype = '';
                                    break;
                            }
                                $xmlinfo = array('type'=>$infotype,'info'=>$info);
                    } else {
                        $newresponse = self::is_json($response);
                        if(is_array($newresponse)){
                            $type = ucfirst($newresponse['msgtype']);unset($newresponse['msgtype']);
                            //$info = Factory($type)->load($newresponse)->select();
                            $info = ($apiinfo['webuntil_tag']==1) ? Factory($type)->load($newresponse)->select() : Factory($type)->load($newresponse)->deltag('*')->select();
                            $xmlinfo = array('type'=>$type,'info'=>$info);
                        } else {
                            //$info = Factory('Text')->load($response)->select();
                            $info = ($apiinfo['webuntil_tag']==1) ? Factory('Text')->load($response)->select() : Factory('Text')->load($response)->deltag('*')->select();

                            $xmlinfo = array('type'=>'text','info'=>$info);
                        }
                    }
                //设置缓存
                if($apiinfo['webuntil_cache']>0){
                     excute_cache($apiinfo['id'],$xmlinfo,$apiinfo['webuntil_cache'],$is_echo=false);
                }
                        return $xmlinfo;
        }

        public function is_json($string){
            $array = json_decode($string,true);
            $a     = (json_last_error() == JSON_ERROR_NONE) ? $array : false;
            return $a;
        }

        public function _empty(){
                return '该方法不存在';
        }
	}
