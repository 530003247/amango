<?php
namespace Addons\Toolbox\Controller;
use Common\Controller\Bundle;

/**
 * Toolbox微信处理Bundle
 */
class WeixinController extends Bundle{
	public function index($param){
		$title = strtolower($param['title']);unset($param['title']);
        $this->$title($param);
	}
	//快递查询
	protected function kuaidi($param){
            global $_W;
                $content = str_replace('+', '', $_W['content']);
                $content = str_replace(' ', '', $content);
            if (strstr($_W['content'],'快递')) {
                wx_success("【快递名字+单号】查询快递\n圆通  中通  申通  顺丰  韵达  邮政 EMS\n例查圆通发送：圆通2728435536\n例查中通发送：中通778044381976");
            }
      
            $kuaidic = array();
            $kuaidil = array();
            //单号长度
            $kuaidic["圆通"]  = 10;
            $kuaidic["申通"]  = 12;
            $kuaidic["中通"]  = 12;
            $kuaidic["顺丰"]  = 12;
            $kuaidic["邮政"]  = 13;
            $kuaidic["韵达"]  = 13;
            //快递标识
            $kuaidil["圆通"] = "yuantong";
            $kuaidil["中通"] = "zhongtong";
            $kuaidil["申通"] = "shentong";
            $kuaidil["顺丰"] = "shunfeng";
            $kuaidil["邮政"] = "ems";
            $kuaidil["韵达"] = "yunda";
            //提取单号
            $kuaiditype = msubstr($_W['content'],"0","2", $charset="utf-8", $suffix=false);
            $danhao     = str_replace($kuaiditype,'',$content);

            if (strlen($danhao)!=$kuaidic[$kuaiditype]){
                wx_error("【快递名字+单号】查询快递\n圆通  中通  申通  顺丰  韵达  邮政 EMS\n检查快递单号长度是否填写正确");
            } else {
                //获取该插件配置参数
		        $config = Amango_Addons_Config();
            	$config['kuaidiak'] || wx_error('请联系站长先填写快递API的密匙');
                $url = 'http://api.ickd.cn/?com='.$kuaidil[$kuaiditype].'&nu='.$danhao.'&id=82CD8E8282C652F91CAEE36225FD2373&type=text&encode=utf8&ord=asc';          
                $json = file_get_contents($url); 
                   if (!empty($json)){
                        $str = str_replace(date("Y"),"\n".date("Y"),$json);
                        $str = "快递:".$kuaiditype."\n单号：".$danhao."\n--------------\n". $str;
                        $replycontext['Content'] = $str;
				        $this->assign('Text',$replycontext);
				        $this->display();
                   } else {
                          wx_error("您输入单号查询不到相关信息！");
                   }
            }
	}
	//公交查询
	protected function train($param){
            global $_W;
            $_W['content'] = str_replace('公交', '', $_W['content']);
            $_W['content'] = str_replace(' ', '', $_W['content']);
            $userpost = explode('到', $_W['content']);
            $userfrom = empty($userpost[0]) ? $config['origin'] : $userpost[0];
            $userto   = empty($userpost[1]) ? $config['region'] : $userpost[1];
            if(empty($userto)){
              wx_error("亲:目的地不能为空哦\n公交查询格式\n公交**到**");
            }
            //获取该插件配置参数
		    $config = Amango_Addons_Config();
		    $config['trainak'] || wx_error('请联系站长填写公交API的密匙');
		    $config['where'] || wx_error('请联系站长填写一个默认所在的省市,例如:石狮，厦门');
            $direc = 'http://api.map.baidu.com/direction/v1?mode=transit&origin='.urlencode($userfrom).'&destination='.urlencode($userto).'&region='.urlencode($config['where']).'&output=json&ak='.$config['trainak'];
            $Fisr_result = file_get_contents($direc);
            $Fisrresult  = json_decode($Fisr_result,true);
            $errorMsg[2] = '参数错误';$errorMsg[5] = '权限或配额校验失败';
            if($Fisrresult['status']!=0){
            	wx_error($errorMsg[$Fisrresult['status']]);
            }
            if($Fisrresult['type']==1){
                $resultStr = '';
                foreach ($Fisrresult['result']['origin'] as $key => $value) {
                  $result .= $value['name']."\n";
                }
               wx_error("亲:公交查询格式\n********到********\n出发地不明确,请输入下列地址\n".$result);
            }
            if($Fisrresult['type']==2){
                $resultStr = '';
                $resultinfo = '';
                foreach ($Fisrresult['result']['routes'] as $key => $value) {
                  $result .= "--路线".($key+1)."-----\n";
                  $resultinfo = '';
                  foreach ($value['scheme'][0]['steps'] as $ke => $va) {
                    $resultinfo .= ($ke+1)."):".strip_tags($va[0]['stepInstruction'])."\n";
                  }
                  $result .= $resultinfo;
                  if($key==1){
                      break;
                  }
                }
                    $replycontext['Content'] = "【公交】".$userfrom."=>".$userto."\n".$result;
			        $this->assign('Text',$replycontext);
			        $this->display();
            }
            wx_error("亲:目的地不能为空哦\n公交查询格式\n公交**到**");
	}
	//在这里 支持自定义小工具函数  通过第三方配置的title进行导向
	public function xmltags(){
        return '这是插件tags';
	}
	public function run(){
        return '';
	}
}