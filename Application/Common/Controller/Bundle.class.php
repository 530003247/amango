<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
//Bundle  官方继承  插件继承
namespace Common\Controller;
/**
 * Bundle抽象类   封装常用操作
 * @author Kevin
 */
abstract class Bundle{
    //截取类型
    const W_COMMON     =   1;//通用截取
    const W_PREG       =   2;//正则函数
    const W_SELF       =   3;//自身模式
    //微信请求    
    static $WEIXIN_POST   =  array('text','common','image','voice','video','location','link');

    protected $data       = array();
    protected $click      = null;
    protected $denytag    = null;
    protected $amango     = object;

    public    $wxheadword = null;
    public    $wxmainword = null;
    public    $param    = null;
    public    $auto     = false;

    public function __construct($param=null,$auto=false) {
        if(!empty($param)){
            $this->param = $param;
        }
        $is_auto = (empty($this->_auto)||true==$this->_auto)?true:false;
        //自动处理匹配 必须拥有_rules
        if(true==$auto&&true==$is_auto&&!empty($this->_rules)){
            //获取到请求规则
            global $_W;
            //获取默认规则属性
            $rules = $this->_rules[strtoupper($_W['post'])];
            //将CLICK点击转化为content的关键词
            $this->Event2Content('CLICK',array('content'=>$param['title']));
            if(empty($rules)){
                $this->error('亲,#请求规则有误,请检查是否存在【规则】或者【默认操作方法】');
            } else {
                //强制执行参数
                $acname  = $rules['_action'][0];
                $acparam = $rules['_action'][1];
            }
            //如果强制  直接执行
            if(!empty($acname)){
                $this->$acname($acparam);
            }
                //获取要截取的原始数据
                if(isset($rules['_wtype'])){
                    $initcontent = $_W[$rules['_wtype']];
                }
                    unset($rules['_action']);unset($rules['_wtype']);
                    $preg_rules = array();
                    foreach ($rules as $key => $value) {
                        if (preg_match($key,$initcontent)){
                                $preg_rules = $rules[$key];
                                break;
                        }
                    }
                        if(empty($preg_rules)){
                            $this->index();
                        } else {
                            $pregparam = array();
                            $pregparam = $preg_rules['_replace'][2];

                            switch ($preg_rules['_replace'][0]) {
                                case self::W_SELF:
                                    $start = '_'.$preg_rules['_replace'][1];
                                    $end   = $pregparam;
                                    $model = self::W_SELF;

                                    break;
                                case self::W_PREG:
                                    $space = (true===$pregparam[2])?true:false;
                                    $model = $preg_rules['_replace'][1];

                                    break;
                                case self::W_COMMON:
                                    $start = (empty($pregparam[0])||!is_numeric($pregparam[0]))?0:$pregparam[0];
                                    $end   = (empty($pregparam[1])||!is_numeric($pregparam[1]))?2:$pregparam[1];
                                    $space = (true===$pregparam[2])?true:false;
                                    $model = self::W_COMMON;
                                    break;
                                default:
                                    $this->index();
                                    break;
                            }
                            //自动截取关键词
                            $this->getW($initcontent,$start,$end,$model,$space);
                            //反馈方法
                            $actionname = $preg_rules['_action'];
                            //$this->wxheadword[截取的关键词]
                            //$this->wxmainword[截取后关键词]
                            $this->$actionname($param);
                        }
        }
    }

/*************************** 自动处理 ****************************/
    
    protected final function execute_rules($param) {

    }
    //自动回复 type：auto hello black 
    protected final function autoreply($type) {
            $info = M('Config')->where(array('name'=>'AMANGO_DEFAULT_REPLY'))->getField('value');
            $data = explode(',', $info);
            $this->amango = new \Weixin\Model\AmangoModel;
            switch ($type) {
                //关注时候
                case 'hello':
                    empty($data[0]) ? wx_error('欢迎关注') : $this->amango->response('@',$data[0]);
                    break;
                //黑名单
                case 'black':
                    empty($data[1]) ? wx_error('对不起，您暂时无法使用该功能') : $this->amango->response('@',$data[1]);
                    break;
                //默认超时
                default:
                    empty($data[2]) ? wx_error('你所发送的关键词无法匹配,请换个关键词吧') : $this->amango->response('@',$data[2]);
                    break;
            }
    }
/*************************** 请求处理 ****************************/
    //请求映射成请求体
    protected final function Event2Content($event='CLICK',$changetype=array()) {
        global $_W;
            if($_W['event'] == $event){
                foreach ($changetype as $key => $value) {
                    $_W[$key] = $value;
                }
            }
                return $_W;
    }
    //请求截取 截取模式 [字符串截取:string][正则截取:正则语句]
    protected final function getW($content, $start=0, $end=2, $model=self::W_COMMON, $space=true) {
        if(true===$space){
            $content = str_replace(' ', '', $content);
        }
        switch ($model) {
            case self::W_SELF:
                //自身定义函数截取
                $actionname  = '_'.$start;
                $result      = $this->$actionname($end);
                $this->wxheadword = $result['wxheadword'];
                $this->wxmainword = str_replace($result['wxheadword'], '', $content);
                return $this->wxheadword;
                break;
            case self::W_COMMON:
                //字符串截取
                $this->wxheadword = msubstr($content, $start, $end, $charset="utf-8", $suffix=false);
                $this->wxmainword = str_replace($this->wxheadword, '', $content);
                return $this->wxheadword;
                break;
            default:
                //正则截取
                preg_match_all($model,$content,$match);
                foreach ($match[1] as $key => $value) {
                    $this->wxheadword[] = $value;
                    $this->wxmainword[] = str_replace($value, '', $content);
                }
                return $this->wxheadword[0];
                break;
        }
    }
/*************************** 消息回复 ****************************/
    //调试显示
    protected final function trace($content,$tracemodel) {
                global $_W,$_K;
                //请求信息
                $trace_info['postcontext'] =  "\n[请求类型:".$_W['msgtype']."]";
                //匹配关键词信息
                $trace_info['rule']        =  "\n[回复规则]\nID:".$_K['id']."|".$_K['keyword']."\nResponse:".$_K['response_id'];
                //请求信息  详细 __SELF__
                $trace_info['post'] = "\n[请求信息]\n".date('m-d H:i:s',$_SERVER['REQUEST_TIME'])."\n".$_SERVER['SERVER_PROTOCOL'].'/'.$_SERVER['REQUEST_METHOD'];
                //运行时间
                G('beginTime',$GLOBALS['_beginTime']);
                G('viewEndTime');
                $trace_info['time'] = "\n[运行:".G('beginTime','viewEndTime')."s]\n载入:".G('beginTime','loadTime')."s\n初始:".G('loadTime','initTime')."s\n执行:".G('initTime','viewStartTime')."s";
                //内存开销
                $trace_info_memory  = MEMORY_LIMIT_ON?number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024,2).' kb]':'不支持]';
                $trace_info['memory']   = "\n[内存:".$trace_info_memory;
                //查询信息
                $trace_info['sqlinfo']  =  "\n[查询:".N('db_query')."请求|".N('db_write')."操作]";
                //文件加载(数目)
                $trace_info['filesnum'] =  "\n[文件I/O:".count(get_included_files())."]";
                //缓存信息
                $trace_info['cache']    =  "\n[缓存:".N('cache_read')."请求|".N('cache_write')."操作]";
                //配置加载
                $trace_info['conf']     =  "\n[配置加载:".count(c())."]";
                //会话信息
                $trace_info['session']  =  "\n[SESSION_ID]\n".session_id();
                $tracestr = implode('', $trace_info);
                $this->assign('Text',$content."\n调试信息".$tracestr);
                $this->display();
    }

    //失败回复  array(链接,标题)，跳转地址，类型【text】【dantw】
    protected final function error($content,$url,$type='text',$title,$PicUrl) {
        $PicUrl = empty($PicUrl) ? "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg" : $PicUrl;
        switch (strtolower($type)) {
            case 'dantw':
                $title      = empty($title) ? "Sorry！操作失败咯~" : $title;
                $article[0] = array(
                  'Title'       => $title,
                  'Description' => $content,
                  'PicUrl'      => $PicUrl,
                  'Url'         => $url[0],
                );
                $this->assign('Duotw',$article);
                break;
            case 'duotw':
                $title      = empty($title) ? "Sorry！操作失败咯~" : $title;
                $article[0] = array(
                  'Title'       => $title,
                  'Description' => $content,
                  'PicUrl'      => $PicUrl,
                  'Url'         => $url[0],
                );
                $this->assign('Duotw',$article);
                break;
            default:
                if(!empty($url[0])&&!empty($url[1])){
                    $urlinfo = "<a href='".$url[0]."'>".$url[1]."</a>";
                }
                $title   = empty($title) ? "[可怜]:出错咯！" : $title;
                $this->assign('Text',$title."\n".$content."\n".$urlinfo);
                break;
        }
            $this->display();
    }
    //成功回复
    protected final function success($content,$url,$type='text',$title,$PicUrl) {
        $PicUrl = empty($PicUrl) ? "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg" : $PicUrl;
        switch (strtolower($type)) {
            case 'dantw':
                $title      = empty($title) ? "Success！操作成功咯~" : $title;
                $article[0] = array(
                  'Title'       => $title,
                  'Description' => $content,
                  'PicUrl'      => $PicUrl,
                  'Url'         => $url[0],
                );
                $this->assign('Duotw',$article);
                break;
            case 'duotw':
                $title      = empty($title) ? "Success！操作成功咯~" : $title;
                $article[0] = array(
                  'Title'       => $title,
                  'Description' => $content,
                  'PicUrl'      => $PicUrl,
                  'Url'         => $url[0],
                );
                $this->assign('Duotw',$article);
                break;
            default:
                if(!empty($url[0])&&!empty($url[1])){
                    $urlinfo = "<a href='".$url[0]."'>".$url[1]."</a>";
                }
                $title       = empty($title) ? "[得意]:成功咯" : $title;
                $this->assign('Text',$title."\n".$content."\n".$urlinfo);
                break;
        }
            $this->display();
    }
/*************************** 自动生成 ****************************/
    // 自动生成插件前台页面地址  
    // url    '控制器名/操作方法'  或者为 '操作方法'
    // param  额外参数 建议采用数组形式
    // home   默认插件模块分组  默认为home  此处可默认  不建议改写
    protected final function create_url($url='',$param=array(),$home='Home') {
        $result = weixin_addons_url($url,$param,$home);
        if(false==$result){
            wx_error('请填写生成该插件的操作方法');
        } else {
            return $result;
        }
    }

    // 自动生成插件前台  自动登陆页面地址 使用方法同上
    protected final function create_loginurl($url,$param=array(),$home='Home') {
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

/*************************** 机制接口 ****************************/
    //信息机制
    protected final function message() {
        wx_success('信息机制');
    }

    //行为机制
    protected final function behavior() {
        wx_success('行为机制');
    }

    //数据接口
    protected final function cloud($cloudid) {
        wx_success($content);
    }   

/*************************** 常用操作 ****************************/  
    //设置缓存
    protected final function cache($keyid,$content='',$cache_time=60,$is_echo=false) {
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
* 锁定机制
* @param model[模式锁定]  keyword[关键词锁定]
*/
    protected final function lock($model,$content) {
        excute_lock($model,$content);
    }

/**
* 获取锁定值
* @param model[模式锁定]  keyword[关键词锁定]
* @return array 
*/
    protected final function locked($model='model', $cachetime=120, $limit=true) {
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
    protected final function limit_top($limittime=120) {
        global $_K;
        if($_K['keyword_top']>0){
            $topaction  = get_line_top();
            $accesstime = time()-$topaction[1];
            return ($topaction[0]!=$_K['keyword_top'])?wx_error('激活该关键词之前,您要先'.error_type($_K['keyword_top'])):true;
        }
            return true;
    }

/*************************** 主要函数 ****************************/ 
/**
* 快捷回复
* @param keyword[关键词]  response[回复体]
* @return array 
*/
    protected function response($type,$content) {
        $this->amango = new \Weixin\Model\AmangoModel;
        $this->amango->response($type,$content);
    }

/**
* 动态设置菜单点击
* @return array 
*/
    protected function Setclick($where) {
        wx_error('菜单模式');
    }

/**
* 取消tag显示  暂时不开启关闭tag
* @return array 
*/
    protected function Dectag($denytags) {
        $this->denytag = $denytags;
        return $this->denytag;
    }

/**
* 赋值体
* @param array('type','info')
* @return array 
*/
    protected function assign($type, $param) {
        $this->amango = new \Weixin\Model\AmangoModel;
        //默认为文本
        $this->data   = empty($param) ? array('type'=>'text','content'=>$type) : array('type'=>$type,'content'=>$param);
        return $this->data;
    }

/**
* 输出消息
* @param 主题模式
* @return array 
*/
    protected function display($type,$xmlinfo) {
        $type    = empty($this->data['type']) ? $type : $this->data['type'];
        $xmlinfo = empty($this->data['content']) ? $xmlinfo : $this->data['content'];

        $this->amango->setDentag($this->denytag);
        $this->amango->assign($type,$xmlinfo);
        $this->amango->display($type,$xmlinfo);
    }
    //必须实现创建  全部方法
    abstract public function run();
}
