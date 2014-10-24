<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ] 集大微信教务系统
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
class Xitedu{
    private $edu_tmp  = 'Xitedu/Tmp/codepic/';
    private $edu_head = 'Xitedu/Public/Stuheadimg/';
    private $header   = array(
               'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language:zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
                 'Host:jwgl3.jmu.edu.cn',
         'Content-Type:application/x-www-form-urlencoded'
    );
    /**
     * 抓取远程验证码 保存携带COOKIE
     * cookie数据存在 session  (edu_cookie) (edu_picnum)
     */
    public function getVail() {
        $stim       = time();
        $cookie_jar = dirname(__FILE__)."/".$stim.".cookie";
        //获取页面cookie
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        //保存验证码和cookie
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://jwgl3.jmu.edu.cn/Common/CheckCode.aspx');
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/');
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
                $stim       = time();
                $_SESSION['edu_cookie'] = $cookie_jar;
                $_SESSION['edu_picnum'] = $stim;
        //获取验证码图片
        return $ret;
    }

    public function getVIEWSTATE(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/login.aspx");
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
        $result=curl_exec($ch);
        curl_close($ch);
        //提取 VIEWSTATE
        $pattern = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/is';
        preg_match_all($pattern, $result, $matches);
        $res = $matches[1][0];
        return $res;
    }
    /**
     * 主登陆函数
     */
    public function postLogin($xuehao,$password,$valpic) {
        //获取__VIEWSTATE
        $VIEWSTATE = $this->getVIEWSTATE();
        $data['__LASTFOCUS']     = '';
        $data['__VIEWSTATE']     = $VIEWSTATE;
        $data['__EVENTTARGET']   = '';
        $data['__EVENTARGUMENT'] = '';
        $data['TxtUserName']     = $xuehao;
        $data['TxtPassword']     = $password;
        $data['TxtVerifCode']    = $valpic;
        $data['BtnLoginImage.x'] = '33';
        $data['BtnLoginImage.y'] = '14';
        $post = http_build_query($data);
   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/login.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
        $result=curl_exec($ch);
        curl_close($ch);
        if(stristr($result, '/Student/default.aspx')===false){
            return false;
        } else {
            $picpath = ONETHINK_ADDON_PATH.$this->edu_tmp.$_SESSION['edu_picnum'].'.jpg';
            //删除验证码图片
            unlink($picpath);
            return true;
        }
    }
    /**
     * 获取个人信息
     */
    public function getUserinfo(){
        //获取个人信息 初始化数据
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/ViewMyInfo.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/Left.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
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
    /**
     * 获取个人照片 
     * id 用户学号
     */
    public function getUserpic($id){
        //头像储存地址
        $headUpload  = ONETHINK_ADDON_PATH.$this->edu_head.$id.'.jpg';
        //判断用户头像是否存在
        if(file_exists($headUpload)){
             return $headUpload;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Common/Photo.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/ViewMyInfo.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
        $Photo=curl_exec($ch);
        curl_close($ch);
        
        header('Content-type: image/jpg');
                    $write_fd   = fopen($headUpload,"w");
                    fwrite($write_fd,$Photo);  //将采集来的远程数据写入本地文件
                    fclose($write_fd);
        header("Content-type:text/html;charset =utf-8"); 
        return $headUpload;
    }

    public function getclassvate(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/ViewMyStuSchedule.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/Left.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
        $result=curl_exec($ch);
        curl_close($ch);

        //提取 VIEWSTATE
        $pattern = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/is';
        preg_match_all($pattern, $result, $matches);
        $res = $matches[1][0];
        return $res;
    }

    /**
     * 获取用户 学年课表 
     * default 默认学期
     */
    public function getUserclass($default=''){
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
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
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

        $classlist = pq("#ctl00_ContentPlaceHolder3_ScheduleTable td")->find('font');
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
    /**
     * 获取用户 全学年成绩 
     */
    public function getUserscore(){
        //需要提交当前所在学期
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
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
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
    /**
     * 获取用户 等级考试 
     */
    public function getUserexam(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://jwgl3.jmu.edu.cn/Student/RegistExam/MyExamHistory.aspx");
        curl_setopt($ch, CURLOPT_REFERER,'http://jwgl3.jmu.edu.cn/Student/Left.aspx');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $_SESSION['edu_cookie']);
        $MyExamHistory=curl_exec($ch);
        curl_close($ch);
        
        //分析用户考证成绩
        Vendor('phpQuery');
        phpQuery::$defaultCharset = 'UTF-8';
        phpQuery::newDocumentHTML($MyExamHistory);
        $examname     = pq("#ctl00_ContentPlaceHolder1_GridView1 td")->find('span');
        $list = array();
        foreach($examname as $company){  
            $list[] =  pq($company)->text();  
        }
        $grouplist    = array_chunk($list, 6);
        $newgrouplist = array();
        foreach ($grouplist as $key  => $value) {
            $newgrouplist[$value[0]] = $value;
        }
        return $newgrouplist;
    }
    public function delcookie() {
        $picpath = $_SESSION['edu_cookie'];
        //删除cookie
        unlink($picpath);
        session('edu_cookie','');
        session('edu_picnum','');
        return true;
    }
}