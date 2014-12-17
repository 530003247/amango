<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------

//web端管理
//Factory('Text')->run();
//根据数组或者XML 条件(删除tags)   创建全部
//Factory('Text')->load(数组或xml)->deltag(array('单项标识','字键'=>'位置标签'))->select();
//根据数组或者XML 条件(删除tags)   创建单项
//Factory('Text')->load(数组或xml)->deltag()->find();
namespace Common\Controller;

/**
 * 信息创建 工厂类
 * @author Kevin
 */
abstract class Factory{

    //链式参数存储
    protected $options    = array();
    protected $data       = array();
    protected $type       = null;    //0 为 数组  1 为XML  2 为字符串
    /**
     * 加载数据 数组 或XML
     * @param array $data 格式: 微信键名=>键值
     * @param xml   $data 格式: 微信标准的XML
     * @return Object
     */
    
    public function load($data,$denyxml) {
    	if(is_array($data)){
    		$this->type = 0;
            $this->data = $data;
    	} else {
    		$postObj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
    		if($postObj instanceof SimpleXMLElement) {
    			$packet     = array();
                $packet     = xml_to_array($postObj,array('ToUserName','FromUserName','CreateTime'));
                $this->type = 1;
                $this->data = $packet;
            } else {
            	$this->type = 2;
            	$this->data['Content'] = $data;
            }
        }
        return $this;
    }
    //需要隐藏的标签
    public function deltag($tags) {
    	$this->options['tags'] = $tags;
        return $this;
    }
    //标签解析合并
    protected function parax_tags($where) {
        if($where=='*'){
            unset($this->_tags);
        } else {
            foreach ($where as $key => $value) {
                if(is_numeric($key)&&in_array($value,$this->_fields)){
                        unset($this->_tags[$value]);
                } else {
                    if(in_array($key,$this->_fields)&&in_array($value,$this->_tags[$key])){
                        unset($this->_tags[$key][$value]);
                    }
                }
            }
        }
        return $this->_tags;
    }

    //XML形式返回  全部
	public function select(){
        self::parax_tags($this->options['tags']);
        //$data  = $this->data;
        //$group = $this->_group;
        //$top   = $this->_top;
            if($this->_level&&self::judgeData($this->data)){
                //单项GROUP循环
                $msgnums = count($this->data)-1;
                $xml     = '';
                //子项标识
                foreach ($this->_group as $key => $value) {
                        for ($i=0; $i <= $msgnums; $i++) { 
                            $head = creat_amango_tag($value['head'],$i).'<'.$key.'>';
                            $end  = '</'.$key.'>'.creat_amango_tag($value['end'],$i);
                            $xml .= $head.array_to_xml($this->data[$i],$this->_tags,$i).$end;
                        }
                }
            } else {
                    $xml = self::createItem($this->_tags);
            }

                //头部包裹
                if(!empty($this->_top)){
                    foreach ($this->_top as $key => $value) {
                            $tophead = '<'.$key.'>'.creat_amango_tag($value['head']);
                            $topend  = creat_amango_tag($value['end']).'</'.$key.'>';
                    }
                        $xml = $tophead.$xml.$topend;
                }
            return $xml;
	}

    //XML形式返回  单项
    public function find($tags){
        return $this->createItem($tags);
    }
    public function createItem($tags){
        $tags    =  empty($tags) ? self::parax_tags($this->options['tags']) : $tags;
        $newdata = self::judgeData($this->data) ? $this->data[0] : $this->data;
        $xml     = self::array_to_xml($newdata,$tags);
        if($this->_level&&!empty($this->_group)){
            foreach ($this->_group as $key => $value) {
                $head = self::creat_amango_tag($value['head']).'<'.$key.'>';
                $end  = '</'.$key.'>'.self::creat_amango_tag($value['end']);
            }
                $xml = $head.$xml.$end;
        }
        return $xml;
    }
    public static function array_to_xml($packet,$tags='',$pre='') {
            if(is_array($packet)) {
                $xml = '';
                foreach ($packet as $key => $value) {
                    $titlekey  = ucfirst($key);
                         $xml .=  self::creat_amango_tag($tags[$titlekey]['head'],$pre)."<{$titlekey}><![CDATA[".self::creat_amango_tag($tags[$titlekey]['_before'],$pre).$value.self::creat_amango_tag($tags[$titlekey]['after_'],$pre)."]]></{$titlekey}>".self::creat_amango_tag($tags[$titlekey]['end'],$pre);
                }
                return $xml;
            } else {
                return null;
            }
    }
    public static function creat_amango_tag($tagname,$pre) {
        return empty($tagname) ? '' : '<amango:'.$tagname.'tag'.$pre.'>';
    }
    public static function judgeData($array){
        if(is_array($array)){
            if (count($array)==count($array, 1)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    //XML形式返回Item
    public function setItem($param,$fields){
        $fields     =  array_values(empty($fields) ? $this->_fields : $fields);
        $groups     =  $this->_group;
        $paraxparam = array_values($param);
        $xmlitem    = '';
        $xmlinfo    = '';
        //$newarray   = array_combine($fields, $paraxparam);
        foreach ($groups as $key => $value) {
            foreach ($fields as $k => $v) {
                $xmlitem .= '<'.$v.'>'.$paraxparam[$k].'</'.$v.'>';
            }
            $xmlinfo = '<'.$key.'>'.$xmlitem.'</'.$key.'>';
        }
        return $xmlinfo;
    }

    protected function ajaxReturn($data,$type='') {
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);            
            default     :
                // 用于扩展其他返回格式数据
                Hook::listen('ajax_return',$data);
        }
    }
    //必须含有方法
    //Admin 组 管理端方法
    abstract public function run();
   /**
    * Api   组 动态生成内容
    * @param  type类型 detail结构体 originalxml原始xml denytag禁止TAG otherparam其他参数
    * @return type回复体类型  xml具体信息
    */
    abstract public function setInfo($type,$detail,$originalxml,$denytag,$otherparam);
   /**
    * Api   组 动态生成头部
    * @param  type类型 contentStr信息体XML
    * @return string 头部标签
    */
    abstract public function setHead($type, $contentStr);
    abstract public function _empty();
}
