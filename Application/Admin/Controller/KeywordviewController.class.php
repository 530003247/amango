<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台配置控制器
 * @author ChenDenlu <530003247@vip.qq.com>
 */
class KeywordviewController extends AdminController {
    
    /* 保存允许访问的公共方法 */
    static protected $allow = array( 'addkeyword');
    /**
     * 关键词添加界面
     * 唯一传入量   方便以后将此方法划归系统
     * @return none
     */
    public function keyword_posts($amangoajax){
                $res['status'] = 0;                
            //关键词头部信息初始化
            if (!empty($amangoajax['keyword_post'])){
                //请求类型判断
                $date['keyword_post'] = $amangoajax['keyword_post'];

                //下文关键词是否开启
                $date['keyword_down']  = ($amangoajax['keyword_down']==1) ? 1 : 0;
                //上文继承关键词
                $date['keyword_top']   = is_numeric($amangoajax['keyword_top']) ? $amangoajax['keyword_top'] : 0;
                //关键词所属分组
                $date['keyword_group'] = empty($amangoajax['keyword_group']) ? 1 : $amangoajax['keyword_group'];  
                //关键词缓存
                $date['keyword_cache'] = is_numeric($amangoajax['keyword_cache']) ? $amangoajax['keyword_cache'] : 0; 
                //关键词有效期判断
                $date['keyword_start'] = strtotime($amangoajax['keyword_start']);
                $date['keyword_end']   = strtotime($amangoajax['keyword_end']);

                    if(!empty($date['keyword_end'])&&!empty($date['keyword_start'])){
                            if($date['keyword_end']<$date['keyword_start']){
                                $date['keyword_end']   = $date['keyword_start']+31536000;
                            }
                    }
            //关键词后置行为初始化
            //标签隐藏 显示 关闭
            $date['denytag_keyword']   = serialize(parse_config($amangoajax['denytag_keyword'],$type=3));
            //后续行为初始化
            $date['after_keyword']     = serialize(parse_config($amangoajax['after_keyword'],$type=3));
            //菜单模式初始化
            $date['click_model']       = is_numeric($amangoajax['click_model']) ? $amangoajax['click_model'] : '';
            //模式锁定初始化
            //$date['lock_model']        = empty($amangoajax['lock_model']) ? $amangoajax['lock_model'] : '';
            //模式锁定初始化
            if(empty($amangoajax['lock_model1'])){
                //直接  第一个 选择模块
                if(is_numeric($amangoajax['lock_model'])){
                    $modelinfo = M('Addons')->where(array('status'=>1,'weixin'=>1,'id'=>$amangoajax['lock_model']))->field('id,name')->find();
                    $date['lock_model'] = ucfirst($modelinfo['name']).'/index';
                } else {
                    $date['lock_model'] = '';
                }
            } else {
            	    $date['lock_model']     = empty($amangoajax['lock_model1']) ? '' : $amangoajax['lock_model1'];
            }

            $date['sort']              = time();
            $date['status']            = 1;
            $date['keyword_reply']     = $amangoajax['keyword_reply'];
            //关键词正则替代
            //正则标识
            if(empty($amangoajax['keyword_reply'])){
                $this->ajaxReturn(array('status' => 0, 'errmsg'=>'匹配规则不能为空'),'JSON');
            }
                $rule = M('Rules')->where(array('rules_title' => $amangoajax['keyword_reply'], 'status'=>1))->getField('rules_content');

                //判断匹配正则是否可用
                if(empty($rule)){
                    $this->ajaxReturn(array('status' => 0, 'errmsg'=>'该匹配规则为空'),'JSON');
                }
                
                    $date['keyword_content']   = keyword_replace_text($amangoajax['keyword']);

                    //强制性  1文本请求  关键词必填   2菜单请求  关键词必填
                    if (strtolower($date['keyword_post'])=='text'&&empty($date['keyword_content'])){
                        $this->ajaxReturn(array('status' => 0, 'errmsg'=>'文本请求中，关键词不能为空'),'JSON');
                    }

                    if (!empty($date['keyword_content'])&&!empty($rule)){

                        //判断是否携带【参数1】【参数2】【参数3】【参数4】
                        if(strstr($rule, "参数")){
                            $newkey = $rule;
                            $replacekey = explode(';', $date['keyword_content']);
                            foreach ($replacekey as $key => $value) {
                                $newkey = str_ireplace("参数".$key,$value,$newkey);
                            }
                            $newrule = "/".str_replace("/", "\/", $newkey)."/";//循环替代参数
                        }
                        //判断是否携带单规则替代
                        if(strstr($rule, "芒果")){
                            $newrule = str_ireplace("芒果",str_replace("/", "\/", $date['keyword_content']),$rule);//芒果基本关键词
                        }
                        //全无则原规则
                        if(!strstr($rule, "参数")&&!strstr($rule, "芒果")){
                            $newrule = $rule;
                        }
                           $date['keyword_rules'] = $newrule;
                    }
                            return $date;
            } else {
                   $this->ajaxReturn(array('status' => 0, 'errmsg'=>'用户请求标识非法'),'JSON');
            }
    }

    public function addkeyword(){
        if(IS_AJAX){
            //读取数据模型列表
            $res['status'] = 0;
            switch (I('post.creattype','')) {
                case 'post':
                    $data = self::keyword_posts(I('post.'));
                    $datanum = M('Keyword')->add($data);
                    $res['errmsg'] = ($datanum>0) ? '添加用户请求成功！' : '添加用户请求失败！';
                    $this->ajaxReturn($res,'JSON');
                    break;
                case 'response':
                    $data = Factory(I('post.replytype',''))->run();
                    if(false===$data){
                        $this->ajaxReturn(array('status' => 0, 'errmsg'=>'创建工厂生成数据失败'),'JSON');
                    }
                    //新增  OR  修改
                    if(is_numeric(I('editid'))&&I('editid')>0){
                        $datanum = M('Response')->where(array('id'=>I('editid')))->save($data);
                        $res['errmsg'] = (false===$datanum) ? '编辑响应体失败！' : '编辑响应体成功！';
                    } else {
                        $datanum = M('Response')->add($data);
                        $res['errmsg'] = ($datanum>0) ? '添加响应体成功！' : '添加响应体失败！';
                    }
                    $this->ajaxReturn($res,'JSON');
                    break;
                default:
                    $response = Factory(I('post.replytype',''))->run();
                    if(false===$response){
                        $this->ajaxReturn(array('status' => 0, 'errmsg'=>'创建工厂生成数据失败'),'JSON');
                    }
                    $data     = self::keyword_posts(I('post.'));
                    //添加数据
                    $datanum  = M('Keyword')->add($data);$responsenum = M('Response')->add($response);
                    //暂时采用非关联操作
                    if($datanum>0&&$responsenum>0){
                        M('Keyword')->where(array('id' => $datanum))->save(array('keyword_reaponse' => $responsenum));
                    }
                        $this->ajaxReturn(array('status' => 1, 'msg'=>'创建关键词组合成功！'),'JSON');
                    break;
            }
            die;
        }else{
            $tree = D('Category')->getTree(0,'id,title,pid,status');
            $cate_list = array();
            foreach ($tree as $key => $val) {
                if ($val['pid']==0&&$val['status']==1) {
                    $cate_list['parent'][$val['id']] = $val;
                }
                if(is_array($val['_'])){
                    foreach ($val['_'] as $v) {
                        if($v['status']==1){
                            $cate_list['sub'][$val['id']][] = $v;
                        }
                    }
                    unset($cate_list['parent'][$val['id']]['_']);
                }
            }
            $locallist = M('Addons')->where(array('status'=>1,'weixin'=>1))->field('id,title,name,description,config')->select();
            foreach ($locallist as $key => $value) {
                $locallist[$key]['description'] = msubstr($value['description'], 0, 25, $charset="utf-8", $suffix=true);
            }
            $clickmenu = M('Clickmenu')->field('id,title')->select();
            $this->assign('localapi',$locallist);
            $this->assign('clickmenu',$clickmenu);
            $this->assign('cate_list',$cate_list);
            $this->assign('meta_title','关键词管理');
            //TODO  暂时关闭 任意模块读取   仅限图文读取
            //$this->assign('parentlist',get_flycloud_list('local'));
            $this->display();
        }
    }
    public function lists(){
    	$condition = array();
        //获取读取条件  分组
        if(isset($_GET['groupid'])){
            $condition['keyword_group']  = $_GET['groupid'];
        }
        //获取读取条件  标题
        if(isset($_GET['title'])){
            $condition['keyword_content'] = array('like','%'.$_GET['title'].'%');
            $condition['keyword_rules']   = array('like','%'.$_GET['title'].'%');
        }
        //获取读取条件  关键词状态
        if(isset($_GET['keyword_status'])){
            $condition['status']  = $_GET['keyword_status'];
        }
        //获取读取条件  请求类型
        if(isset($_GET['request'])){
            $condition['keyword_post']  = $_GET['request'];
        }
        if(!empty($condition)){
            $condition['_logic']  = 'and';
        }
        $model = D('KeywordView');
        $total = $model->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 5;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->where($condition)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('_page',$page->show());
        $this->assign('list',$list);
        //获取关键词分组信息
        $catemodel = M('Keywordcate');
        $Keywordcatelist = $catemodel->select();
        $this->assign('catelist',$Keywordcatelist);
        //获取请求组信息
        $Postslist = M('Posts')->select();
        $this->assign('postlist',$Postslist);
        
        $catename = $catemodel->where(array('id'=>$_GET['groupid']))->getField('keywordcate_name');
        $this->assign('meta_title','关键词管理');
        $this->assign('search_title',empty($_GET['title']) ? '' : $_GET['title']);
        $this->assign('search_groupid',empty($_GET['groupid']) ? '' : $_GET['groupid']);
        $this->assign('search_groupname',empty($catename) ? '所有分' : $catename);
        $this->display();
    }
    /**
     * 关键词模型 拖动管理
     * @return none
     */
    public function add(){
        if(IS_POST){
            $postslist     = $_POST['field_sort'][2];
            $responselist  = $_POST['field_sort'][3];
            if(empty($_POST['field_sort'][2])||empty($_POST['field_sort'][3])||count($postslist)!=count($responselist)){
                $this->error('请求规则和响应体，匹配组合时候必须一一对应！');
            } else{
                $keywordmodel  = M('Keyword');
                foreach ($postslist as $key => $value) {
                    $keywordmodel->where(array('id' => $value))->save(array('keyword_reaponse' => $responselist[$key]));
                }
                $this->success('新增关键词组合成功！',U('Keywordview/lists'));
            }
        } else {
            $Responselist = M('Response')->where(array('status' => 1))->field('id,response_name,response_reply')->select();
            $Postslist    = M('Keyword')->where(array('status' => 1))->field('keyword_rules,id,keyword_post')->select();
            $this->assign('Responselist',$Responselist);
            $this->assign('Postslist',$Postslist);
            $this->assign('meta_title','关键词管理');
            $this->display();
        }
    }
    //回复体编辑
    public function edit_response(){
        //获取内容分类
            $tree = D('Category')->getTree(0,'id,title,pid,status');
            $cate_list = array();
            foreach ($tree as $key => $val) {
                if ($val['pid']==0&&$val['status']==1) {
                    $cate_list['parent'][$val['id']] = $val;
                }
                if(is_array($val['_'])){
                    foreach ($val['_'] as $v) {
                        if($v['status']==1){
                            $cate_list['sub'][$val['id']][] = $v;
                        }
                    }
                    unset($cate_list['parent'][$val['id']]['_']);
                }
            }

        $id = I('id');
        $responseinfo = M('Response')->where(array('id'=>$id))->find();
        $edit_model   = ucfirst($responseinfo['response_reply']);
        //模板赋值
        $staticinfo = Factory($edit_model)->edit($responseinfo);
        $this->assign($edit_model,$staticinfo);
        $this->assign('meta_title','关键词管理');
        $this->assign('responseid',$id);
        $this->assign('info',$responseinfo);
        $this->assign('type',$responseinfo['response_reply']);
        $this->assign('cate_list',$cate_list);
        $this->display();
    }
    /**
     * 响应列表
     * @return none
     */
    public function responselists(){
        $model = D('Response');
        $total = $model->count();
        $listRows = 10;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('_page',$page->show());
        $this->assign('list',$list);
        $this->assign('meta_title','关键词管理');
        $this->display();
    }

    /**
     * 根据父级菜单  读取下级资源
     * @param cateid 父级资源ID   cate 目标资源ID
     */
    public function ajax_list(){
        if(IS_AJAX){
            //TODO   暂时使用资讯列表
            $lists = api('Category/get_category_list',array('cateid'=>I('cateid',0),'field'=>'l.id,l.title','order'=>'l.level DESC,l.id DESC'));
            $readfields = D('Flycloud')->where(array('data_type' => 'category', 'data_table'=>$_POST['cateid']))->find();
            if(empty($readfields)){
                $this->ajaxReturn(array('status'=>0,'errmsg'=>"该分类下为绑定数据模型,无法读取详细信息,为了避免生成空图文\n请在云端中添加读取模型\n===步骤=====\n【数据聚合】->【本地模型】->【新增】\n===填写参数格式===\n模型名称:调用【当前分类的名称】\n类型:图文数据模型\n数据表名为:当前分类的名字"),'JSON');
            }
            // $options  = get_category(I('cateid',0));
            $newlist = array();
            foreach ($lists as $key => $value) {
                    $newlist[$key][] = $value['id'];
                    $newlist[$key][] = I('cateid');
                    $newlist[$key][] = $value['title'];
            }
            $data['status'] = 1;
            $data['msg']    = $newlist;
            $this->ajaxReturn($data,'JSON');
        }
    }
    /**
     * 根据Api获取参数
     */
    public function ajax_apiparam(){
        if(IS_AJAX){
            $addonstype = I('cate',0);
            $addonsid   = I('cateid',0);
            if(!in_array($addonstype, array('local','cloud'))){
                $data['status'] = 0;
                $data['errmsg'] = '插件不存在';
                $this->ajaxReturn($data,'JSON');
            } else {
                if ($addonstype=='local') {
                    $addon_name = M('Addons')->where(array('id'=>$addonsid))->getField('name');
                    $class          =   get_addon_class($addon_name);
                    if(!class_exists($class)){
                        $data['status'] = 0;
                        $data['errmsg'] = '插件不存在';
                        $this->ajaxReturn($data,'JSON');
                    }
                    $addons    =   new $class;
                    $paramlist = $addons->info['weixinkeyword']['option'];
                    $newlist   = array();
                    foreach ($paramlist as $key => $value) {
                        $newlist[] = array(
                           '0'=>$key,
                           '2'=>$key.'【'.$value.'】',
                        );
                    }
                    $data['status'] = 1;
                    $data['msg']    = $newlist;
                    $this->ajaxReturn($data,'JSON');
                }
            }
        }
    }
    /**
     * 根据资源ID读取 字段信息
     * @param cateid 父级资源ID   cate 目标资源ID   id单项ID
     */
    public function ajax_info(){
        if(IS_AJAX){
            if(!in_array($_POST['id'],array('news','rand'))){
                $data['status'] = 1;
                //TODO  暂时仅限于  调用分类资讯
                // $data['msg'] = get_infoByid($_POST['cateid'],$_POST['id']);
                $readfields = D('Flycloud')->where(array('data_type' => 'category', 'status'=>1, 'data_table'=>$_POST['cateid']))->find();
                $id         =   $_POST['id'];
                if(empty($readfields)){
	                $this->ajaxReturn(array('status'=>0,'errmsg'=>"该分类下为绑定数据模型,无法读取详细信息,为了避免生成空图文\n请在云端中添加读取模型\n===步骤=====\n【数据聚合】->【本地模型】->【新增】\n===填写参数格式===\n模型名称:调用【当前分类的名称】\n类型:图文数据模型\n数据表名为:当前分类的名字"),'JSON');
                }
                if(empty($id)){
                    $this->ajaxReturn(array('status'=>0,'errmsg'=>'帖子参数不能为空,否则无法生成完整的图文信息'),'JSON');
                }
                $fields     = explode(',', $readfields['data_fields']);
                /*获取一条记录的详细数据*/
                $Document = D('Document');
                $data = $Document->detail($id);
                foreach ($fields as $value) {
                    $newdata[] = $data[$value];
                }
                    $url        = is_numeric($newdata[1]) ? D('Picture')->where(array('id' => $newdata[1]))->getField('path') : $newdata[1];
                    //false 找不到
                    $newdata[1] = (false===stripos($url, 'http://')) ? HEAD_URL.__ROOT__.$url : $url;
                    $newdata[3] = Amango_U('Article/detail?id='.$id);
                $data['msg']    = $newdata;
                $this->ajaxReturn($data,'JSON');
            }
        }
    }

    /**
     * Api行为列表
     */
    public function ajax_apilist(){
        if(IS_AJAX){
            switch ($_POST['modeltype']) {
                case 'local':
                    //$this->ajaxReturn(array('status'=>0,'errmsg'=>'暂不支持本地插件'),'JSON');
                    $locallist = M('Addons')->where(array('status'=>1,'weixin'=>1))->field('id,name,title,description')->select();
                    foreach ($locallist as $key => $value) {
                        $locallist[$key]['description'] = msubstr($value['description'], 0, 14, $charset="utf-8", $suffix=true);
                    }
                    $newlist   = array();
                    foreach ($locallist as $key => $value) {
                        $newlist[$key] = array_values($value);
                    }
                    $this->ajaxReturn(array('status'=>1,'msg'=>$newlist,'type'=>'local'),'JSON');
                    break;
                case 'cloud':
                    $cloudlist = M('webuntil')->where(array('status'=>1))->field('id,webuntil_type,webuntil_name,webuntil_backtype,webuntil_title')->select();
                    $newlist   = array();
                    foreach ($cloudlist as $key => $value) {
                        $newlist[$key] = array_values($value);
                    }
                    $this->ajaxReturn(array('status'=>1,'msg'=>$newlist,'type'=>'cloud'),'JSON');
                    break;
                case 'behavior':
                    $this->ajaxReturn(array('status'=>0,'errmsg'=>'行为监听将在下个版本完善，暂不支持使用'),'JSON');
                    break;
            }
        }
    }

    /**
     * 正则匹配类型首页
     * @return none
     */
    public function rules_lists(){
        redirect(U('Think/lists?model=rules'));
    }

    /**
     * 编辑配置
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function edit_posts($id = 0){
        if(IS_POST){
                $res['status'] = 0;                
            //关键词头部信息初始化
            if (!empty($_POST['keyword_post'])&&!empty($_POST['id'])){
                //请求类型判断
                $date['keyword_post'] = $_POST['keyword_post'];

                //下文关键词是否开启
                $date['keyword_down']  = !empty($_POST['keyword_down']) ? $_POST['keyword_down'] : '';
                //上文继承关键词
                $date['keyword_top']   = is_numeric($_POST['keyword_top']) ? $_POST['keyword_top'] : 0;
                //关键词所属分组
                $date['keyword_group'] = empty($_POST['keyword_group']) ? 1 : $_POST['keyword_group'];  
                //关键词缓存
                $date['keyword_cache'] = is_numeric($_POST['keyword_cache']) ? $_POST['keyword_cache'] : 0; 
                //关键词有效期判断
                $date['keyword_start'] = strtotime($_POST['keyword_start']);
                $date['keyword_end']   = strtotime($_POST['keyword_end']);

                    if(!empty($date['keyword_end'])&&!empty($date['keyword_start'])){
                            if($date['keyword_end']<$date['keyword_start']){
                                $date['keyword_end']   = $date['keyword_start']+31536000;
                            }
                    }
            //关键词后置行为初始化
            //标签隐藏 显示 关闭
            $date['denytag_keyword']   = serialize(parse_config($_POST['denytag_keyword'],$type=3));
            //后续行为初始化
            $date['after_keyword']     = serialize(parse_config($_POST['after_keyword'],$type=3));
            //菜单模式初始化
            $date['click_model']       = is_numeric($_POST['click_model']) ? $_POST['click_model'] : '';
            //模式锁定初始化
            if(empty($_POST['lock_model1'])){
                //直接  第一个 选择模块
                if(is_numeric($_POST['lock_model'])){
                    $modelinfo = M('Addons')->where(array('status'=>1,'weixin'=>1,'id'=>$_POST['lock_model']))->field('id,name')->find();
                    $date['lock_model'] = ucfirst($modelinfo['name']).'/index';
                } else {
                    $date['lock_model'] = '';
                }
            } else {
            	    $date['lock_model'] = empty($_POST['lock_model']) ? '' : $_POST['lock_model1'];
            }
            $date['status']            = 1;
            $date['keyword_reply']     = $_POST['keyword_reply'];
            //关键词正则替代
            //正则标识
            if(empty($_POST['keyword_reply'])){
                $this->error('匹配规则不能为空');
            }
               $rule = M('Rules')->where(array('rules_title' => $_POST['keyword_reply'], 'status'=>1))->getField('rules_content');
                //判断匹配正则是否可用
                if(empty($rule)){
                    $this->error('该匹配规则为空');
                }
                    $date['keyword_content']   = keyword_replace_text($_POST['keyword']);
                    $date['keyword_content'] = str_replace('/', '\\/', $date['keyword_content']);
                    //强制性  1文本请求  关键词必填   2菜单请求  关键词必填
                    if (strtolower($date['keyword_post'])=='text'&&empty($date['keyword_content'])){
                        $this->error('文本请求中，关键词不能为空');
                    }
                    if (!empty($date['keyword_content'])&&!empty($rule)){

                        //判断是否携带【参数1】【参数2】【参数3】【参数4】
                        if(strstr($rule, "参数")){
                            $newkey = $rule;
                            $replacekey = explode(';', $date['keyword_content']);
                            foreach ($replacekey as $key => $value) {
                                $newkey = str_ireplace("参数".$key,$value,$newkey);
                            }
                            $newrule = "/".$newkey."/";//循环替代参数
                        }
                        //判断是否携带单规则替代
                        if(strstr($rule, "芒果")){
                            $newrule = str_ireplace("芒果",$date['keyword_content'],$rule);//芒果基本关键词
                        }
                        //全无则原规则
                        if(!strstr($rule, "参数")&&!strstr($rule, "芒果")){
                            $newrule = $rule;
                        }
                           $date['keyword_rules'] = $newrule;
                    }
                    $datanum = M('Keyword')->where(array('id' => $_POST['id']))->save($date);
                    $this->success('编辑用户请求成功',U('Keywordview/lists'));
            } else {
                    $this->error('用户请求标识非法');
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = D('KeywordView')->where(array('id' => intval($_REQUEST['id'])))->find();
            $info['response_reply']  = strtolower($info['response_reply']);
            $string = '';
            foreach (unserialize($info['after_keyword']) as $key =>$value) {
                if(!empty($value)){
                     $string .= $value."\n";
                }
            }
            $info['after_keyword'] = $string;
            $clickmenu             = M('Clickmenu')->field('id,title')->select();
            $this->assign('clickmenu',$clickmenu);
            $locallist             = M('Addons')->where(array('status'=>1,'weixin'=>1))->field('id,title,name')->select();
            $info['lock_model1']   = is_numeric($info['lock_model']) ? '' : $info['lock_model'];
            $this->assign('localapi',$locallist);
            $info['click_model']   = str_replace(' ', '', $info['click_model']);
            $this->assign('meta_title','关键词管理');
            $this->assign('info', $info);
            $this->display();
        }
    }
    /**
     * 删除关键词
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的关键词组合!');
        }
        $map = array('id' => array('in', $id) );
        if(M('Keyword')->where($map)->delete()){
            action_log('del_keyword', 'Keywordview', $id, UID);
            $this->success('关键词组合删除成功');
        } else {
            $this->error('关键词组合删除失败！');
        }
    }

    /**
     * 删除微信回复体
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function del_response(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的关键词响应体!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Response')->where($map)->delete()){
            action_log('del_response', 'Keywordview', $id, UID);
            $this->success('关键词响应体删除成功');
        } else {
            $this->error('关键词响应体删除失败！');
        }
    }

    public function sort($model = null, $id = 0){
          $mod    = M('Keyword');
          $mapid  = array('id' => intval($_REQUEST['id']));
          $num    = trim($_REQUEST['num']);
            if(is_numeric($num)){
                $data['sort'] = $num;
                $result = $mod->where($mapid)->save(array('sort' =>$num));
            }
                $values = $mod->where($mapid)->find();
                $this->success($values[$type]);
    }
    public function status($id = 0){
        $mod    = M('Keyword');
        $id     = $_REQUEST['id'];
        $sql    = "update ".C('DB_PREFIX')."keyword set status=(status+1)%2 where id='$id'";
        $res    = $mod->execute($sql);
        if($res!==false ) {
            $this->success('排序操作成功！');
        }else{
            $this->error('排序操作失败！');
        }
    }
    /* 默认回复设置 */
    public function default_reply(){
        if(IS_AJAX){
            if(!is_numeric($_POST['subscribe'])||!is_numeric($_POST['black'])||!is_numeric($_POST['overtime'])){
                $this->error('非法响应体ID！');
            }
            $data['value'] = implode(',',$_POST);
            M('Config')->where(array('name'=>'AMANGO_DEFAULT_REPLY'))->save($data);
            $this->success('默认响应体设置成功！');
        } else {
            $data   = M('Config')->where(array('name'=>'AMANGO_DEFAULT_REPLY'))->field('value')->find();
            $fields = explode(',', $data['value']);
            $list = M('Response')->select();
            $this->assign('list',$list);
            $this->assign('data',$fields);
            $this->assign('meta_title','关键词管理');
            $this->display();
        }
    }
    /* 自定义菜单添加 */
    public function click_add(){
        if(IS_AJAX){
        	//获取按钮格式
        	$sqlmenu  = array();
        	$submenu  = array();
        	//判断主菜单数量
        	$mainmenu = count($_POST['data']);
        	if($mainmenu>3||$mainmenu==0){
                $this->error('主菜单数量必须为1至3个');
        	}
	    	//菜单模型标识  左|中|右
	    	$menumodel = array(
	               '0' => 'LEFT',
	               '1' => 'CENTER',
	               '2' => 'RIGHT',
	    	);
            //去除没设置的菜单
            foreach ($_POST['data'] as $key => $value) {
                if($value['typ']=='res_ejcd'){
                        if(is_array($value['subdata'])){
                            foreach ($value['subdata'] as $k => $v) {
                                if(empty($v['typ'])){
                                    unset($_POST['data'][$key]['subdata'][$k]);
                                }
                            }
                        } 
                        if(empty($value['subdata'])){
                            unset($_POST['data'][$key]);
                        }
                }
                if(empty($value['tit'])||in_array($value['tit'], array('主菜单一','主菜单二','主菜单三'))){
                        unset($_POST['data'][$key]);
                }
            }
            
        	foreach ($_POST['data'] as $key => $value) {
        		//菜单类型
        		$getmenu  = array();
        		$type = $value['typ'];
        		$name = $value['tit'];
        		switch ($type) {
        			case 'res_ejcd':
	        			$subvalue = array_values($value['subdata']);
        				foreach ($subvalue as $k => $v) {
        					//子菜单key
	        			    $subtitle  = $menumodel[$key].$k;
	        			    $getmenu   = $this->setWeixinmenu($v['typ'],$subtitle,$v['tit'],$v,$key);
			        		$submainmenu[] = $getmenu['menu'];
			        		                       
			        		if(!empty($getmenu['sqlmenu'])){
			        			if($v['typ']=='res_url'){
			        				$urldata   = explode(':', $v['data']);
                                    $sqlmenu['URL'.$urldata[0]]  = $getmenu['sqlmenu'];
                                    unset($urldata);
			        			} else {
                                    $sqlmenu[$subtitle]  = $getmenu['sqlmenu'];
			        			}
			        		}
        				}
        				$submenu[] = array(
                                  'name' => urlencode($name),
                            'sub_button' => $submainmenu
		        	    );
                        unset($submainmenu);
        				break;
        			case 'res_gjz':
        			    $subtitle  = $menumodel[$key];
        			    $getmenu   = $this->setWeixinmenu($type,$subtitle,$name,$value,$key);
		        		$submenu[]          = $getmenu['menu'];
		        		if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu[$subtitle]  = $getmenu['sqlmenu'];
			        		}
        				break;
        			case 'res_url'://组装菜单 URL
		        	    $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['URL'.$urldata[0]]  = $getmenu['sqlmenu'];
			            }
		        	    unset($urldata);
        				break;
                    case 'res_scanpush'://组装菜单扫描
                        $subtitle  = $menumodel[$key];
                        $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['SCANPUSH'.$urldata[1]]  = $urldata[1];
                        }
                        unset($urldata);
                        break;
                    case 'res_scanmsg'://组装菜单扫描提示
                        $subtitle  = $menumodel[$key];
                        $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['SCANMSG'.$urldata[1]]  = $urldata[1];
                        }
                        unset($urldata);
                        break;
                    case 'res_photosys'://组装菜单扫描提示
                        $subtitle  = $menumodel[$key];
                        $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['PHOTOSYS'.$urldata[1]]  = $urldata[1];
                        }
                        unset($urldata);
                        break;
                    case 'res_photoall'://组装菜单扫描提示
                        $subtitle  = $menumodel[$key];
                        $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['PHOTOALL'.$urldata[1]]  = $urldata[1];
                        }
                        unset($urldata);
                        break;
                    case 'res_photoalbum'://组装菜单扫描提示
                        $subtitle  = $menumodel[$key];
                        $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['PHOTOALBUM'.$urldata[1]]  = $urldata[1];
                        }
                        unset($urldata);
                        break;
                    case 'res_locationselect'://组装菜单扫描提示
                        $subtitle  = $menumodel[$key];
                        $urldata   = explode('::', $value['data']);
                        $getmenu   = $this->setWeixinmenu($type,'',$name,$value,$key);
                        $submenu[] = $getmenu['menu'];
                        if(!empty($getmenu['sqlmenu'])){
                                $sqlmenu['LOCATIONSELECT'.$urldata[1]]  = $urldata[1];
                        }
                        unset($urldata);
                        break;
        			default:
        				$this->error('菜单类型错误');
        				break;
        		}
        	}
        	//post的数据 sql的数据
        	$mainmenu = array();$mainmenu['button'] = $submenu;$postinfo = urldecode(json_encode($mainmenu,true));
            $emptytitle = function($postdata){
                $menuword = array();
                foreach ($postdata as $key => $value) {
                      $menuword[] = $value['tit'];
                }
                return implode('|', $menuword);
            };
            $menudata = array(
                  'title'       => empty($_POST['title']) ? $emptytitle($_POST['data']) : $_POST['title'],
                  'postmenu'    => $postinfo,
                  'sqlmenu'     => json_encode($sqlmenu,true),
                  'update_time' => time(),
                  'status'      => 0,
                  'circletime'  => ''
            );
            $menumodel = M('clickmenu');
            if(I('id')){
                $a = $menumodel->where(array('id'=>I('id')))->save($menudata);
                ($a===1) ? $this->success('编辑菜单成功') : $this->error('编辑菜单失败！');
            } else {
                $q = $menumodel->add($menudata);
                is_numeric($q) ? $this->success('新增菜单成功') : $this->error('新增菜单失败！');
            }
        } else {
            //关联微信响应体
            //$list = M('Response')->select();
            //关联关键词
            $list = D('KeywordView')->select();
            $this->assign('Responselist',$list);
            $this->assign('meta_title','关键词管理');
            $this->display();
        }
    }
    //点击菜单模板组装
    protected function setWeixinmenu($type,$subtitle,$name,$value,$key){
        switch ($type) {
            case 'res_gjz'://菜单关键字响应
                $submenu   = array(
                    'type' => 'click',
                    'name' => urlencode($name),
                    'key'  => $subtitle
                );
                $sqlmenu   = $value['data'];
                break;
            case 'res_url'://组装菜单链接
                $urldata   = explode('::', $value['data']);
                $submenu   = array(
                    'type' => 'view',
                    'name' => urlencode($name),
                    'url'  => urlencode($urldata[0])
                );
                $sqlmenu    = $urldata[1];
                break;
            case 'res_scanpush'://组装扫码推事件
                $urldata   = explode('::', $value['data']);
                $sqlmenu   = 'SCANPUSH'.$urldata[1];
                $submenu   = array(
                    'type' => 'scancode_push',
                    'name' => urlencode($name),
                    'key'  => urlencode($sqlmenu),
                    'sub_button' => array()
                );
                break;
            case 'res_scanmsg'://组装扫码带提示
                $urldata   = explode('::', $value['data']);
                $sqlmenu   = 'SCANMSG'.$urldata[1];
                $submenu   = array(
                    'type' => 'scancode_waitmsg',
                    'name' => urlencode($name),
                    'key'  => urlencode($sqlmenu),
                    'sub_button' => array()
                );
                break;
            case 'res_photosys'://组装系统拍照图
                $urldata   = explode('::', $value['data']);
                $sqlmenu   = 'PHOTOSYS'.$urldata[1];
                $submenu   = array(
                    'type' => 'pic_sysphoto',
                    'name' => urlencode($name),
                    'key'  => urlencode($sqlmenu),
                    'sub_button' => array()
                );
                break;
            case 'res_photoall'://组装系统拍照图
                $urldata   = explode('::', $value['data']);
                $sqlmenu   = 'PHOTOALL'.$urldata[1];
                $submenu   = array(
                    'type' => 'pic_photo_or_album',
                    'name' => urlencode($name),
                    'key'  => urlencode($sqlmenu),
                    'sub_button' => array()
                );
                break;
            case 'res_photoalbum'://组装系统拍照图
                $urldata   = explode('::', $value['data']);
                $sqlmenu   = 'PHOTOALBUM'.$urldata[1];
                $submenu   = array(
                    'type' => 'pic_weixin',
                    'name' => urlencode($name),
                    'key'  => urlencode($sqlmenu),
                    'sub_button' => array()
                );
                break;
            case 'res_locationselect'://组装系统拍照图
                $urldata   = explode('::', $value['data']);
                $sqlmenu   = 'LOCATIONSELECT'.$urldata[1];
                $submenu   = array(
                    'type' => 'location_select',
                    'name' => urlencode($name),
                    'key'  => urlencode($sqlmenu),
                    'sub_button' => array()
                );
                break;
            default:
                $this->error('子菜单类型错误！');
                break;
        }
            return array('menu'=>$submenu,'sqlmenu'=>$sqlmenu);
    }
    public function edit_clickmenu(){
        $menuinfo = M('Clickmenu')->where(array('id'=>I('id')))->find();
        $menu     = json_decode($menuinfo['postmenu'],true);
        $data     = json_decode($menuinfo['sqlmenu'],true);
        $button   = $menu['button'];
        //dump($menuinfo['sqlmenu']);die;
        $jsonmenu = array();
        $buttontype = array('click'=>'res_gjz','view'=>'res_url');//  
        $menutype = array(
               '0'=>'LEFT',
               '1'=>'CENTER',
               '2'=>'RIGHT',
        ); 
        //主要按钮
        foreach ($button as $key => $value) {
            $jsonmenu[] = $this->menu2json($value,$key,$data);
            if(!empty($value['sub_button'])){
                foreach ($value['sub_button'] as $k => $v) {
                    $submenu[$menutype[$key].$k] = array('reldata'=>array('typ'=>$buttontype[$v['type']],'data'=>($v['type']=='view')?$v['url']:$data[$menutype[$key].$k]),'name'=>str_replace(" ", '', $v['name']));
                }
            }
        }
        //dump($submenu);die;
        $list = D('KeywordView')->select();
        $this->assign('menuname',str_replace(" ", '', $menuinfo['title']));
        $this->assign('jsonmenu',urldecode(json_encode($jsonmenu,true)));
        $this->assign('Responselist',$list);
        $this->assign('submenu',$submenu);
        $this->assign('id',I('id'));
        $this->assign('meta_title','关键词管理');
        //获取子菜单按钮
        $this->display();

    }
    //数组菜单转json
    protected function menu2json($value,$key,$data){
        $result = array();
        if(!empty($value['sub_button'])){
            $titname = $value['name'];
            $result['typ'] = 'res_ejcd';
            foreach ($value['sub_button'] as $k => $v) {
                $itemid[] = $k+1;
            }
            $result['data'] = implode('@', $itemid);
            $result['tit']  = urlencode($titname);
        } else {
            $result = $this->setmenujson($key,$value,$data);
            $result['tit']  = urlencode($value['name']);
        }
            $result['subdata']  = '';
            return $result;
    }
    protected function setmenujson($key,$value,$data){
        $item = array(
           '0'=>'LEFT',
           '1'=>'CENTER',
           '2'=>'RIGHT',
        );
        switch ($value['type']) {
            case 'click':
                $result['typ']  = 'res_gjz';
                $result['data'] = $data[$item[$key]];
                break;
            case 'view':
                $result['typ']  = 'res_url';
                $result['data'] = $value['url'];
                break;
            default:
                $this->error('子菜单类型错误！');
                break;
        }
            return $result;
    }

    public function click_list(){
        $list = D('Clickmenu')->select();
        $this->assign('list',$list);
        $this->assign('meta_title','关键词管理');
        $this->display();
    }
    //启用微信菜单
    public function setclickmenu(){
        $clcikmenumodel = M('Clickmenu');
        if(is_numeric(I('id'))){
            //判断是否含有该菜单
            $menustatus  = $clcikmenumodel->where(array('id'=>I('id')))->getField('status');
            ($menustatus==1) ? $this->delmenu() : $this->setMenu();
        } else {
                $this->error('更改失败！请选择正确的自定义菜单ID');
        }
    }
    //设置菜单
    protected function setMenu(){
          $clcikmenumodel = M('Clickmenu');
        //获取微信菜单JSON
          $menujson     = $clcikmenumodel->where(array('id'=>I('id')))->getField('postmenu');
          ///emoji表情解析
          $menujson     = emoji($menujson);
          $access_token = $this->getToken();
          $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $menujson);
          $output = curl_exec($ch);
          curl_close($ch);
          $JR = json_decode($output);
          $rea = $JR->errmsg;
          if($rea['errcode']==0){
                $clcikmenumodel->where(array('id'=>I('id')))->save(array('status'=>1));
                $clcikmenumodel->where(array('id'=>array('neq',I('id'))))->save(array('status'=>0));
                $this->success('设置微信自定义菜单成功！');
          } else {
                $this->error('设置微信自定义菜单失败！错误代码：'.$rea['errcode'].'错误提示：'.$rea['errmsg']);
          }  
    }
    //返回微信Token
    protected function getToken(){
        $accesstoken = S('ACCESS_TOKEN');
        if(empty($accesstoken)){
            //获取公众号的APPID SECRET
            $account = M('Account')->where(array('account_default'=>'default'))->find();
            if(empty($account['account_appid'])||empty($account['account_secret'])){
                $this->error('请先配置微信公众号的APPID和SECRET【服务号自带；未认证的订阅号无法使用菜单】');
            }
            //获取TOKEN
            $api  = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$account['account_appid']."&secret=".$account['account_secret'];
            $json = file_get_contents($api);
            $JT   = json_decode($json);
            $access_token = $JT->access_token;
            if(empty($access_token)){
                $this->error('错误提示:'.$JT->errmsg);
            }
            S('ACCESS_TOKEN',$access_token,7170);
            return $access_token;
        } else {
            return $accesstoken;
        }
    }
    //返回微信Token
    public function delmenu(){
        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
        $json1 = file_get_contents($url);
        $JR = json_decode($json1);
        $rea = $JR->errmsg;
         if($rea['errcode']==0){
            M('clickmenu')->where(array('id'=>array('gt',0)))->save(array('status'=>'0'));
            $this->success('清除微信自定义菜单成功！');
         } else {
            $this->error('清除微信自定义菜单失败！错误代码：'.$rea['errcode'].'错误提示：'.$rea['errmsg']);
         }
    }
    //返回微信Token
    public function del_clickmemu(){
        $status = M('Clickmenu')->where(array('id'=>I('id')))->delete();
        (false===$status) ? $this->error('删除自定义菜单失败') : $this->success('删除自定义菜单成功！');

    }
}
