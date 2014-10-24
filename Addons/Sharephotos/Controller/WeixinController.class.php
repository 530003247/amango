<?php

namespace Addons\Sharephotos\Controller;
use Common\Controller\Bundle;

/**
 * Sharephotos微信处理Bundle
 */
class WeixinController extends Bundle{
    private $edu_tmp  = 'Sharephotos/Public/sharepics/';
    /**
     * 开发说明:三大全局变量  global $_W,$_P,$_K;            W[微信请求参数]  P[用户信息] K[匹配到关键词信息]
     *          获取插件配置  $config = Amango_Addons_Config();
     *          微信回复      $this->assign(类型,$article);  类型[Text Dantw  Duotw]
     *                        $this->display();
     */
    //插件微信处理默认入口
	public function index($param){
        global $_W,$_P;
        //获取插件配置
        $config    = Amango_Addons_Config('Sharephotos');
        $nowtime   = time();
        if(!empty($_W['picurl'])){
            //限制每天分享的图片数量
                $todaystart = strtotime(date('Y-m-d 00:00:00', $nowtime));
                $todayend   = strtotime(date('Y-m-d 23:59:59', $nowtime));
                $condition  = array(
                    'from'      => $_P['fromusername'],
                    'sharetime' => array('egt',$todaystart),
                    'sharetime' => array('elt',$todayend)
                );
                    $hassharenums = M('Addonssharepics')->where($condition)->count();
                    if($hassharenums>=$config['shareitmes']){
                        $this->assign('Text',"[可怜]亲~您今天已分享了".$config['shareitmes']."次\n请明天再分享吧！");
                        $this->display();
                    }
                $article[0] = array(
                    'Title'       => "分享图片成功",
                    'Description' => "请回复下你对这张照片的评价吧~",
                    'PicUrl'      => $_W['picurl'],
                    'Url'         => $_W['picurl'],
                );
                //锁定图片分享模式
                $this->lock('model','Sharephotos');
                //缓存微信图片链接
                $this->cache('sharepicurl',$_W['picurl']);
                $this->assign('Duotw',$article);
                $this->display();
        } else {
                //判断是否存在模式锁定
                $lastmodel = $this->locked('model');
                $urlcache  = $this->cache('sharepicurl');
                if(!empty($lastmodel)&&!empty($_W['content'])&&!empty($urlcache)){
                    //远程保存图片至本地数据库
                    $urldown  = $this->savepics($urlcache);
                    //将照片地址写入数据库 初始化浏览量为1
                    $data = array(
                        'from'      => $_P['fromusername'],
                        'nickname'  => $_P['nickname'],
                        'picurl'    => $urldown,
                        'sharetime' => $nowtime,
                        'content'   => $_W['content'],
                        'views'     => 1,
                        'status'    => $config['random'],
                    );
                    M('Addonssharepics')->add($data);
                    //清除缓存
                    $this->cache('sharepicurl',NULL);
                    //清除模块锁定
                    $this->lock('model','');
                    //评价照片 
                    $commonurl = $this->create_loginurl('index');
                    $article[0] = array(
                        'Title'       => "分享图片成功",
                        'Description' => "分享理由：".$_W['content'],
                        'PicUrl'      => $urlcache,
                        'Url'         => $commonurl,
                    );
                    $this->assign('Duotw',$article);
                    $this->display();

                } else {
                    //展示图片分享内容  底部附带分享方法
                    $article[0] = array(
                        'Title'       => "精彩美图,随手分享",
                        'Description' => "",
                        'PicUrl'      => str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', ONETHINK_ADDON_PATH.'Sharephotos/Public/banner.jpg'),
                        'Url'         => $this->create_loginurl('index'),
                    );
                    $gebi = array_merge($article,$this->show(6));
                    $this->assign('Duotw',$gebi);
                    $this->display();                    
                }
        }
        wx_error(json_encode($_W));die;
        wx_success('Hello World!这是图吧的微信Bundle！');
	}

    public function savepics($picurl){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $picurl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        $stim       = time();
        $fileUpload = ONETHINK_ADDON_PATH.$this->edu_tmp.$stim.'.jpg';
        header('Content-type: image/jpg');
                    $write_fd   = fopen($fileUpload,"w");
                    fwrite($write_fd,$ret);  //将采集来的远程数据写入本地文件
                    fclose($write_fd);
        header("Content-type:text/html;charset =utf-8");
        return $fileUpload;
    }
    //显示随便照片
    public function show($limit){
        $picslists = M('Addonssharepics')->where(array('status'=>1))->order('rand()')->limit($limit)->select();

        $newlist   = array();
        if(empty($picslists)){
            $errmsg = "发送任意图片,然后评论即可";
            $this->error($errmsg,'','dantw');
        } else {
            $commonurl = $this->create_loginurl('index');$i=0;            
            foreach ($picslists as $key => $value) {
                $i = $i+1;
                $newlist[$i+1] = array(
                    'Title'       => "来自：".$value['nickname']."\n分享：".$value['content'],
                    'Description' => "",
                    'PicUrl'      => str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', $value['picurl']),
                    'Url'         => $commonurl,
                );
            }
        }
            return $newlist;
    }
    public function run(){
        return true;
    }
    //插件展示微信TAG方法
    public function showTips(){
        return '';
    }
}
