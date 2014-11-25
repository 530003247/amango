<?php

namespace Addons\Exam\Controller;
use Common\Controller\Bundle;

/**
 * Exam微信处理Bundle
 */
class WeixinController extends Bundle{
    public $_rules = array(
        'COMMON'    => array(
                            '_action'  => null,
                            '_wtype'   => 'content',
                            '/^考试/' => array(
                                                '_action'  => 'show_test',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            )
                            ),
        'CLICK'    => array(
                            '_action'  => null,
                            '_wtype'   => 'content',
                            '/^考试/' => array(
                                                '_action'  => 'show_test',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            )
                            )
    );
	public function index(){
        wx_success('Hello World!这是微考试的微信Bundle！');
	}
    private function default_tw($list){
        $article    = array();
        $article[0] = array(
              'Title'       => "微考试",
              'Description' => "子曰:学而时习之,不亦说乎！\n",
              'PicUrl'      => ADDON_PUBLIC.'exam.jpg',
              'Url'         => '',
        );
        $article[1] = array(
              'Title'       => "发送[考试+关键词]开始考试\n发送[考试中心]查看个人记录",
              'Description' => "",
              'PicUrl'      => '',
              'Url'         => '',
        );
        if(empty($list)){
            $article[2] = array(
                  'Title'       => "暂时木有考试！",
                  'Description' => "子曰:学而时习之,不亦说乎！",
                  'PicUrl'      => '',
                  'Url'         => '',
            );
        } else {
            $i       = 2;
            foreach ($list as $key => $value) {
               $article[$i++] = array(
                  'Title'       => "《".$value['title']."》\n关键词：".$value['keyword']."\n出卷人：".$value['author']."\n总   分：".$value['score']."\n参考数：".$value['views']."次\n简介：".$value['desc']."\n\n点我立即参加考试>>>",
                  'Description' => "",
                  'PicUrl'      => empty($value['logo'])?ADDON_PUBLIC.'exam.jpg':get_cover_pic($value['logo']),
                  'Url'         => $this->create_loginurl('index',array('id'=>$value['id'])),
                );
            }
        }
            return $article;
    }
    public function show_test(){
        if($this->wxmainword=='中心'){
            global $_P;
            $article[0] = array(
                  'Title'       => "微考试-考试中心\n已参加考试",
                  'Description' => "子曰:学而时习之,不亦说乎！\n已参加的考试列表：",
                  'PicUrl'      => ADDON_PUBLIC.'exam.jpg',
                  'Url'         => '',
            );
            $userloglist = M('Addonsexamlog')->where(array('fromusername'=>$_P['fromusername']))->limit(5)->order('addtime DESC')->select();
            if(empty($userloglist)){
                $article[0] = array(
                      'Title'       => "您还未参加任何考试",
                      'Description' => "子曰:学而时习之,不亦说乎！",
                      'PicUrl'      => '',
                      'Url'         => '',
                );
            } else {
                $model = M('Addonsexam');
                foreach ($userloglist as $key => $value) {
                    $testinfo = $model->where(array('id'=>$value['testid']))->field('title,logo')->find();
                    $userloglist[$key]['title'] = $testinfo['title'];
                    $userloglist[$key]['logo'] = $testinfo['logo'];
                }
                $im       = 1;
                foreach ($userloglist as $key => $value) {
                   $article[$im++] = array(
                      'Title'       => "《".$value['title']."》\n分数：".$value['score']."\n时间：".date('Y-m-d h:i:sa',$value['addtime']),
                      'Description' => "子曰:学而时习之,不亦说乎！",
                      'PicUrl'      => empty($value['logo'])?ADDON_PUBLIC.'exam.jpg':get_cover_pic($value['logo']),
                      'Url'         => '',
                    );
                }
            }
                $this->assign('Duotw',$article);
                $this->display();
        }
            $model = M('Addonsexam');
            $fields = 'id,keyword,desc,title,score,author,views,logo';
            $info   = $model->where(array('keyword'=>$this->wxmainword))->field($fields)->find();
            //模糊查询
            if(empty($info)){
                $map['keyword'] = array('like','%'.$this->wxmainword.'%');
                $list    = $model->where($map)->field($fields)->select();
                $article = $this->default_tw($list);
            } else {
               $article[0] = array(
                  'Title'       => "微考试-".$info['title'],
                  'Description' => "出卷人：".$info['author']."\n总   分：".$info['score']."\n参考数：".$info['views']."次\n简介：".$info['desc']."\n\n点我立即参加考试>>>",
                  'PicUrl'      => empty($info['logo'])?ADDON_PUBLIC.'exam.jpg':get_cover_pic($info['logo']),
                  'Url'         => $this->create_loginurl('index',array('id'=>$info['id'])),
                );
            }
                $this->assign('Duotw',$article);
                $this->display();
    }
    public function run(){
        return true;
    }
    //插件展示微信TAG方法
    public function showTips(){
        return '';
    }
}
