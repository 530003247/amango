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
}
?>