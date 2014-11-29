<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果高校微信公众后台管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------

/**
 * 静态变量输出
 * $param['context']   必须传入
 * 统一隐式传参 session('from')  session('to')
 * @return string 
 */     
function wx_static($param) {
        return $param['context'];
}
/**
 * 微信指令 错误回复
 * @return string 
 */
function wx_error($contentStr,$is_json) {
    $text_tpl = '<xml>
                 <ToUserName><![CDATA[%s]]></ToUserName>
                 <FromUserName><![CDATA[%s]]></FromUserName>
                 <CreateTime>%s</CreateTime>
                 <MsgType><![CDATA[text]]></MsgType>
                 <Content><![CDATA[%s]]></Content>
                 </xml>';
        $time = time();
        $contentStr = empty($is_json)?$contentStr:json_encode($contentStr);
        $contentStr = "[可怜]:出错咯！\n".emoji($contentStr);
        $resultStr = sprintf($text_tpl,session('from'),session('to'), $time, $contentStr);
        echo $resultStr;die;
}
/**
 * 微信指令 错误回复
 * @return string 
 */
function wx_success($contentStr,$is_json) {
    $text_tpl = '<xml>
                 <ToUserName><![CDATA[%s]]></ToUserName>
                 <FromUserName><![CDATA[%s]]></FromUserName>
                 <CreateTime>%s</CreateTime>
                 <MsgType><![CDATA[text]]></MsgType>
                 <Content><![CDATA[%s]]></Content>
                 </xml>';
        $time = time();
        $contentStr = empty($is_json)?$contentStr:json_encode($contentStr);
        $contentStr = "[得意]:成功咯！\n".emoji($contentStr);
        $resultStr = sprintf($text_tpl,session('from'),session('to'), $time, $contentStr);
        echo $resultStr;die;
}
/**
 * 构造JSON回复标签
 * $trace_tab 数组:按值显示;否则:全部显示;支持拓展
 * @return string 
 */
 function wx_tracemodel($trace_tab){
                 global $_W;
                 global $_K;
                //请求信息
                  $trace_info['postcontext'] =  "\n[请求类型:".$_W['msgtype']."]";
                //匹配关键词信息
                  $trace_info['rule'] =  "\n[回复规则]\nID:".$_K['id']."|".$_K['keyword'];
                //请求信息  详细 __SELF__
                $trace_info['post'] = "\n[请求信息]\n".date('m-d H:i:s',$_SERVER['REQUEST_TIME'])."\n".$_SERVER['SERVER_PROTOCOL'].'/'.$_SERVER['REQUEST_METHOD'];
                //运行时间
                                     G('beginTime',$GLOBALS['_beginTime']);
                                     G('viewEndTime');
                  $trace_info['time'] = "\n[运行:".G('beginTime','viewEndTime')."s]\n载入:".G('beginTime','loadTime')."s\n初始:".G('loadTime','initTime')."s\n执行:".G('initTime','viewStartTime')."s";
                //内存开销
                  $trace_info_memory = MEMORY_LIMIT_ON?number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024,2).' kb]':'不支持]';
                  $trace_info['memory'] = "\n[内存:".$trace_info_memory;
                //查询信息
                  $trace_info['sqlinfo'] =  "\n[查询:".N('db_query')."请求|".N('db_write')."操作]";
                //文件加载(数目)
                  $trace_info['filesnum'] =  "\n[文件I/O:".count(get_included_files())."]";
                //缓存信息
                  $trace_info['cache'] =  "\n[缓存:".N('cache_read')."请求|".N('cache_write')."操作]";
                //配置加载
                  $trace_info['conf'] =  "\n[配置加载:".count(c())."]";
                //会话信息
                  $trace_info['session'] =  "\n[SESSION_ID]\n".session_id();
                if(is_array($trace_tab)){
                   $traceinfo = "";
                   foreach ($trace_tab as $value) {
                        $traceinfo .= $trace_info[$value];
                   }
                } else {
                    $traceinfo = implode('', $trace_info);
                }
                if($_SESSION['tagtype']=='duotw'||$_SESSION['tagtype']=='dantw'||$_K['replytype']=='dantw'||$_K['replytype']=='duotw'){
                    $content = array($traceinfo, "1","http://".$_SERVER['HTTP_HOST']."/statics/logo.png","1");
                        return amango_wx_item($content);
                } else {
                        return $traceinfo;
                }
}
