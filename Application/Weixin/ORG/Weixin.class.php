<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
class Weixin
{ 

    /**
     * 传递参数
     * @access public
     */
    public function __construct($echostr,$token,$postStr,$signature,$timestamp,$nonce) {
        //检验所需的参数
        $this->signature    =   $signature;
        $this->timestamp    =   $timestamp;
        $this->nonce        =   $nonce;

        $this->token        =   $token;
        $this->postStr      =   $postStr;
        $this->echostr      =   $echostr;

        if(!empty($this->echostr)) {
            $this->valid();
        } else {
            $this->responseMsg();
        }
 
    }

	public function valid()
    {
        if($this->checkSignature()){
        	echo $this->echostr;
        	exit;
        }
    }

    public function responseMsg()
    {
		$postStr = $this->postStr;

		if (!empty($postStr)){
                
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        //封装请求到的数据 并打包
          if($postObj instanceof SimpleXMLElement) {
                foreach ($postObj as $variable => $property) {
                    $packet[strtolower($variable)] = (string)$property;
                }
                if(!empty($packet['content'])){
                    $packet['content'] = $this->replace_emoji($packet['content']);                    
                }
            }          
            return $packet;

        }else {
        	return false;
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $this->signature ;
        $timestamp = $this->timestamp ;
        $nonce     = $this->nonce;	
        		
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
    private function replace_emoji($str)
    {
         $amango_bq =Array("/::)"=>"/微笑","/::~"=>"/撇嘴","/::B"=>"/色","/::|"=>"/发呆","/:8-)"=>"/得意","/::<"=>"/流泪","/::$"=>"/害羞","/::X"=>"/闭嘴","/::Z"=>"/睡","/::'("=>"/大哭","/::-|"=>"/尴尬","/::@"=>"/发怒","/::P"=>"/调皮","/::D"=>"/呲牙","/::O"=>"/惊讶","/::("=>"/难过","/::+"=>"/酷","/:--b"=>"/冷汗","/::Q"=>"/抓狂","/::T"=>"吐","/:,@P"=>"/偷笑","/:,@-D"=>"/愉快","/::d"=>"/白眼","/:,@o"=>"/傲慢","/::g"=>"/饥饿","/:|-)"=>"/困","/::!"=>"/惊恐","/::L"=>"/流汗","/::>"=>"/憨笑","/::,@"=>"/悠闲","/:,@f"=>"/奋斗","/::-S"=>"/咒骂","/:?"=>"/疑问","/:,@x"=>"/嘘","/:,@@"=>"/晕","/::8"=>"/疯了","/:,@!"=>"/衰","/:!!!"=>"/骷髅","/:xx"=>"/敲打","/:bye"=>"/再见","/:wipe"=>"/擦汗","/:dig"=>"/抠鼻","/:handclap"=>"鼓掌","/:&-("=>"/糗大了","/:B-)"=>"/坏笑","/:<@"=>"/左哼哼","/:@>"=>"/右哼哼","/::-O"=>"/哈欠","/:>-|"=>"/鄙视","/:P-("=>"/委屈","/::'|"=>"/快哭了","/:X-)"=>"/阴险","/::*"=>"/亲亲","/:@x"=>"/吓","/:8*"=>"/可怜","/:pd"=>"/菜刀","/:<W>"=>"/西瓜","/:beer"=>"/啤酒","/:basketb"=>"/篮球","/:oo"=>"/乒乓","/:coffee"=>"/咖啡","/:eat"=>"/饭","/:pig"=>"/猪头","/:rose"=>"/玫瑰","/:fade"=>"/凋谢","/:showlove"=>"嘴唇","/:heart"=>"/爱心","/:break"=>"/心碎","/:cake"=>"/蛋糕","/:li"=>"/闪电","/:bome"=>"/炸弹","/:kn"=>"/刀","/:footb"=>"/足球","/:ladybug"=>"/瓢虫","/:shit"=>"/便便","/:moon"=>"/月亮","/:sun"=>"/太阳","/:gift"=>"/礼物","/:hug"=>"/拥抱","/:strong"=>"/强","/:weak"=>"/弱","/:share"=>"/握手","/:v"=>"/胜利","/:@)"=>"/抱拳","/:jj"=>"/勾引","/:@@"=>"/拳头","/:bad"=>"/差劲","/:lvu"=>"/爱你","/:no"=>"/NO","/:ok"=>"/OK","/:love"=>"爱情","/:<L>"=>"/飞吻","/:jump"=>"/跳跳","/:shake"=>"/发抖","/:<O>"=>"/怄火","/:circle"=>"/转圈","/:kotow"=>"/磕头","/:turn"=>"/回头","/:skip"=>"/跳绳","/:oY"=>"/投降","/:#-0"=>"/激动","/:hiphot"=>"/乱舞","/:kiss"=>"/献吻","/:<&"=>"/左太极","/:&>"=>"/右太极");
             $str = strtr($str,$amango_bq);
                     return $str;
    }
}
?>