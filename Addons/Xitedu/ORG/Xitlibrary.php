<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ] 集大微信教务系统
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
class Xitlibrary{
    private $edu_head = 'Library/Public/Stuheadimg/';
    private $infoheader   = array(
               'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language:zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
                 'Host:smjslib.jmu.edu.cn',
         'Content-Type:application/x-www-form-urlencoded',
           'User-Agent:Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
    );
    private $passheader   = array(
               'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language:zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
                 'Host:id.jmu.edu.cn',
         'Content-Type:application/x-www-form-urlencoded',
           'User-Agent:Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
    );
    private $myid   = array(
               'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language:zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
                 'Host:myid.jmu.edu.cn',
         'Content-Type:application/x-www-form-urlencoded',
           'User-Agent:Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
    );
    private $libheader   = array(
               'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language:zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
                 'Host:smjslib.jmu.edu.cn',
         'Content-Type:application/x-www-form-urlencoded',
           'User-Agent:Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
    );
    /**
     * 模拟登陆图书系统 myid.jmu.edu.cn
     */
    public function library_login($user,$pwd) {
        $stim       = time();
        $cookie_jar = dirname(__FILE__)."/".$stim."library.cookie";

        $data['IDToken0']       = '';
        $data['IDToken1']       = $user;
        $data['IDToken2']       = $pwd;
        $data['IDButton']       = 'Submit';
        $data['goto']           = 'aHR0cDovL215aWQuam11LmVkdS5jbi9pZHMvVXNlckNoZWNrLmFzcHg/Z290bz1odHRwOi8vbXlpZC5qbXUuZWR1LmNuL2lkcy9Vc2VyQ2hlY2suYXNweD9nb3RvPWh0dHA6Ly9zbWpzbGliLmptdS5lZHUuY24vbG9naW4uYXNweD9SZXR1cm5Vcmw9L3VzZXIvdXNlcmluZm8uYXNweCZmcm9tSURTPTE=';
        $data['goto_Url']       = 'http://myid.jmu.edu.cn/ids/UserCheck.aspx?goto=http://myid.jmu.edu.cn/ids/UserCheck.aspx?goto=http://smjslib.jmu.edu.cn/login.aspx?ReturnUrl=/user/userinfo.aspx&fromIDS=1';
        $data['encoded']        = 'true';
        $data['inputCode']      = '';
        $data['gx_charset']     = 'UTF-8';

        $post = http_build_query($data);
        //统一登陆地址
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://id.jmu.edu.cn/amserver/UI/Login");
        curl_setopt($ch, CURLOPT_REFERER,'http://id.jmu.edu.cn/amserver/UI/Login?goto=http%3a%2f%2fmyid.jmu.edu.cn%2fids%2fUserCheck.aspx%3fgoto%3dhttp%3a%2f%2fsmjslib.jmu.edu.cn%2flogin.aspx%3fReturnUrl%3d%2fuser%2fuserinfo.aspx%26fromIDS%3d1');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->passheader);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        $result=curl_exec($ch);
        curl_close($ch);
        $_SESSION['library_cookie'] = $cookie_jar;
        //解析个人信息
        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($result);
        $userinfo     = pq("#userInfoContent")->find('span.inforight');
        $list = array();
        foreach($userinfo as $company){
            $item   = pq($company)->text();
            $item   = str_replace("\n", '', $item);
            $item   = str_replace("\r", '', $item);
            $list[] =  str_replace(" ", "", $item); 
        }
        return $list;
    }

    /**
     * 获取个人信息
     */
    public function getUserinfo(){
        //获取个人信息 初始化数据
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/ViewMyInfo.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/Left.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->passheader);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['library_cookie']);
        $ViewMyInfo=curl_exec($ch);
        curl_close($ch);

        //提取个人信息
        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($ViewMyInfo);  
        $userinfo     = pq("#baseInfo td")->html();
        $userinfo     = str_replace(" ", '', $userinfo);
        $userinfo     = str_replace("\n", '', $userinfo);
        $userinfo     = str_replace("<b>学号", '', $userinfo);
        $userinfo     = str_replace("：</b>", '', $userinfo);
        $replace_list = array(
                '姓名'     => '',
                '姓名拼音' => '',
                '性别'     => '',
                '出生'     => '',
                '身份证号' => '',
                '民族'     => '',
                '政治面貌' => '',
                '生源省份' => '',
                '学院'     => '',
                '专业'     => '',
                '班级'     => '',
                '地址'     => '',
                '邮编'     => ''
        );
        $userinfo = strtr($userinfo,$replace_list);
        $infolist = explode('<b>', $userinfo);
        return $infolist;
    }
    //获取成绩
    public function getclass() {
        $data['ctl00_ToolkitScriptManager1_HiddenField'] = '';
        $data['__EVENTTARGET']                           = 'ctl00$ContentPlaceHolder1$pageNumber';
        $data['__EVENTARGUMENT']                         = '';
        $data['__LASTFOCUS']                             = '';
        $data['__VIEWSTATE']                             = '';
        $data['ctl00_ToolkitScriptManager1_HiddenField'] = '/wEPDwULLTEzNDkxMjMzODEPZBYCZg9kFgICBQ9kFgICAw9kFgQCAQ8QZBAVBw/mjInlrablubTlrabmnJ8bMjAxMS0yMDEy5a2m5bm056ys5LiA5a2m5pyfGzIwMTEtMjAxMuWtpuW5tOesrOS6jOWtpuacnxsyMDEyLTIwMTPlrablubTnrKzkuIDlrabmnJ8bMjAxMi0yMDEz5a2m5bm056ys5LqM5a2m5pyfGzIwMTMtMjAxNOWtpuW5tOesrOS4gOWtpuacnxsyMDEzLTIwMTTlrablubTnrKzkuozlrabmnJ8VBwAFMjAxMTEFMjAxMTIFMjAxMjEFMjAxMjIFMjAxMzEFMjAxMzIUKwMHZ2dnZ2dnZxYBZmQCBQ9kFgJmD2QWAmYPZBYMAgEPFgIeBFRleHQFATFkAgQPEGRkFgFmZAIGDxYCHwAFAjY0ZAIIDxYCHwAFATRkAgsPDxYCHgdFbmFibGVkaGRkAg0PDxYCHwFoZGRkqln96pm7cJ/Gthl3OBP46RFpl/0=';
        $data['ctl00$ContentPlaceHolder1$semesterList']  = '';
        $data['ctl00$ContentPlaceHolder1$pageNumber']    = '200';
        $post = http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/ScoreCourse/ScoreAll.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/ScoreCourse/ScoreAll.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->passheader);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['library_cookie']);
        $ScoreAll=curl_exec($ch);
        curl_close($ch);
        //分析成绩列表 1课程ID 2课程名字 3课时 4学分 5类型 6选修状态 7得分状态 8成绩 9绩点
        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($ScoreAll);

        $scorelist     = pq("#ctl00_ContentPlaceHolder1_scoreList tr")->html();
        preg_match_all("/<td>(.*)<\/td>/iUs", $scorelist,$result);
        $allscore     = array_chunk($result[1], 10);unset($allscore[0]);
        $newscorelist = array();
        foreach ($allscore as $key => $value) {
            $scoreid = $value[0];unset($value[0]);
            $newscorelist[$scoreid][] = $value;
        }
            return $newscorelist;
    }
    //获取课表
    public function getscore($default) {
        if(empty($default)||strlen($default)!=5){
            $nowyear = date('Y');$nowmon = date('m');
            $nowyear = (in_array($nowmon,array('01','02','03','04','05','06'))) ? $nowyear-1 : $nowyear;
            $nowmon  = (in_array($nowmon,array('02','03','04','05','06'))) ? '2' : '1';
            $default = $nowyear.$nowmon;
        }

        $classvate = $this->getclassvate();
        $data['ctl00_ToolkitScriptManager1_HiddenField'] = '';
        $data['__EVENTTARGET']                           = 'ctl00$ContentPlaceHolder1$semesterList';
        $data['__EVENTARGUMENT']                         = '';
        $data['__LASTFOCUS']                             = '';
        $data['__VIEWSTATE']                             = $classvate;
        $data['ctl00$ContentPlaceHolder1$semesterList']    = $default;
        $post = http_build_query($data);

        //需要提交当前所在学期
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/ViewMyStuSchedule.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/ViewMyStuSchedule.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->passheader);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['library_cookie']);
        $Class=curl_exec($ch);
        curl_close($ch);
        //分析提取课表  0课程ID 1课表标识 2任课教师 3有效周期
        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($Class);
        //获取课表头部信息
        $userinfo     = pq("#ctl00_ContentPlaceHolder1_lbtitle")->text();
        $userinfo  = str_replace("集美大学", '', $userinfo );
        $userinfo  = str_replace(" 班级课表", '', $userinfo );
        $classbase = explode(" ",$userinfo);

        $classlist = pq("#ctl00_ContentPlaceHolder3_ScheduleTable")->find('td');
        $list = array();
        foreach($classlist as $company){  
            $list[] =  pq($company)->text();  
        }
        $groupclass = array_chunk($list, 8);unset($groupclass[0]);
        $weekclass = array();
        for ($i=1; $i <=7 ; $i++) { 
            foreach ($groupclass as $key => $value) {
                $weekclass[$i][$key] = $value[$i];
            }
        }
        $newallchenji = array();
        foreach ($weekclass as $key => $value) {
            foreach ($value as $k => $v) {
                if(empty($v)||$v==" "){
                    $newallchenji[$key][$k] = "木有课哦~";
                } else {
                    $hangclass = array();
                    $hangclass = explode('★', $v);unset($hangclass[0]);
                    foreach ($hangclass as $e => $a) {
                        $lieclass = array();
                        $a        = str_replace("  ", " ", $a);
                        $lieclass = explode(" ", $a);
                        $newallchenji[$key][$k][$e] = $lieclass;
                        unset($lieclass);
                    }
                        unset($hangclass);
                }
            }
        }
        $newclass['name']  = $classbase;
        $newclass['class'] = $newallchenji;
        return $newclass;
    }

    public function getclassvate(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/ViewMyStuSchedule.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/Left.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->passheader);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['library_cookie']);
        $result=curl_exec($ch);
        curl_close($ch);

        //提取 VIEWSTATE
        $pattern = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/is';
        preg_match_all($pattern, $result, $matches);
        $res = $matches[1][0];
        return $res;
    }

    public function getovertime() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://smjslib.jmu.edu.cn/user/bookborrowed.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://smjslib.jmu.edu.cn/user/bookborrowed.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->libheader);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['library_cookie']);
        $bookList=curl_exec($ch);
        curl_close($ch);
        
        //解析图书借阅信息
        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($bookList);
        $userinfo     = pq("#borrowedcontent table tbody tr")->find('td');
        $list = array();
        foreach($userinfo as $company){  
            $list[] =  pq($company)->text();
        }
        $groupclass = array_chunk($list, 7);
        unlink($_SESSION['library_cookie']);
        return $groupclass;
    }

    /**
     * 模拟一卡通
     */
    public function ykt_login($user,$pwd) {
        $stim       = time();
        $cookie_jar = dirname(__FILE__)."/".$stim."ykt.cookie";

        $data['IDToken0']       = '';
        $data['IDToken1']       = $user;
        $data['IDToken2']       = $pwd;
        $data['IDButton']       = 'Submit';
        $data['goto']           = 'aHR0cDovL215aWQuam11LmVkdS5jbi9pZHMvVXNlckNoZWNrLmFzcHg/Z290bz1odHRwOi8vbXlpZC5qbXUuZWR1LmNuL3lrdC9kZWZhdWx0LmFzcHg/ZnJvbUlEUz0x';
        $data['goto_Url']       = 'http://myid.jmu.edu.cn/ids/UserCheck.aspx?goto=http://myid.jmu.edu.cn/ykt/default.aspx?fromIDS=1';
        $data['encoded']        = 'true';
        $data['inputCode']      = '';
        $data['gx_charset']     = 'UTF-8';

        $post = http_build_query($data);
        //统一登陆地址
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://id.jmu.edu.cn/amserver/UI/Login");
        curl_setopt($ch, CURLOPT_REFERER,'http://id.jmu.edu.cn/amserver/UI/Login?goto=http%3a%2f%2fmyid.jmu.edu.cn%2fykt%2fdefault.aspx%3ffromIDS%3d1');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->passheader);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        $result=curl_exec($ch);
        curl_close($ch);

        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($result);
        $username     = pq("#welcome")->text();


        $examname     = pq("#ctl00_ContentPlaceHolder1_MainCallbackPanel_pageControl_Page0_Overview_AccountsControl_nbAccounts")->find('span');
        $list = array();
        foreach($examname as $company){  
            $list[] =  pq($company)->text();  
        }
        unset($list[0]);unset($list[1]);
        $groupclass = array_chunk($list, 4);
        $num =0;
        foreach ($groupclass as $key => $value) {
            $nowjiage = $this->only_num($value[2]);
            $num      = $num + $nowjiage;
            $other[]  = $nowjiage;
        }
        //总消费记录
        $groupclass['total'] = $num;
        $groupclass['name']  = str_replace('欢迎您, ', '', $username);
        $xiaofeiinfo     = pq("#ctl00_ContentPlaceHolder1_MainCallbackPanel_pageControl_Page0_Overview_navBar_GCTC1_RecentlyView_RecentlyRecGrid_DXMainTable")->find('td');
        $xiaofeilist = array();
        foreach($xiaofeiinfo as $company){  
            $xiaofeilist[] =  pq($company)->text();  
        }
        for ($i=0; $i <=14 ; $i++) { 
            unset($xiaofeilist[$i]);
        }
            $groupinfo = array_chunk($xiaofeilist, 5);
            unlink($cookie_jar);
            return array('total'=>$groupclass,'list'=>$groupinfo,'other'=>$other);
    }
    function only_num($str){
        $str = str_replace('-¥', '', $str);
        $str = str_replace(',', '', $str);
        return $str;
    }
    public function delcookie() {
        $picpath   = $_SESSION['library_cookie'];
        $param     = explode('/', $picpath);
        $educookie = end($param);
        //删除cookie
        unlink('./Addons/Xitedu/ORG/'.$educookie);
        session('library_cookie','');
        return true;
    }
}