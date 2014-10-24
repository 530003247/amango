<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 后台内容控制器
 * @author huajie <banhuajie@163.com>
 */
class ArticleController extends AdminController {

	/* 保存允许访问的公共方法 */
	static protected $allow = array( 'draftbox','mydocument');

    private $cate_id        =   null; //文档分类id

    /**
     * 检测需要动态判断的文档类目有关的权限
     *
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        $cates = AuthGroupModel::getAuthCategories(UID);
        switch(strtolower(ACTION_NAME)){
            case 'index':   //文档列表
                $cate_id =  I('cate_id');
                break;
            case 'edit':    //编辑
            case 'update':  //更新
                $doc_id  =  I('id');
                $cate_id =  M('Document')->where(array('id'=>$doc_id))->getField('category_id');
                break;
            case 'setstatus': //更改状态
            case 'permit':    //回收站
                $doc_id  =  (array)I('ids');
                $cate_id =  M('Document')->where(array('id'=>array('in',$doc_id)))->getField('category_id',true);
                $cate_id =  array_unique($cate_id);
                break;
        }
        if(!$cate_id){
            return null;//不明,需checkRule
        }elseif( !is_array($cate_id) && in_array($cate_id,$cates) ) {
            return true;//有权限
        }elseif( is_array($cate_id) && $cate_id==array_intersect($cate_id,$cates) ){
            return true;//有权限
        }else{
            return false;//无权限
        }
        return null;//不明,需checkRule
    }

    /**
     * 显示左边菜单，进行权限控制
     * @author huajie <banhuajie@163.com>
     */
    protected function getMenu(){
        //获取动态分类
        $cate_auth  =   AuthGroupModel::getAuthCategories(UID);	//获取当前用户所有的内容权限节点
        $cate_auth  =   $cate_auth == null ? array() : $cate_auth;
        $cate       =   M('Category')->where(array('status'=>1))->field('id,title,pid,allow_publish')->order('pid,sort')->select();

        //没有权限的分类则不显示
        if(!IS_ROOT){
            foreach ($cate as $key=>$value){
                if(!in_array($value['id'], $cate_auth)){
                    unset($cate[$key]);
                }
            }
        }

        $cate           =   list_to_tree($cate);	//生成分类树

        //获取分类id
        $cate_id        =   I('param.cate_id');
        $this->cate_id  =   $cate_id;

        //是否展开分类
        $hide_cate = false;
        if(ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument'){
            $hide_cate  =   true;
        }

        //生成每个分类的url
        foreach ($cate as $key=>&$value){
            $value['url']   =   'Article/index?cate_id='.$value['id'];
            if($cate_id == $value['id'] && $hide_cate){
                $value['current'] = true;
            }else{
                $value['current'] = false;
            }
            if(!empty($value['_child'])){
                $is_child = false;
                foreach ($value['_child'] as $ka=>&$va){
                    $va['url']      =   'Article/index?cate_id='.$va['id'];
                    if(!empty($va['_child'])){
                        foreach ($va['_child'] as $k=>&$v){
                            $v['url']   =   'Article/index?cate_id='.$v['id'];
                            $v['pid']   =   $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if($va['id'] == $cate_id || $is_child){
                        $is_child = false;
                        if($hide_cate){
                            $value['current']   =   true;
                            $va['current']      =   true;
                        }else{
                            $value['current'] 	= 	false;
                            $va['current']      =   false;
                        }
                    }else{
                        $va['current']      =   false;
                    }
                }
            }
        }
        $this->assign('nodes',      $cate);
        $this->assign('cate_id',    $this->cate_id);

        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav',   $nav);

        //获取回收站权限
        $show_recycle = $this->checkRule('Admin/article/recycle');
        $this->assign('show_recycle', IS_ROOT || $show_recycle);
        //获取草稿箱权限
        $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
    }

    /**
     * 分类文档列表页
     * @param $cate_id 分类id
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    public function index($cate_id = null){
        //获取左边菜单
        $this->getMenu();

        if($cate_id===null){
            $cate_id = $this->cate_id;
        }

        //获取模型信息
        $model = M('Model')->getByName('document');

        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', $model['list_grid']);
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }

        // 过滤重复字段信息 TODO: 传入到查询方法
        $fields = array_unique($fields);

        //获取对应分类下的模型
        if(!empty($cate_id)){   //没有权限则不查询数据
            //获取分类绑定的模型
            $models         =   get_category($cate_id, 'model');
            $allow_reply    =   get_category($cate_id, 'reply');//分类文档允许回复
            $pid            =   I('pid');
            if ( $pid==0 ) {
                //开发者可根据分类绑定的模型,按需定制分类文档列表
                $template = $this->indexOfArticle( $cate_id ); //转入默认文档列表方法
                $this->assign('model',  explode(',',$models));
            }else{
                //开发者可根据父文档的模型类型,按需定制子文档列表
                $doc_model = M('Document')->where(array('id'=>$pid))->find();

                switch($doc_model['model_id']){
                    default:
                        if($doc_model['type']==2 && $allow_reply){
                            $this->assign('model',  array(2));
                            $template = $this->indexOfReply( $cate_id ); //转入子文档列表方法
                        }else{
                            $this->assign('model',  explode(',',$models));
                            $template = $this->indexOfArticle( $cate_id ); //转入默认文档列表方法
                        }
                }
            }
            $this->assign('list_grids', $grids);
            $this->assign('model_list', $model);
            // 记录当前列表页的cookie
            Cookie('__forward__',$_SERVER['REQUEST_URI']);
            $this->display($template);
        }else{
            $this->error('非法的文档分类');
        }
    }
    public function replylists($model = null, $p = 0,$tpl='', $pid='', $modelid=''){
        //芒果  根据ID  获取 回复表名 reply+标识
        $pid || $this->error('请选择正确的资讯ID');
        $modelid || $this->error('请选择正确的资讯ID');
        unset($_GET['modelid']);unset($_REQUEST['modelid']);
        $modelname = M('Category')->where(array('model'=>$modelid))->getField('name');
        $model = 'reply'.$modelname;
        $tablename  = C('DB_PREFIX').$model;
$sql = <<<sql
                SHOW TABLES LIKE '{$tablename}';
sql;
        //判断回复表是否存在
        $res = M()->query($sql);
        (count($res)>=1) || $this->error('找不到该资讯分类对应的回复表');

        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = M('Model')->getByName($model);
        $model || $this->error('模型不存在！');


        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', $model['list_grid']);
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 关键字搜索
        $map    =   array();
        $key    =   $model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]  =   array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        if($model['extend']){
            $name   = get_table_name($model['id']);
            $parent = get_table_name($model['extend']);
            $fix    = C("DB_PREFIX");

            $key = array_search('id', $fields);
            if(false === $key){
                array_push($fields, "{$fix}{$parent}.id as id");
            } else {
                $fields[$key] = "{$fix}{$parent}.id as id";
            }

            /* 查询记录数 */
            $count = M($parent)->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")->where($map)->count();

            // 查询数据
            $data   = M($parent)
                ->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order("{$fix}{$parent}.id DESC")
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();

        } else {
            //芒果自定义排序  如果包含sort字段  那么 按照sor排序
            $order = (false === strpos($model['list_grid'],'sort')) ? 'id DESC' : 'sort DESC';
            in_array('id', $fields) || array_push($fields, 'id');
            $name = parse_name(get_table_name($model['id']), true);
            $data = M($name)
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order($order)
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();

            /* 查询记录总数 */
            $count = M($name)->where($map)->count();
        }
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //获取帖子标题
        $doutitle = M('Document')->where(array('id'=>$pid))->getField('title');
        $model['title'] = $doutitle.'-回复';
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';

        //芒果
        $tpl = $tpl ? $tpl : $model['template_list']; //默认显示第一页数据
        $this->display($tpl);
    }
    /**
     * 默认文档回复列表方法
     * @param $cate_id 分类id
     * @author huajie <banhuajie@163.com>
     */
    protected function indexOfReply($cate_id) {
        /* 查询条件初始化 */
        $map = array();
        if(isset($_GET['content'])){
            $map['content']  = array('like', '%'.(string)I('content').'%');
        }
        if(isset($_GET['status'])){
            $map['status'] = I('status');
            $status = $map['status'];
        }else{
            $status = null;
            $map['status'] = array('in', '0,1,2');
        }
        if ( !isset($_GET['pid']) ) {
            $map['pid']    = 0;
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if ( isset($_GET['username']) ) {
            $map['uid'] = M('UcenterMember')->where(array('username'=>I('username')))->getField('id');
        }

        // 构建列表数据
        $Document = M('Document');
        $map['category_id'] =   $cate_id;
        $map['pid']         =   I('pid',0);
        if($map['pid']){ // 子文档列表忽略分类
            unset($map['category_id']);
        }

        $prefix   = C('DB_PREFIX');
        $l_table  = $prefix.('document');
        $r_table  = $prefix.('document_article');
        $list     = M() ->table( $l_table.' l' )
                       ->where( $map )
                       ->order( 'l.id DESC')
                       ->join ( $r_table.' r ON l.id=r.id' );
        $_REQUEST = array();
        $list = $this->lists($list,null,null,null,'l.id id,l.pid pid,l.category_id,l.title title,l.update_time update_time,l.uid uid,l.status status,r.content content' );
        int_to_string($list);

        if($map['pid']){
            // 获取上级文档
            $article    =   $Document->field('id,title,type')->find($map['pid']);
            $this->assign('article',$article);
        }
        //检查该分类是否允许发布内容
        $allow_publish  =   get_category($cate_id, 'allow_publish');

        $this->assign('status', $status);
        $this->assign('list',   $list);
        $this->assign('allow',  $allow_publish);
        $this->assign('pid',    $map['pid']);
        $this->meta_title = '子文档列表';
        return 'reply';//默认回复列表模板
    }
    /**
     * 默认文档列表方法
     * @param $cate_id 分类id
     * @author huajie <banhuajie@163.com>
     */
    protected function indexOfArticle($cate_id){
        /* 查询条件初始化 */
        $map = array();
        if(isset($_GET['title'])){
            $map['title']  = array('like', '%'.(string)I('title').'%');
        }
        if(isset($_GET['status'])){
            $map['status'] = I('status');
            $status = $map['status'];
        }else{
            $status = null;
            $map['status'] = array('in', '0,1,2');
        }
        if ( !isset($_GET['pid']) ) {
            $map['pid']    = 0;
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if ( isset($_GET['nickname']) ) {
            $map['uid'] = M('Member')->where(array('nickname'=>I('nickname')))->getField('uid');
        }

        // 构建列表数据
        $Document = M('Document');
        $map['category_id'] =   $cate_id;
        $map['pid']         =   I('pid',0);
        if($map['pid']){ // 子文档列表忽略分类
            unset($map['category_id']);
        }

        $list = $this->lists($Document,$map,'level DESC,id DESC');
        int_to_string($list);
        if($map['pid']){
            // 获取上级文档
            $article    =   $Document->field('id,title,type')->find($map['pid']);
            $this->assign('article',$article);
        }
        //检查该分类是否允许发布内容
        $allow_publish  =   get_category($cate_id, 'allow_publish');
        $this->assign('status', $status);
        $this->assign('list',   $list);
        $this->assign('allow',  $allow_publish);
        $this->assign('pid',    $map['pid']);

        $this->meta_title = '文档列表';
        return 'index';
    }

    /**
     * 设置一条或者多条数据的状态
     * @author huajie <banhuajie@163.com>
     */
    public function setStatus($model='Document'){
        return parent::setStatus('Document');
    }

    /**
     * 文档新增页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function add(){
        //获取左边菜单
        $this->getMenu();

        $cate_id    =   I('get.cate_id',0);
        $model_id   =   I('get.model_id',0);

        empty($cate_id) && $this->error('参数不能为空！');
        empty($model_id) && $this->error('该分类未绑定模型！');

        //检查该分类是否允许发布
        $allow_publish = D('Document')->checkCategory($cate_id);
        !$allow_publish && $this->error('该分类不允许发布内容！');

        /* 获取要编辑的扩展模型模板 */
        $model      =   get_document_model($model_id);

        //处理结果
        $info['pid']            =   $_GET['pid']?$_GET['pid']:0;
        $info['model_id']       =   $model_id;
        $info['category_id']    =   $cate_id;
        if($info['pid']){
            // 获取上级文档
            $article            =   M('Document')->field('id,title,type')->find($info['pid']);
            $this->assign('article',$article);
        }

        //获取表单字段排序
        $fields = get_model_attribute($model['id']);
        $this->assign('info',       $info);
        $this->assign('fields',     $fields);
        $this->assign('type_list',  get_type_bycate($cate_id));
        $this->assign('model',      $model);
        $this->meta_title = '新增'.$model['title'];
        $this->display();
    }

    /**
     * 文档编辑页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function edit(){
        //获取左边菜单
        $this->getMenu();

        $id     =   I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }
        /*获取一条记录的详细数据*/
        $Document = D('Document');
        $data = $Document->detail($id);
        if(!$data){
            $this->error($Document->getError());
        }

        if($data['pid']){
            // 获取上级文档
            $article        =   M('Document')->field('id,title,type')->find($data['pid']);
            $this->assign('article',$article);
        }
        $this->assign('data', $data);
        $this->assign('model_id', $data['model_id']);

        /* 获取要编辑的扩展模型模板 */
        $model      =   get_document_model($data['model_id']);
        $this->assign('model',      $model);

        //获取表单字段排序
        $fields = get_model_attribute($model['id']);
        $this->assign('fields',     $fields);


        //获取当前分类的文档类型
        $this->assign('type_list', get_type_bycate($data['category_id']));

        $this->meta_title   =   '编辑文档';
        $this->display();
    }

    /**
     * 更新一条数据
     * @author huajie <banhuajie@163.com>
     */
    public function update(){
        $res = D('Document')->update();
        if(!$res){
            $this->error(D('Document')->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }

    /**
     * 批量操作
     * @author huajie <banhuajie@163.com>
     */
    public function batchOperate(){
        //获取左边菜单
        $this->getMenu();

        $pid = I('pid', 0);
        $cate_id = I('cate_id');

        empty($cate_id) && $this->error('参数不能为空！');

        //检查该分类是否允许发布
        $allow_publish = D('Document')->checkCategory($cate_id);
        !$allow_publish && $this->error('该分类不允许发布内容！');

        //批量导入目录
        if(IS_POST){
            $model_id = I('model_id');
            $type = 1;	//TODO:目前只支持目录，要动态获取
            $content = I('content');
            $_POST['content'] = '';	//重置内容
            preg_match_all('/[^\r]+/', $content, $matchs);	//获取每一个目录的数据
            $list = $matchs[0];
            foreach ($list as $value){
                if(!empty($value) && (strpos($value, '|') !== false)){
                    //过滤换行回车并分割
                    $data = explode('|', str_replace(array("\r", "\r\n", "\n"), '', $value));
                    //构造新增的数据
                    $data = array('name'=>$data[0], 'title'=>$data[1], 'category_id'=>$cate_id, 'model_id'=>$model_id);
                    $data['description'] = '';
                    $data['pid'] = $pid;
                    $data['type'] = $type;
                    //构造post数据用于自动验证
                    $_POST = $data;

                    $res = D('Document')->update($data);
                }
            }
            if($res){
                $this->success('批量导入成功！', U('index?pid='.$pid.'&cate_id='.$cate_id));
            }else{
                if(isset($res)){
                    $this->error(D('Document')->getError());
                }else{
                    $this->error('批量导入失败，请检查内容格式！');
                }
            }
        }

        $this->assign('pid',        $pid);
        $this->assign('cate_id',	$cate_id);
        $this->assign('type_list',  get_type_bycate($cate_id));

        $this->meta_title       =   '批量导入';
        $this->display('batchoperate');
    }


    /**
     * 待审核列表
     */
    public function examine(){
        //获取左边菜单
        $this->getMenu();

        $map['status']  =   2;
        if ( !IS_ROOT ) {
            $cate_auth  =   AuthGroupModel::getAuthCategories(UID);
            if($cate_auth){
                $map['category_id']    =   array('IN',$cate_auth);
            }else{
                $map['category_id']    =   -1;
            }
        }
        $list = $this->lists(M('Document'),$map,'update_time desc');
        //处理列表数据
        if(is_array($list)){
            foreach ($list as $k=>&$v){
                $v['username']      =   get_nickname($v['uid']);
            }
        }

        $this->assign('list', $list);
        $this->meta_title       =   '待审核';
        $this->display();
    }

    /**
     * 回收站列表
     * @author huajie <banhuajie@163.com>
     */
    public function recycle(){
        //获取左边菜单
        $this->getMenu();

        $map['status']  =   -1;
        if ( !IS_ROOT ) {
            $cate_auth  =   AuthGroupModel::getAuthCategories(UID);
            if($cate_auth){
                $map['category_id']    =   array('IN',$cate_auth);
            }else{
                $map['category_id']    =   -1;
            }
        }
        $list = $this->lists(M('Document'),$map,'update_time desc');

        //处理列表数据
        if(is_array($list)){
            foreach ($list as $k=>&$v){
                $v['username']      =   get_nickname($v['uid']);
            }
        }

        $this->assign('list', $list);
        $this->meta_title       =   '回收站';
        $this->display();
    }

    /**
     * 写文章时自动保存至草稿箱
     * @author huajie <banhuajie@163.com>
     */
    public function autoSave(){
        $res = D('Document')->autoSave();
        if($res !== false){
            $return['data']     =   $res;
            $return['info']     =   '保存草稿成功';
            $return['status']   =   1;
            $this->ajaxReturn($return);
        }else{
            $this->error('保存草稿失败：'.D('Document')->getError());
        }
    }

    /**
     * 草稿箱
     * @author huajie <banhuajie@163.com>
     */
    public function draftBox(){
        //获取左边菜单
        $this->getMenu();

        $Document   =   D('Document');
        $map        =   array('status'=>3,'uid'=>UID);
        $list       =   $this->lists($Document,$map);
        //获取状态文字
        //int_to_string($list);

        $this->assign('list', $list);
        $this->meta_title = '草稿箱';
        $this->display();
    }

    /**
     * 我的文档
     * @author huajie <banhuajie@163.com>
     */
    public function mydocument($status = null, $title = null){
        //获取左边菜单
        $this->getMenu();

        $Document   =   D('Document');
        /* 查询条件初始化 */
        $map['uid'] = UID;
        if(isset($title)){
            $map['title']   =   array('like', '%'.$title.'%');
        }
        if(isset($status)){
            $map['status']  =   $status;
        }else{
            $map['status']  =   array('in', '0,1,2');
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        //只查询pid为0的文章
        $map['pid'] = 0;
        $list = $this->lists($Document,$map,'update_time desc');
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->meta_title = '我的文档';
        $this->display();
    }

    /**
     * 还原被删除的数据
     * @author huajie <banhuajie@163.com>
     */
    public function permit(){
        /*参数过滤*/
        $ids = I('param.ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }

        /*拼接参数并修改状态*/
        $Model  =   'Document';
        $map    =   array();
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $this->restore($Model,$map);
    }

    /**
     * 清空回收站
     * @author huajie <banhuajie@163.com>
     */
    public function clear(){
        $res = D('Document')->remove();
        if($res !== false){
            $this->success('清空回收站成功！');
        }else{
            $this->error('清空回收站失败！');
        }
    }

    /**
     * 移动文档
     * @author huajie <banhuajie@163.com>
     */
    public function move() {
        if(empty($_POST['ids'])) {
            $this->error('请选择要移动的文档！');
        }
        session('moveArticle', $_POST['ids']);
        session('copyArticle', null);
        $this->success('请选择要移动到的分类！');
    }

    /**
     * 拷贝文档
     * @author huajie <banhuajie@163.com>
     */
    public function copy() {
        if(empty($_POST['ids'])) {
            $this->error('请选择要复制的文档！');
        }
        session('copyArticle', $_POST['ids']);
        session('moveArticle', null);
        $this->success('请选择要复制到的分类！');
    }

    /**
     * 粘贴文档
     * @author huajie <banhuajie@163.com>
     */
    public function paste() {
        $moveList = session('moveArticle');
        $copyList = session('copyArticle');
        if(empty($moveList) && empty($copyList)) {
            $this->error('没有选择文档！');
        }
        if(!isset($_POST['cate_id'])) {
            $this->error('请选择要粘贴到的分类！');
        }
        $cate_id = I('post.cate_id');	//当前分类
        $pid = I('post.pid', 0);		//当前父类数据id

        //检查所选择的数据是否符合粘贴要求
        $check = $this->checkPaste(empty($moveList) ? $copyList : $moveList, $cate_id, $pid);
        if(!$check['status']){
            $this->error($check['info']);
        }

        if(!empty($moveList)) {// 移动	TODO:检查name重复
            foreach ($moveList as $key=>$value){
                $Model              =   M('Document');
                $map['id']          =   $value;
                $data['category_id']=   $cate_id;
                $data['pid'] 		=   $pid;
                //获取root
                if($pid == 0){
                    $data['root'] = 0;
                }else{
                    $p_root = $Model->getFieldById($pid, 'root');
                    $data['root'] = $p_root == 0 ? $pid : $p_root;
                }
                $res = $Model->where($map)->save($data);
            }
            session('moveArticle', null);
            if(false !== $res){
                $this->success('文档移动成功！');
            }else{
                $this->error('文档移动失败！');
            }
        }elseif(!empty($copyList)){ // 复制
            foreach ($copyList as $key=>$value){
                $Model  =   M('Document');
                $data   =   $Model->find($value);
                unset($data['id']);
                unset($data['name']);
                $data['category_id']    =   $cate_id;
                $data['pid'] 			=   $pid;
                $data['create_time']    =   NOW_TIME;
                $data['update_time']    =   NOW_TIME;
                //获取root
                if($pid == 0){
                    $data['root'] = 0;
                }else{
                    $p_root = $Model->getFieldById($pid, 'root');
                    $data['root'] = $p_root == 0 ? $pid : $p_root;
                }

                $result   =  $Model->add($data);
                if($result){
                    $logic      =   D(get_document_model($data['model_id'],'name'),'Logic');
                    $data       =   $logic->detail($value); //获取指定ID的扩展数据
                    $data['id'] =   $result;
                    $res 		= 	$logic->add($data);
                }
            }
            session('copyArticle', null);
            if($res){
                $this->success('文档复制成功！');
            }else{
                $this->error('文档复制失败！');
            }
        }
    }

    /**
     * 检查数据是否符合粘贴的要求
     * @author huajie <banhuajie@163.com>
     */
    protected function checkPaste($list, $cate_id, $pid){
        $return = array('status'=>1);
        $Document = D('Document');

        // 检查支持的文档模型
        $modelList =   M('Category')->getFieldById($cate_id,'model');	// 当前分类支持的文档模型
        foreach ($list as $key=>$value){
            //不能将自己粘贴为自己的子内容
            if($value == $pid){
                $return['status'] = 0;
                $return['info'] = '不能将编号为 '.$value.' 的数据粘贴为他的子内容！';
                return $return;
            }
            // 移动文档的所属文档模型
            $modelType  =   $Document->getFieldById($value,'model_id');
            if(!in_array($modelType,explode(',',$modelList))) {
                $return['status'] = 0;
                $return['info'] = '当前分类的文档模型不支持编号为 '.$value.' 的数据！';
                return $return;
            }
        }

        // 检查支持的文档类型和层级规则
        $typeList =   M('Category')->getFieldById($cate_id,'type');	// 当前分类支持的文档模型
        foreach ($list as $key=>$value){
            // 移动文档的所属文档模型
            $modelType  =   $Document->getFieldById($value,'type');
            if(!in_array($modelType,explode(',',$typeList))) {
                $return['status'] = 0;
                $return['info'] = '当前分类的文档类型不支持编号为 '.$value.' 的数据！';
                return $return;
            }
            $res = $Document->checkDocumentType($modelType, $pid);
            if(!$res['status']){
                $return['status'] = 0;
                $return['info'] = $res['info'].'。错误数据编号：'.$value;
                return $return;
            }
        }

        return $return;
    }

    /**
     * 文档排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            //获取左边菜单
            $this->getMenu();

            $ids        =   I('get.ids');
            $cate_id    =   I('get.cate_id');
            $pid        =   I('get.pid');

            //获取排序的数据
            $map['status'] = array('gt',-1);
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }else{
                if($cate_id !== ''){
                    $map['category_id'] = $cate_id;
                }
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = M('Document')->where($map)->field('id,title')->order('level DESC,id DESC')->select();

            $this->assign('list', $list);
            $this->meta_title = '文档排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = array_reverse(explode(',', $ids));
            foreach ($ids as $key=>$value){
                $res = M('Document')->where(array('id'=>$value))->setField('level', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->eorror('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}
