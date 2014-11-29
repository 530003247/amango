<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Weixin\Model;

class AmangoModel {
 
    //微信XML体
    protected $originalxml;
    //回复结构体
    protected $detail;

    //是否是静态
    protected $static;
    //微信体类型  
    protected $type;
    //设置下文记录 
    protected $down;
    //设置关键词缓存
    protected $cache;
    //隐藏TAG 
    protected $denytag;
    //执行前行为
    protected $behaviorhead;
    //执行后行为
    protected $behaviorend;
    //菜单锁定
    protected $clickmodel;
    //锁定模式
    protected $lockmodel;

    protected $xmlinfo;
    const XMLHEAD = '<xml>
                         <ToUserName><![CDATA[%s]]></ToUserName>
                         <FromUserName><![CDATA[%s]]></FromUserName>
                         <CreateTime>%s</CreateTime>
                         <MsgType><![CDATA[%s]]></MsgType>';
    const XMLEND  = '</xml>';
/**
* 关键词基础处理
* @param #[关键词]  @[回复体]  默认全局变量
* @return array 
*/
    public function setParam($type,$content){
        $respondeinfo = array();
        switch ($type) {
            case '#':
                $respondeinfo = get_single_keyword($content,$field='*');
                break;
            case '@':
                $respondeinfo = get_single_response($content,$field='*');
                break;            
            default:
                global $_K;
                $respondeinfo = $_K;
                break;
        }
            //回复体状态
            $this->static  = $respondeinfo['response_static'];
            //回复体类型   Api类型
            $this->type    = $respondeinfo['response_reply'];
            //设置下文
            $this->down    = $respondeinfo['keyword_down'];
            //设置缓存
            $this->cache   = $respondeinfo['keyword_cache'];
            //原始XML体
            $this->originalxml  = $respondeinfo['response_xml'];
            //原始XML体
            $this->detail       = $respondeinfo['response_compos'];
            //菜单模式
            $this->clickmodel   = $respondeinfo['click_model'];
            //锁定模式
            $this->lockmodel    = $respondeinfo['lock_model'];
            //回复前执行
            $this->behaviorhead = unserialize($respondeinfo['before_keyword']);
            //回复后执行
            $this->behaviorend  = unserialize($respondeinfo['after_keyword']);
            return $respondeinfo;
    }
/**
* 快捷回复
* @param #[关键词]  @[回复体]  默认全局变量
* 主要  参数分配
* @return array 
*/
    public function response($type,$content){
        //基础处理
        $respondeinfo = array();
        $respondeinfo = $this->setParam($type,$content);

            if($this->static!=1){
                //关闭TAG显示隐藏
                $this->denytag = $respondeinfo['denytag_keyword'];

                self::responseFactory($this->type,$this->detail,$this->originalxml,$this->denytag);
            }
                self::display($this->type,$this->originalxml);
    }

   /**
    * 动态回复处理  生成工厂
    * @access public
    */
    public function responseFactory($type,$detail,$originalxml,$denytag,$otherparam) {
        $type || wx_error('动态工厂类型必须填！');
        $factoryinfo = Factory($type)->setInfo($type,$detail,$originalxml,$denytag,$otherparam);

        $this->type  = empty($factoryinfo['type']) ? $type : $factoryinfo['type'];
        $this->originalxml = empty($factoryinfo['info']) ? $factoryinfo : $factoryinfo['info'];
        return $this->originalxml;
    }
   /**
    * 回复处理  单个 不带标签
    * @access public
    */
    public function responseBlock($type='@',$content) {
        //基础处理
        $this->setParam($type,$content);
        $facorymodel  = Factory($this->type);
        $a            = $facorymodel->setInfo($this->type,$this->detail,$this->originalxml,'','');
        $toplist      = $facorymodel->_top;
        //通用字符转义
        $content = preg_replace('/<amango:[^>]+>/','', $a['info']);
        foreach ($toplist as $key => $value) {
            $keytag   = ucfirst($key);
            $content  = str_replace('<'.$keytag.'>', '', $content);
            $content  = str_replace('</'.$keytag.'>', '', $content);
        }
        return $content;
    }
/************************************ 链式设置操作 ************************************/
   /**
    * 设置模式锁定
    * @param [$detail] [字符串:用户将锁定该行为]
    * @access public
    */
    public function setDentag($denytag) {
        if(!empty($denytag)){
            $this->denytag = $denytag;
        }
    }

   /**
    * 设置回复类型
    * @param [$detail]
    * @access public
    */
    public function setType($detail) {
        if(is_string($detail)){
            $this->type = $detail;
        }
    }

   /**
    * 设置下文锁定
    * @access public
    */
    public function setDown($detail) {
        if(is_numeric($detail)){
            $this->down = $detail;
        }
    }

   /**
    * 设置点击菜单锁定
    * @param [$detail] [数字时:整体更换click模式] [数组时:更换局部click模式]
    * @access public
    */
    public function setClickmodel($detail) {
        if(is_numeric($detail)){
            $this->clickmodel = $detail;
        }
        if(is_array($detail)){
            $this->clickmodel = array_merge($this->clickmodel,$detail);
        }
    }

   /**
    * 设置模式锁定
    * @param [$detail] [字符串:用户将锁定该行为]
    * @access public
    */
    public function setLockmodel($detail) {
        if(is_string($detail)){
            $this->lockmodel = $detail;
        }
    }

   /**
    * 设置缓存时间
    * @param [$detail] [数字时:整体更换click模式] [数组时:更换局部click模式]
    * @access public
    */
    public function setCache($detail) {
        if(is_numeric($detail)){
            $this->cache = $detail;
        }
    }

   /**
    * 设置激活前行为
    * @param [$detail] [数字时:整体更换click模式] [数组时:更换局部click模式]
    * @access public
    */
    public function setBehaviorhead($detail) {
        if(is_array($detail)){
            $this->behaviorhead = array_merge($this->behaviorhead,$detail);
        }
    }

   /**
    * 设置激活后行为
    * @param [$detail] [数字时:整体更换click模式] [数组时:更换局部click模式]
    * @access public
    */
    public function setBehaviorend($detail) {
        if(is_array($detail)){
            $this->behaviorend = array_merge($this->behaviorend,$detail);
        }
    }

   /**
    * 内容赋值
    * @param   array('type','info')[赋值参数]
    * @access public
    */
    public function assign($type, $param) {
        $xml = Factory($type)->load($param)->select();
        $this->xmlinfo = $xml;
        self::setType($type);
    }

   /**
    * 显示输出
    * @param   处理参数
    * @access public
    */
    public function display($type,$xmlinfo) {
        $xmlinfo = empty($this->xmlinfo) ? $xmlinfo : $this->xmlinfo;
        $type    = empty($this->type) ? $type : $this->type;
        session('ReplyType',$type);
        //TODO  激活前行为 暂不启用
        // excute_behavior($this->behaviorhead);
        //回复内容缓存  判断是否含有头部
        $xmlinfo = self::completeInfo($type,$this->denytag,$xmlinfo);

        $xml = (false!==stripos($xmlinfo,'ToUserName')||false!==stripos($xmlinfo,'MsgType')) ? $xmlinfo : self::completeHead($type,$xmlinfo).$xmlinfo.self::XMLEND;
        // $xmlhead = self::completeHead($type,$xmlinfo);
        // $xml     = $xmlhead.$xmlinfo.self::XMLEND;
        echo $xml;
        //个人菜单   锁定
        if($this->clickmodel>0){
           excute_lock('click',$this->clickmodel);
        }
        global $_K;
        //个人关键词 锁定
        if(1==$this->down){
            excute_lock('keyword',$_K['id']);
        }
        //个人模式   锁定
        if(!empty($this->lockmodel)){
            excute_lock('model',$this->lockmodel);
        }

        //设置关键词缓存
        if($this->cache>0){
            excute_cache($_K['id'],$xml,$this->cache,$is_echo=false);
        }

        //TODO  激活后行为 暂不启用
        // excute_behavior($this->behaviorend);
        self::amangolog();
        die;
    }

/****************************************TAG 处理 start*************************************************/
   /**
    * 完整xml
    * @param  默认缓存时间为8个小时
    * @return string 
    */
    private function completeInfo($type, $denytag, $contentStr) {
        $taglist = parse_tags($denytag,$type);
        foreach ($taglist as $key => $value) {
            if($value==0){
                $contentStr = str_replace('<amango:'.$value.'tag>', '', $contentStr);
            }
        }
            $contentStr = preg_replace_callback("/(<amango:[^>]+>)/","xml_parse_tags",$contentStr);
            //清除 未解析标签-
            $preg = '/<amango:[^>]+>/';
            //通用字符转义
            $content = escape_common(preg_replace($preg,'', $contentStr));
            //emoji表情处理
            return emoji($content);
    }

   /**
    * 完整xml
    * @param  默认缓存时间为8个小时
    * @return string 
    */
    private function completeHead($type, $contentStr) {
        if(!empty($type)){
            $factory = Factory($type);
            $head  = sprintf(self::XMLHEAD,session('from'),session('to'),time(),$factory->_replytype);
            $top   = $factory->setHead($type, $contentStr);
            return $head.$top;
        } else {
            wx_error('完整头部');
        }
    }

 /****************************************TAG 处理 end*************************************************/

/**
* 关键词点击量自增
* @return true 
*/
private function amangolog(){
    global $_K;
        $map['id'] = $_K['id'];
        D('KeywordView')->where($map)->setInc('keyword_click');
        return true;
}

}
?>