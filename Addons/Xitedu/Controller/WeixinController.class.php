<?php

namespace Addons\Xitedu\Controller;
use Common\Controller\Bundle;

/**
 * Xitedu微信处理Bundle
 */
class WeixinController extends Bundle{
    public $_rules = array(
        'COMMON'    => array(
                            '_action'  => null,
                            '_wtype'   => 'content',
                            '/^(周一|周二|周三|周四|周五|周六|周日|周天|今天|明天)/' => array(
                                                '_action'  => 'show_class',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            '/^成绩/' => array(
                                                '_action'  => 'get_score',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            '/^余额/' => array(
                                                '_action'  => 'ykt_model',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            '/^借阅/' => array(
                                                '_action'  => 'ykt_jieyue',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            ),
        'CLICK'    => array(
                            '_action'  => null,
                            '_wtype'   => 'content',
                            '/^(周一|周二|周三|周四|周五|周六|周日|周天|今天|明天)/' => array(
                                                '_action'  => 'show_class',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            '/^成绩/' => array(
                                                '_action'  => 'get_score',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            '/^余额/' => array(
                                                '_action'  => 'ykt_model',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            '/^借阅/' => array(
                                                '_action'  => 'ykt_jieyue',
                                                '_replace' => array(self::W_COMMON,'',array(0,2))
                                            ),
                            )
    );
    //插件微信处理默认入口
	public function index($param){
        $this->success('查询不到哦');
	}

    //课表显示
    public function show_class($param){
        global $_P;
        if(empty($this->wxmainword)){
            //查看是否绑定了学号
            $info = $this->has_xuehao($_P['fromusername']);
            $condition['xuehao'] = $info['xuehao'];
        } else {
            //携带学号
            $condition['xuehao'] = array('like',$this->wxheadword);
        }
        switch ($this->wxheadword) {
            case '今天':
                $todayweek   = date('w');
                $this->assign('Duotw',$this->get_class($todayweek,$condition,'今日',$info['name']));
                break;
            case '明天':
                $tomorrow = date("w",strtotime("+1 day"));
                $this->assign('Duotw',$this->get_class($tomorrow,$condition,'明日',$info['name']));
                break;            
            default:
                $replace_list = array(
                '周一' => '1',
                '周二' => '2',
                '周三' => '3',
                '周四' => '4',
                '周五' => '5',
                '周六' => '6',
                '周日' => '0',
                '周天' => '0',
                 );
                $weekinfo = strtr($this->wxheadword,$replace_list);
                $this->assign('Duotw',$this->get_class($weekinfo,$condition,$this->wxheadword,$info['name']));
                break;
        }
        $this->display();
    }

    //获取考试信息
    public function get_score(){
        global $_P;
        $userinfo = $this->has_xuehao($_P['fromusername']);
            if(empty($userinfo['allscore'])){
                $autolink = $this->create_loginurl('profile');
                $errmsg = "❶.未绑定学号?点击本图文进入绑定\n❷.查课表?发送‘课表’,‘今天’,‘明天’,‘周一’至‘周日’";
                $errurl = array($autolink,'出错');
                $this->error($errmsg,$errurl,'dantw','未绑定学号！点击绑定');
            }
            $article[0] = array(
            //'Title'       => "-{$userinfo['name']}-历史成绩单",
              'Title'       => "{$userinfo['name']} 成绩查询",
              'Description' => "-",
              'PicUrl'      => ADDON_PUBLIC.'img/logo.jpg',
              'Url'         => $this->create_loginurl('scorelist'),
            );
            $cjstr     = '';
            $listscore = unserialize($userinfo['allscore']);

            $nowscore  = end($listscore);
            $itemname = array_search($nowscore, $listscore);
                $cjstr   = '';
                foreach ($nowscore as $k => $v) {
                    
                    $cjstr   .= "\n科目:".$v[2]."  【".strip_tags($v[8])."】";
                }
                $article[1] = array(
                    'Title'       => "=====".$itemname."学年=====".$cjstr,
               //   'Title'       => "Hi~ ".$userinfo['name']." ，这是最新成绩。"."\n\n=======".$itemname."学年=======".$cjstr,
                    'Description' => "-222-",
                    'PicUrl'      => "",
                    'Url'         => $this->create_loginurl('scorelist'),
                );

                $this->assign('Duotw',$article);
                $this->display();
    }

    //获取课表
    public function get_class($todayweek,$condition,$title,$nickname){
                $nickname    = empty($nickname) ? '童鞋' : $nickname;
                $week        = 'week'.$todayweek;
                $todayclass  = M('Addonsclass')->where($condition)->field($week.',itemname,classname')->find();
                if(empty($todayclass)){
                    $autolink = $this->create_loginurl('profile');
                    $errmsg = "❶.未绑定学号?点击本图文进入绑定\n❷.查课表?发送‘课表’,‘今天’,‘明天’,‘周一’至‘周日’";
                    $errurl = array($autolink,'出错');
                    $this->error($errmsg,$errurl,'dantw','查询不到相关课表');
                }
                $today_class = unserialize($todayclass[$week]);
                $article[0] = array(
                  'Title'       => "".$nickname." ".$title."课表",
                  'Description' => "-{$todayclass['itemname']}-",
                  'PicUrl'      => ADDON_PUBLIC.'img/logo.jpg',
                  'Url'         => $this->create_loginurl('myclass'),
                ); 
                $str  = '';
                $str .= "\n-----------上午-----------\n";
                $str .= "①".$this->parax_class($today_class[1]);
                $str .= "②".$this->parax_class($today_class[2]);
                $str .= "\n-----------下午-----------\n";
                $str .= "③".$this->parax_class($today_class[3]);
                $str .= "④".$this->parax_class($today_class[4]);
                $str .= "\n-----------晚上-----------\n";
                $str .= "⑤".$this->parax_class($today_class[5]);

                $article[1] = array(
                //'Title'       => "Hi~ ".$nickname." ，这是".$title."课表。\n".$str,
                  'Title'       => "".$str,
                  'Description' => "-222-",
                  'PicUrl'      => "",
                  'Url'         => $this->create_loginurl('myclass'),
                );
                return $article;
    }
    //解析课表
    public function parax_class($today_class){
        if($today_class=='木有课哦~'){
             return "木有课哦~\n";
        }
        $str = '';
        foreach ($today_class as $key => $value) {
            $str .="科目：".$value[0]."\n  教室：".$value[1]."\n  教师：".$value[2]."\n  周期：[".$value[3]."周]"."\n";
        }
        return $str;
    }
    //实现的微信TAG方法
    public function has_xuehao($fromusername){
        //判断该学生是否已经绑定账号 并同步课表信息
        $userinfo = M('Addonseduinfo')->where(array('fromusername'=>$fromusername))->field('*')->find();
        if(empty($userinfo)){
                $autolink = $this->create_loginurl('profile');
                $errmsg = "❶.未绑定学号?点击本图文进入绑定\n❷.查课表?发送‘课表’,‘今天’,‘明天’,‘周一’至‘周日’";
                $errurl = array($autolink,'出错');
                $this->error($errmsg,$errurl,'dantw','还没绑定学号呢，请先绑定吧~');
        }
             return $userinfo;
    }
    //一卡通
    public function ykt_model(){
        global $_P;
        $info = $this->has_xuehao($_P['fromusername']);
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $Jmuedu = new \Xitlibrary();
        $group = $Jmuedu->ykt_login($info['xuehao'],$info['headimg']);
        if(empty($group['total']['name'])){
            $autolink = $this->create_loginurl('profile');
            $errmsg = "❶.未绑定通行证?点击本图文进入绑定";
            $errurl = array($autolink,'出错');
            $this->error($errmsg,$errurl,'dantw','未绑定学号！点击绑定');
        }
        $article[0] = array(
          'Title'       => $group['total']['name']." 一卡通记录",
          'Description' => "-",
          'PicUrl'      => ADDON_PUBLIC.'img/logo.jpg',
          'Url'         => $this->create_loginurl('balance'),
        );
        $totalnums = count($group['other']);
        if($totalnums<=3){
            $other  = "\n【餐费支出】：".$this->is_nomoney($group['other'][0])."\n【购物支出】：".$this->is_nomoney($group['other'][1])."\n【用电支出】：0\n【其他支出】：".$this->is_nomoney($group['other'][2]);
        } else {
            $other  = "\n【餐费支出】：".$this->is_nomoney($group['other'][0])."\n【购物支出】：".$this->is_nomoney($group['other'][1])."\n【用电支出】：".$this->is_nomoney($group['other'][2])."\n【其他支出】：".$this->is_nomoney($group['other'][3]);
        }
        $article[1] = array(
                    'Title'       => "可用余额 |  ".str_replace('-', '', $group['list'][0][3])."\n累计消费 |  ¥".$group['total']['total']."\n".$other,
                    'Description' => "-222-",
                    'PicUrl'      => "",
                    'Url'         => $this->create_loginurl('balance'),
        );
        $list = $group['list'];
        $str    = '';
        $i = 0;
                    foreach ($list as $key => $value) {
                        if($i>2){
                            break;
                        } else {
                        	$str .= "\n--------------------\n时间：".$value[0]."\n类型：".$value[1]."\n消费：".$value[2]."\n地点：".$value[4];
                        }
                            $i++;
                    }
        if(empty($str)){
           $str = "\n暂无消费记录";
        }
        $article[2] = array(
            'Title'       => "【消费记录】".$str,
            'Description' => "-222-",
            'PicUrl'      => "",
            'Url'         => $this->create_loginurl('balance'),
        );
        $this->assign('Duotw',$article);
        $this->display();
    }
    public function is_nomoney($money){
        $money  = (empty($money)||!is_numeric($money)) ? 0 : $money;
        return $money;
    }
    //图书借阅
    public function ykt_jieyue(){
        global $_P;
        $info = $this->has_xuehao($_P['fromusername']);
        Amango_Addons_Import('Xitlibrary.php','Xitedu');//导入
        $demo = new \Xitlibrary();
        $oldinfo = $demo->library_login($info['xuehao'],$info['headimg']);
        foreach ($oldinfo as $key => $value) {
            $oldinfo[$key] = str_replace(array("\n","\r"), "", $value);
        }
        //判断是否登陆成功
        if(empty($oldinfo[1])){
            $autolink = $this->create_loginurl('profile');
            $errmsg = "❶.未绑定通行证?点击本图文进入绑定";
            $errurl = array($autolink,'出错');
            $this->error($errmsg,$errurl,'dantw','未绑定学号！点击绑定');
        }
                //读取图书借阅情况
                $booklist   = $demo->getovertime();
                if(empty($booklist)){
                    $str    = "\n--------------------\n您暂未借阅任何书籍";
                } else {
                    $str    = '';
                    foreach ($booklist as $key => $value) {
                        $str .= "\n--------------------\n【".($key+1)."】《:".$value[2]."》\n应还日期:".$value[1]."\n借阅日期:".$value[6];
                    }
                }
                $article[0] = array(
                  'Title'       => $info['name']." 图书借阅记录",
                  'Description' => "-",
                  'PicUrl'      => ADDON_PUBLIC.'img/logo.jpg',
                  'Url'         => $this->create_loginurl('borrow'),
                );
                $article[1] = array(
                    'Title'       => "已借图书:".count($booklist)."册".$str,
                    'Description' => "-222-",
                    'PicUrl'      => "",
                  'Url'           => $this->create_loginurl('borrow'),
                );

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
