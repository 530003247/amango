<?php

namespace Addons\Neighbours\Controller;
use Common\Controller\Bundle;

/**
 * Neighbours微信处理Bundle
 */
class WeixinController extends Bundle{

	public $content     = array();
	public $is_neibours = '';

	public function __construct(){
		global $_W;
		if(empty($_W)){
        //这里是Api接口入口
          echo 'Api接口模块还未开启,敬请期待【隔壁】2.0';
		} else {
			global $_W;
	        $_W['content'] = str_replace(' ', '', $_W['content']);
	        $_W['content'] = str_replace('，', ',', $_W['content']);
	        $this->content = $_W;
	    	//隔壁简介  纯隔壁 点击
	        if($_W['content']=='隔壁'||$_W['event']=='CLICK'){
	        	$article[0] = array(
	              'Title'       => "-隔壁-有朋自远方来",
	              'Description' => "怎么玩转隔壁呢？\n1.发送内容包含“隔壁”即可分享你的动态\n2.@本地朋友昵称(学校)@+任意含有隔壁的内容，即可@来自远方大学或朋友\n3.发送任意图片即可实时分享到隔壁",
	              'PicUrl'      => "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg",
	              'Url'         => "",
	        	);
	        	$gebi = array_merge($article,$this->show());
	            $this->assign('Duotw',$gebi);
	            $this->display();
	        }
	    	//隔壁简介
	        if($_W['content']=='隔壁图片'){
	            $this->lock('model','Neighbours/share_pic');
	            wx_success("亲，您已进入“隔壁图片”分享哦，请在聊天窗口中发送您的想分享的图片");
	        }
	    	//隔壁简介
	        if($_W['content']=='隔壁位置'){
	            $this->lock('model','Neighbours/share_location');
	            wx_success("亲，您当前正在设置位置哦，请在聊天窗口中发送您的有效位置(无法定位的话,拜托您手动输入)");
	        }
	    	//隔壁大学
	        if($_W['content']=='隔壁大学'){
	        	$article = array(
	              'Title'       => "-隔壁-有朋自远方来",
	              'Description' => "目前支持的大学:\n厦门工学院,天津外国语大学\n分享给其他大学,请使用#厦门工学院#+您的分享\n分享给其他大学的朋友,请使用#厦门工学院,你朋友的昵称#+您的分享\n分享给本校校友,使用@朋友昵称@+您的分享",
	              'PicUrl'      => "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg",
	              'Url'         => "",
	        	);
	            $this->assign('Duotw',$article);
	            $this->display();
	        }
	    	//隔壁简介
	        if($_W['content']=='退出'){
	            $this->lock('model','');
	            wx_error('退出成功！');
	        }
		}
	}
	//主处理控制器
	public function index($param){
		$content = $this->content;
		$gebiconditon = array();
		$school       = '';
		$to           = '';
		//匹配  @...@  #...#模式
		//分享给其他大学,请使用#厦门工学院#+您的分享
		//分享给其他大学的朋友,请使用#厦门工学院,你朋友的昵称#+您的分享
		//分享给本校校友,使用@朋友昵称@+您的分享
		//匹配异地大学
		preg_match('/(#[^#]+#)/', $content['content'], $tag_name);
		if(empty($tag_name[0])){
			preg_match('/(@[^@]+@)/', $content['content'], $tagname);
			if(empty($tagname[0])){
				//正常
                $str = $content['content'];
			} else {
				//匹配本地用户 多用户 @A,B,C,D,E@
				$str = str_replace($tagname[0], '', $content['content']);
                $to  = str_replace('@', '', $tagname[0]);
			}
		} else {
			    //分享给其他大学,请使用#厦门工学院|用户A,B,C,D#
			    //目前只支持一次  一个大学多个人
			    $str        = str_replace($tag_name[0], '', $content['content']);
			    $schoolinfo = str_replace('#', '', $tag_name[0]);
                $exinfo     = explode('|', $schoolinfo);
                $school     = $exinfo[0];
                $to         = (count($exinfo)==2) ? $exinfo[1] : '';
                self::sendOther($school,$to);
		}
		//获取个人信息
		global $_P;
		//获取配置
		$config = Amango_Addons_Config();
		$now          = time();
		$location     = explode('|', $_P['location']);
		$locationinfo = (count($location)==2) ? $location[1] : $config['name'];
		if(empty($str)){
            wx_error('亲！请发送带有隔壁字样的动态哦~');
		} else {
			$data = array(
					'from'      =>$_P['id'],
					'school'    =>$school,
					'sharetype' =>'text',
					'content'   =>$str,
					'creattime' =>$now,
					'location'  =>$locationinfo,
					'to'        =>$to
			);
		}
			M('Addonsneighbours')->add($data);
		    $article = array(
	          'Title'       => "-隔壁-有朋自远方来",
	          'Description' => "您于".date("Y-m-d h:i:s", $now)."在隔壁说:\n".$str,
	          'PicUrl'      => "http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg",
	          'Url'         => "",
	    	);
	        $this->assign('Duotw',$article);
	        $this->display();
	}
	//处理图片分享
    public function share_pic(){
        $userpost = $this->content;
        wx_error('恭喜您，成功分享一张照片！');
        //wx_error(json_encode($userpost));
    }
    //处理位置绑定
    public function share_location(){
    	$userpost = $this->content;
    	if($userpost['msgtype']!='location'){
            wx_error("亲，您当前正在设置位置哦，请在聊天窗口中发送您的有效位置(无法定位的话,拜托您手动输入)");
    	}
    	if(empty($userpost['label'])){
            wx_error("亲，您刚才设置的位置识别不出，请在聊天窗口中发送您的有效位置(无法定位的话,拜托您手动输入)");
    	}
    	global $_P;
    	$locationdata = $userpost['location_x'].'/'.$userpost['location_y'].'|'.$userpost['label'];
    	M('Weixinmember')->where(array('id'=>$_P['id']))->save(array('location'=>$locationdata));
        $this->lock('model','');
        wx_success("恭喜您，设置常用位置成功！个性地址:\n".$userpost['label']);
    }

    public function sendOther($school,$to){
    	//$to  可能为多用户
    	return true;
        wx_error($school.'|'.$to);
    }

    public function show($where,$limit=1){
		//获取配置
		$config = Amango_Addons_Config();
		$limit  = ($limit==0) ? 1 : $limit;
		$nums   = ($config['nums']>=1) ? $config['nums'] : $limit;

  //   	//$to  可能为多用户
  //   	//获取本地隔壁动态 至少一条
    	$lists  = M('Addonsneighbours')->where(array('to'=>''))->order('creattime desc')->order('rand()')->field('id,from,location,creattime,view,sharetype,to,school,content')->limit($nums)->select();

    	//$lists  = D('Addons://Neighbours/AddonsneighboursView')->select();
    	$newlist= array();
    	$usermodel = M('Weixinmember');
        foreach ($lists as $key => $value) {
        	$nickname = $usermodel->where(array('id'=>$value['from']))->getField('nickname');
        	if($value['sharetype']=='text'){
        		$randnum = rand(1,6);
        		$randimg = 'http://'.$_SERVER['HTTP_HOST'].'/Addons/Neighbours/Public/images/default_head'.$randnum.'.png';
		         	$newlist[$key] = array(
				        'Title'       => "-".$nickname."-刚在隔壁说:\n".$value['content'],
				        'Description' => "",
				        'PicUrl'      => $randimg,
				        'Url'         => U('Home/Addons/execute',array('_addons'=>'Neighbours','_controller'=>'Home','_action'=>'index','id'=>$value['id']),'',true),
		         	);
        	}
        	if($value['sharetype']=='picture'){
	         	$newlist[$key] = array(
			        'Title'       => "-".$nickname."-在隔壁分享了一张照片",
			        'Description' => "",
			        'PicUrl'      => $value['content'],
			        'Url'         => "",
	         	);
        	}
        }

        //判断是否有外大学提醒缓存
        $tips = S('Neighbours_tip');
        if(empty($tips)){
        	$tips = ',厦门工学院';
            $schoolinfo = explode(',', $tips);
            unset($schoolinfo[0]);
            //读取外校授权列表
            $othertips = array();
            $other     = parse_config($config['dsfjr']);
            foreach ($schoolinfo as $key => $value) {
            	//判断该学校是否在名下
            	if(!empty($other[$value])){
                    $urllist[] = $other[$value];
            	}
            }
            $othertips = $this->readOther($urllist);
        }
     	return $newlist;
    }
    //读取站外信息
    public function readOther($urllist){
    	return true;
  //   	if(!is_array($urllist)){
  //   		$urls   = $urllist;
  //   	} else {
  //   		$urls[] = $urllist;
  //   	}
  //       //多线程抓取信息  
		// $mh = curl_multi_init();    
		// foreach ($urls as $i => $url) {   
		// 	$conn[$i] = curl_init($url);   
		// 	curl_setopt($conn[$i], CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");   
		// 	curl_setopt($conn[$i], CURLOPT_HEADER ,0);
		// 	curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1 );
		// 	curl_setopt($conn[$i], CURLOPT_CONNECTTIMEOUT,4);
		// 	curl_setopt($conn[$i], CURLOPT_CONNECTTIMEOUT,3);
		// 	curl_multi_add_handle($mh,$conn[$i]);
		// } // 初始化

		// $data = array();
		// do {   
	 //      curl_multi_exec($mh,$active);
		// } while ($active);

		// foreach ($urls as $i => $url) {   
		//   $data[] = curl_multi_getcontent($conn[$i]);
		// }

		// foreach ($urls as $i => $url) {   
		// 	curl_multi_remove_handle($mh,$conn[$i]);
		// 	curl_close($conn[$i]);   
		// } // 结束清理

		// curl_multi_close($mh);   
  //       return $data;
    }
    //TAG动态展示
    public function showTips($param){
    	global $_W;
        if(false!==strpos($_W['content'],'隔壁')){
	    	$type = session('ReplyType');
	    	switch (strtolower($type)) {
	    		case 'duotw':
	    			return "<item><Title><![CDATA[-隔壁-有朋自远方来]]></Title><Description><![CDATA[怎么玩转隔壁呢？
	1.发送内容包含“隔壁”即可分享你的动态
	2.@本地朋友昵称(学校)@+任意含有隔壁的内容，即可@来自远方大学或朋友
	3.发送任意图片即可实时分享到隔壁]]></Description><PicUrl><![CDATA[http://a.36krcnd.com/photo/2014/5ba35e8ea888894d706afad6a8858037.jpg]]></PicUrl><Url><![CDATA[]]></Url></item>";
	    			break;
	    		case 'dantw':
	return '暂无隔壁动态';
	    			break;
	    		default:
	    		return '1暂无隔壁动态';
	    			break;
	    	}
        } else {
        	return '';
        }
    }
    public function run(){
        return true;
    }
}
