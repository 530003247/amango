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
    //禁止快捷方式
    public $deny_static = array();
    //允许回复类型  调用工厂
    public $reply_type  = array('text','news','voice','music','dantw','duotw');
    //回复类型别名
    public $reply_type_alias = array('news'=>'duotw','articles'=>'duotw');
    //自定义处理Bundle Weixin\Bundle\名称Bundle
    public $event_type = array('click');
    //微信XML体
    public $originalxml;
    //回复结构体
    public $detail;
    //是否是静态
    public $static;
    //微信体类型  
    public $type;
    //设置下文记录 
    public $down;
    //设置关键词缓存
    public $cache;
    //隐藏TAG 
    public $denytag;
    //执行前行为
    public $behaviorhead;
    //执行后行为
    public $behaviorend;
    //菜单锁定
    public $clickmodel;
    //锁定模式
    public $lockmodel;

    public $xmlinfo;

    public static $factoryObj;

    public static $factoryModel;

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

                $this->responseFactory($this->type,$this->detail,$this->originalxml,$this->denytag);
            }
                $this->display($this->type,$this->originalxml);
    }

   /**
    * 动态回复处理  生成工厂
    * @access public
    */
    public function responseFactory($type,$detail,$originalxml,$denytag,$otherparam) {
        $type || $this->trace('动态工厂类型必须填！');
        $factoryinfo = $this->setFactory($type)->setInfo($type,$detail,$originalxml,$denytag,$otherparam);
        $this->type  = empty($factoryinfo['type']) ? $type : $factoryinfo['type'];
        $this->originalxml = empty($factoryinfo['info']) ? $factoryinfo : $factoryinfo['info'];
        return $this->originalxml;
    }
    
    public function runBundle($name){
        //判断是否为自定义Bundle
        if(!in_array($name,$this->event_type)){
            $keywordinfo = get_posttype_list(strtolower($event));
            if(empty($keywordinfo)){
                $this->autoreply('auto');
            } else {
                global $_K;$_K = $keywordinfo[0];
                get_keyword_user_auth(true);
                $this->limit_top();
                $this->cache($_K['id'],'',$_K['keyword_cache'],true);
                $this->response();
            }
        } else {
            if (is_object($name)){
                $factorymodel = $name;
            } else {
                if (isset(self::$factoryObj[$name])){
                    $factorymodel = self::$factoryObj[$name];
                } else {
                    $name = "Weixin\Bundle\\".ucfirst($name)."Bundle";
                    $factorymodel = new $name();
                    self::$factoryObj[$name] = $factorymodel;
                }
            }
                $factorymodel->run();
        }
    }
   /**
    * 回复处理  单个 不带标签
    * @access public
    */
    public function responseBlock($type='@',$content) {
        //基础处理
        $this->setParam($type,$content);
        $facorymodel  = $this->setFactory($this->type);
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
    public function setFactory($name){
        if (is_object($name)) return $name;
        $name = strtr(strtolower($name), $this->reply_type_alias);
        $name = ucfirst((strtolower($name)=='basic') ? 'Duotw' : $name);
        if (isset(static::$factoryModel[$name])){
            return static::$factoryModel[$name];
        } else {
            $factory = '\Common\Factory\\'.$name.'Factory';
            return static::$factoryModel[$name] = new $factory;
        }
    }
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
        $xml = $this->setFactory($type)->load($param)->select();
        $this->xmlinfo = $xml;
        $this->setType($type);
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
        $xmlinfo = $this->completeInfo($type,$this->denytag,$xmlinfo);

        $xml = (false!==stripos($xmlinfo,'ToUserName')||false!==stripos($xmlinfo,'MsgType')) ? $xmlinfo : $this->completeHead($type,$xmlinfo).$xmlinfo.self::XMLEND;
        // $xmlhead = self::completeHead($type,$xmlinfo);
        // $xml     = $xmlhead.$xmlinfo.self::XMLEND;
        hook('weixin_end',&$xml);
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
        self::amangolog();
        hook('weixin_end');
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
            $factory = $this->setFactory($type);
            $head  = sprintf(self::XMLHEAD,session('from'),session('to'),time(),$factory->_replytype);
            $top   = $factory->setHead($type, $contentStr);
            return $head.$top;
        } else {
            $this->trace('头部不完整');
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

    //数据接口
    public final function cloud($cloudid) {
        wx_success($content);
    }

    /**
    * 锁定机制
    * @param model[模式锁定]  keyword[关键词锁定]
    */
    public final function lock($model,$content) {
        excute_lock($model,$content);
    }

    /**
    * 获取锁定值
    * @param model[模式锁定]  keyword[关键词锁定]
    * @return array 
    */
    public final function locked($model='model', $cachetime=120, $limit=true) {
        global $_P;
        if(empty($model)){
            $info = get_line_top();
        } else {
            $title = 'last'.strtolower($model);
            $last  = $_P[$title];
            if(empty($last)){
                return '';
            } else {
                $info  = explode('|', $last);
                session('user_'.$title,$last);
            }
        }
            if($limit){
                $accesstime = time()-$info[1];
                return ($accesstime<=$cachetime)?$info[0]:'';
            } else {
                return $info[0];
            }
    }

    //校验上级关键词
    public final function limit_top($limittime=120) {
        global $_K;
        if($_K['keyword_top']>0){
            $topaction  = get_line_top();
            $accesstime = time()-$topaction[1];
            if($topaction[0]!=$_K['keyword_top']){
                $this->error('激活该关键词之前,您要先'.error_type($_K['keyword_top']));
            }
        }
            return true;
    }

    //设置缓存
    public final function cache($keyid,$content,$cache_time=60,$is_echo=false) {
        if(!empty($keyid)){
            $keyword_cachename = session('from').'cache'.$keyid;
            if(''=== $content){ // 获取缓存
                $cache_contxt = S($keyword_cachename);
                return $cache_contxt;
            }elseif(is_null($content)) { // 删除缓存
                S($keyword_cachename,NULL);
            }else { // 缓存数据
                if($cache_time>0){
                    S($keyword_cachename,$content,$cache_time);
                } 
            }
        }
    }

    /**
     * 自动回复
     * @param  string  $type  [hello,auto,outtime,black,empty]
     * @return mixed
     */
    public final function autoreply($type) {
        $type = in_array($type,array('hello','auto','outtime','black','empty'))?strtolower($type):'auto';
        $info = M('Config')->where(array('name'=>'AMANGO_DEFAULT_REPLY'))->getField('value');
        $data = explode(',', $info);
        $type_reply = array(
            'hello'   => $data[0],
            'black'   => $data[1],
            'outtime' => $data[2],
            'auto'    => $data[2],
        );
        $replyid = $type_reply[$type];
        if(empty($replyid)||$type=='empty'){
            echo "";die;
        } else {
            $this->response('@',$replyid);
        }
    }

    /**
     * 自动回复
     * type = text
     * @param  content(string)[文本内容] url(array)[0=>链接,1=>链接名称] title(string)[默认名称]
     * type = dantw || duotw
     * @param  content(string)[图文描述] url(string)[跳转链接] title(string)
     * @return mixed
     */
    public final function error($content,$url,$type='text',$title,$PicUrl,$status=true) {
        $status = (false==$status) ? false : true;
        //wx_error($type.json_encode($status));
        if(in_array(strtolower($type),array('dantw','duotw'))){
            //图文默认图片
            $defaultpic   = ($status) ? "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg" : "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg";
            $PicUrl       = empty($PicUrl) ? $defaultpic : $PicUrl;
            //图文默认标题
            $defaultitle  = ($status) ? "Sorry！操作失败咯~" : "Success！操作成功咯~";
            $title        = empty($title) ? $defaultitle : $title;
            $article[0] = array(
              'Title'       => $title,
              'Description' => $content,
              'PicUrl'      => $PicUrl,
              'Url'         => $url,
            );
            $this->assign('Duotw',$article);
        } else {
            if(!empty($url[0])&&!empty($url[1])){
                $urlinfo = "<a href='".$url[0]."'>".$url[1]."</a>";
            }
            //文本回复标题
            $defaul_title = ($status) ? "[可怜]:出错咯！" : "[得意]:成功咯!";
            $title   = empty($title) ? $defaul_title : $title;
            $this->assign('Text',$title."\n".$content."\n".$urlinfo);
        }
            $this->display();
    }

    //成功回复
    public final function success($content,$url,$type='text',$title,$PicUrl) {
        $this->error($content,$url,$type,$title,$PicUrl,false);
    }

    // 自动生成插件前台页面地址  
    // url    '控制器名/操作方法'  或者为 '操作方法'
    // param  额外参数 建议采用数组形式
    // home   默认插件模块分组  默认为home  此处可默认  不建议改写
    public final function create_url($url,$param=array(),$home='Home') {
        $result = weixin_addons_url($url,$param,$home);
        if(false!=$result){
            return $result;
        } else {
            return null;
        }
    }

    // 自动生成插件前台  自动登陆页面地址 使用方法同上
    public final function create_loginurl($url,$param=array(),$home='Home') {
        $aimurl = weixin_addons_url($url,$param,$home);
        global $_P;
        $autoinfo = array(
                'nickname'   => $_P['nickname'],
                'ucusername' => $_P['ucusername'],
                'ucpassword' => $_P['ucpassword'],
                'amangogoto' => base64_encode(base64_encode($aimurl))//双重base64 防止出现/
        );
        $autolink = U('Home/User/login',$autoinfo,'',true);
        return $autolink;
    }

    public final function message() {
        wx_success('信息机制');
    }
    
    public final function trace($errormsg){
        global $_G;
        if($_G['Weixin_trace']){
            wx_error($errormsg);
        } else {
            $this->autoreply('auto');
        }
    }
    /**
     * 处理公共方法
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args){
        //是否属于自动回复方法
        if(in_array($method, $this->reply_type)){
            $this->assign($method,$args[0]);
            $this->display();
        } else {
            $this->trace('该方法不存在');
        }
    }
}
?>