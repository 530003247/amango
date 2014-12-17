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
use Weixin\Controller\Reply;
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
    public    $param      = null;
    public    $auto       = false;
    //引用的回复模型
    public static $replymodel   = 'Amango';
    public static $denyResponse;

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

    /**
     * 获取禁止方法
     * @param  string  $name
     * @return mixed
     */
    protected static function getResponseAction(object$instance){
        if (isset(self::$denyResponse)){
            return self::$denyResponse;
        } else {
            if(is_object($instance)){
                return self::$denyResponse = $instance->deny_static;
            } else {
                Reply::trace('请传入有效回复模型');
            }
        }
    }

    public function __call($method, $args){
        //返回注册回复模型
        $instance    = Reply::setResponseModel(self::$replymodel);
        //模型禁用方法
        $deny_static = self::getResponseAction($instance);
        //判断是否为可用静态方法 * 代表全部可用
        if(in_array($method, $deny_static)){
            $instance->trace('该方法不支持快捷回复哦');
        }
        switch (count($args)){
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }

    //TODO 无法同时使用 __callStatic和__call
    // public static function __callStatic($method, $args){
    //     wx_error('模拟静态回复');
    // }
    
    //必须实现创建
    abstract public function run();
}
