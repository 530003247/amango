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
 * 扩展后台管理页面
 * @author yangweijie <yangweijiester@gmail.com>
 */
class AddonsController extends AdminController {

    public function _initialize(){
        $this->assign('_extra_menu',array(
            '已装插件后台'=> D('Addons')->getAdminList(),
        ));
        parent::_initialize();
    }

    //创建向导首页
    public function create(){
        if(!is_writable(ONETHINK_ADDON_PATH))
            $this->error('您没有创建目录写入权限，无法使用此功能');

        $hooks = M('Hooks')->field('name,description')->select();
        $this->assign('Hooks',$hooks);
        $list = '';
        $tagsgroup = M('Tagscate')->field('id,tagscate_name,tagscate_title')->select();
        foreach ($tagsgroup as $key => $value) {
        	$list .= $value['tagscate_title'].':'."\n";
        }

        $this->assign('Tagsgroup',str_replace(' ', '', $list));
        $this->meta_title = '创建向导';
        $this->display('create');
    }

    //预览
    public function preview($output = true){
        $data                   =   $_POST;
        $data['info']['status'] =   (int)$data['info']['status'];
        $extend                 =   array();
        $custom_config          =   trim($data['custom_config']);

        $tagslist = parse_config($data['tagslists']);
        //去掉空标签配置
        foreach ($tagslist as $key => $value) {
        	if(empty($key)||empty($value)){
                 unset($tagslist[$key]);
        	}
        }
        //生成标签 为空 则不生成
        $amangotag = empty($tagslist)? '':'amangotag=>'.var_export($tagslist, true).',';
        if($data['has_config'] && $custom_config){
            $custom_config = <<<str


        public \$custom_config = '{$custom_config}';
str;
            $extend[] = $custom_config;
        }

        $admin_list = trim($data['admin_list']);
        if($data['has_adminlist'] && $admin_list){
            $admin_list = <<<str


        public \$admin_list = array(
            {$admin_list}
        );
str;
           $extend[] = $admin_list;
        }

        $custom_adminlist = trim($data['custom_adminlist']);
        if($data['has_adminlist'] && $custom_adminlist){
            $custom_adminlist = <<<str


        public \$custom_adminlist = '{$custom_adminlist}';
str;
            $extend[] = $custom_adminlist;
        }

        $extend = implode('', $extend);
        $hook = '';
        //默认注册芒果hook
        if(empty($data['hook'])){
            $data['hook'] = array('amango');
        }
        foreach ($data['hook'] as $value) {
            $hook .= <<<str
        //实现的{$value}钩子方法
        public function {$value}(\$param){

        }

str;
        }
$addonsname  = $data['info']['name'].'Addon';
$weixinstat  = ($data['weixin']==1)? '1':'0';
$has_profile = ($data['profile']==1)? '1':'0';
        $tpl = <<<str
<?php

namespace Addons\\{$data['info']['name']};
use Common\Controller\Addon;

/**
 * {$data['info']['title']}插件
 * @author {$data['info']['author']}
 */

    class {$addonsname} extends Addon{

        public \$info = array(
            //插件标识
            'name'=>'{$data['info']['name']}',
            //插件名称
            'title'=>'{$data['info']['title']}',
            {$amangotag}
            //插件是否含有微信Bundle 1/0
            'weixin'=>'{$weixinstat}',
            //插件前台含有个人中心 1/0
            'has_profile'=>'{$has_profile}',
            //插件描述
            'description'=>'{$data['info']['description']}',
            //插件状态
            'status'=>'{$data['info']['status']}',
            //插件作者
            'author'=>'{$data['info']['author']}',
            //插件版本
            'version'=>'{$data['info']['version']}',
            //插件LOGO
            'logo'=>'logo.jpg',
        );{$extend}
            /**
             * 当该插件含有安装或者卸载SQL时候，请放置于插件根目录，并将下面4行注释去掉
             * 若无安装sql时  默认返回true
             */
        public function install(){
            /**     \$install_sql = './Addons/{$data['info']['name']}/install.sql';
              *     if (file_exists()) {
              *      execute_sql_file(\$install_sql);
              *     }
              */
            return true;
        }

        public function uninstall(){
            /**     \$uninstall = './Addons/{$data['info']['name']}/uninstall.sql';
              *     if (file_exists(\$uninstall)) {
              *      execute_sql_file(\$uninstall);
              *     }
              */
            return true;
        }

{$hook}
    }
str;
        if($output)
            exit($tpl);
        else
            return $tpl;
    }

    public function checkForm(){
        $data                   =   $_POST;
        $data['info']['name']   =   trim($data['info']['name']);
        if(!$data['info']['name'])
            $this->error('插件标识必须');
        //检测插件名是否合法
        $addons_dir             =   ONETHINK_ADDON_PATH;
        if(file_exists("{$addons_dir}{$data['info']['name']}")){
            $this->error('插件已经存在了');
        }
        $this->success('可以创建');
    }

    public function build(){
        $data                   =   $_POST;
        //根据标签生成微信方法
        $tagslist = parse_config($data['tagslists']);
        //去掉空标签配置
        foreach ($tagslist as $key => $value) {
        	if(empty($key)){
                 unset($tagslist[$key]);
        	}
        }
        
        $autotagaction = '';
        foreach ($tagslist as $key => $value) {
        	if(substr_count($value,'/')==0){
            $autotagaction .= <<<str
        //实现的{$value}微信TAG方法
        public function {$value}(\$param){
            return '实现的{$value}微信TAG方法';
        }

str;
        	}
        }

        $data['info']['name']   =   trim($data['info']['name']);
        $addonFile              =   $this->preview(false);
        $addons_dir             =   ONETHINK_ADDON_PATH;
        //创建目录结构
        $files          =   array();
        $addon_dir      =   "$addons_dir{$data['info']['name']}/";
        $files[]        =   $addon_dir;
        $addon_name     =   "{$data['info']['name']}Addon.class.php";
        $files[]        =   "{$addon_dir}{$addon_name}";
        if($data['has_config'] == 1);//如果有配置文件
            $files[]    =   $addon_dir.'config.php';
        if($data['has_org'] == 1);//如果有自定义类
            $files[]    =   "{$addon_dir}ORG/";
        if($data['has_outurl']){
            //生成插件控制器
            $files[]    =   "{$addon_dir}Controller/";
            $files[]    =   "{$addon_dir}Controller/{$data['info']['name']}Controller.class.php";
            $files[]    =   "{$addon_dir}Controller/HomeController.class.php";
            $files[]    =   "{$addon_dir}Controller/WeixinController.class.php";
            $files[]    =   "{$addon_dir}Model/";
            $files[]    =   "{$addon_dir}Model/{$data['info']['name']}Model.class.php";
            //生成资源文件
            $files[]    =   "{$addon_dir}Public/";
            //生成模板文件
            $files[]    =   "{$addon_dir}View/";
            $files[]    =   "{$addon_dir}View/default/";
            $files[]    =   "{$addon_dir}View/default/Home/";
            $files[]    =   "{$addon_dir}View/default/Home/index.html";
            $files[]    =   "{$addon_dir}View/default/Home/profile.html";
        }
        $custom_config  =   trim($data['custom_config']);
        if($custom_config)
            $data[]     =   "{$addon_dir}{$custom_config}";

        $custom_adminlist = trim($data['custom_adminlist']);
        if($custom_adminlist)
            $data[]     =   "{$addon_dir}{$custom_adminlist}";

        create_dir_or_files($files);

        //写文件
        file_put_contents("{$addon_dir}{$addon_name}", $addonFile);
        if($data['has_outurl']){
//创建  插件名Controller
            $addonController = <<<str
<?php

namespace Addons\\{$data['info']['name']}\Controller;
use Home\Controller\AddonsController;

class {$data['info']['name']}Controller extends AddonsController{

}

str;
            file_put_contents("{$addon_dir}Controller/{$data['info']['name']}Controller.class.php", $addonController);
//创建  HomeController
            $homeController = <<<str
<?php

namespace Addons\\{$data['info']['name']}\Controller;
use Home\Controller\AddonsController;

class HomeController extends AddonsController{
    /**
     * 开发说明:三大全局变量  global \$_W,\$_K;            W[网站信息]  K[默认微信公众号信息]
     *                        session('P');              前台用户信息
     *          获取插件配置  get_addon_config(\$name);
     */
    //插件前台展示页面
    public function index(){
        \$this->display();
    }
    //插件Tips返回处理 return 数字
    public function run(){
        return '';
    }
    //插件首页用户后台
    public function profile(){
        \$this->display();
    }
}

str;
            file_put_contents("{$addon_dir}Controller/HomeController.class.php", $homeController);
//创建 Home模板文件
            $indexhtml = <<<str
<!--  统一风格  采用模板渲染 插件通用模板addons 默认已加载 请参考 Amaze UI的css框架 -->
<extend name="Base/addon"/>
<block name="header"></block>
<block name="body"></block>
<block name="foot"></block>

str;
            file_put_contents("{$addon_dir}View/default/Home/index.html", $indexhtml);
//创建 Home模板文件
            $profilehtml = <<<str
<!--  统一风格  采用模板渲染 用户中心通用模板addons 默认已加载 请参考 Amaze UI的css框架 -->
<extend name="Base/addons"/>
<block name="title">网页标题</block>
<block name="main">内容主体</block>
<block name="foot"></block>
str;
            file_put_contents("{$addon_dir}View/default/Home/profile.html", $profilehtml);
            $addonModel = <<<str
<?php

namespace Addons\\{$data['info']['name']}\Model;
use Think\Model;

/**
 * {$data['info']['name']}模型
 * model   作用: 列表页显示参数设置
 * _fields 作用: 编辑时候显示字段
 */
class {$data['info']['name']}Model extends Model{
    public \$model = array(
        'title'=>'',//新增[title]、编辑[title]、删除[title]的提示
        'template_add'=>'',//自定义新增模板自定义html edit.html 会读取插件根目录的模板
        'template_edit'=>'',//自定义编辑模板html
        'search_key'=>'',// 搜索的字段名，默认是title
        'extend'=>1,
    );

    public \$_fields = array(
        'id'=>array(
            'name'=>'id',//字段名
            'title'=>'ID',//显示标题
            'type'=>'num',//字段类型
            'remark'=>'',// 备注，相当于配置里的tip
            'is_show'=>3,// 1-始终显示 2-新增显示 3-编辑显示 0-不显示
            'value'=>0,//默认值
        ),
        'title'=>array(
            'name'=>'title',
            'title'=>'书名',
            'type'=>'string',
            'remark'=>'',
            'is_show'=>1,
            'value'=>0,
            'is_must'=>1,
        ),
    );
}

str;
            file_put_contents("{$addon_dir}Model/{$data['info']['name']}Model.class.php", $addonModel);
        }
        //生成微信处理
        if($data['weixin']){
            $weixinController = <<<str
<?php

namespace Addons\\{$data['info']['name']}\Controller;
use Common\Controller\Bundle;

/**
 * {$data['info']['name']}微信处理Bundle
 */
class WeixinController extends Bundle{
    /**
     * 自动匹配定位:可以通过配置_rules属性
     * 开发说明:三大全局变量  global \$_W,\$_P,\$_K;            W[微信请求参数]  P[用户信息] K[匹配到关键词信息]
     *          获取插件配置  \$config = Amango_Addons_Config();
     *          微信回复      \$this->assign(类型,\$article);  类型[Text Dantw  Duotw]
     *                        \$this->display();
     */
    //插件微信处理默认入口
	public function index(){
        wx_success('Hello World!这是{$data['info']['title']}的微信Bundle！');
	}
    {$autotagaction}
    public function run(){
        return true;
    }
    //插件展示微信TAG方法
    public function showTips(){
        return '';
    }
}

str;
            file_put_contents("{$addon_dir}Controller/WeixinController.class.php", $weixinController);
        }
        if($data['has_config'] == 1)
            file_put_contents("{$addon_dir}config.php", $data['config']);

        $this->success('创建成功',U('index'));
    }

    /**
     * 插件列表
     */
    public function index(){
        $this->meta_title = '插件列表';
        $list       =   D('Addons')->getList();
        $request    =   (array)I('request.');
        $total      =   $list? count($list) : 1 ;
        $listRows   =   C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $page       =   new \Think\Page($total, $listRows, $request);
        $voList     =   array_slice($list, $page->firstRow, $page->listRows);
        $p          =   $page->show();
        $this->assign('_list', $voList);
        $this->assign('_page', $p? $p: '');
        // 记录当前列表页的cookie
        if(file_exists('./Update/update.sql')){
            $this->assign('newversion','1');
        }
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('version',C('AMANGO_VERSION'));
        $this->display();
    }
    
    public function sysupdate(){
        //SQL更新
        $update_sql = './Update/update.sql';
        if (file_exists($update_sql)) {
            execute_sql_file($update_sql);
        }
        $this->success('系统更新完成',U('index'));
    }
    /**
     * 插件后台显示页面
     * @param string $name 插件名
     */
    public function adminList($name){
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('name', $name);
        $class = get_addon_class($name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if(!$param)
            $this->error('插件列表信息不正确');
        $this->meta_title = $addon->info['title'];
        extract($param);
        $this->assign('title', $addon->info['title']);
        $this->assign($param);
        
        if(!isset($fields))
            $fields = '*';
        if(!isset($search_key))
            $key = 'title';
        else
            $key = $search_key;
        if(isset($model)){
            $model  =   D("Addons://{$name}/{$model}");
            // 条件搜索
            $map    =   array(); 
            if(isset($_REQUEST[$key])){
                $map[$key] = array('like', '%'.$_GET[$key].'%');
                $this->assign('searchtitle', $_GET[$key]);
                unset($_REQUEST[$key]);
            }
            foreach($_REQUEST as $name=>$val){
                if(in_array($name, $fields)){
                    $map[$name] = $val;
                }
            }
            if($fields == '*'){
                $fields = $model->getDbFields();
            }

            if(!isset($order))  $order = '';
            $list = $this->lists($model->field($fields),$map,$order);
            $fields = array();
            foreach ($list_grid as &$value) {
                // 字段:标题:链接
                $val = explode(':', $value);
                // 支持多个字段显示
                $field = explode(',', $val[0]);
                $value = array('field' => $field, 'title' => $val[1]);
                if(isset($val[2])){
                    // 链接信息
                    $value['href'] = $val[2];
                    // 搜索链接信息中的字段信息
                    preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
                }
                if(strpos($val[1],'|')){
                    // 显示格式定义
                    list($value['title'],$value['format']) = explode('|',$val[1]);
                }
                foreach($field as $val){
                    $array = explode('|',$val);
                    $fields[] = $array[0];
                }
            }
            $this->assign('model', $model->model);
            $this->assign('list_grid', $list_grid);
        }
        $this->assign('_list', $list);
        if($addon->custom_adminlist)
            $this->assign('custom_adminlist', $this->fetch($addon->addon_path.$addon->custom_adminlist));
        $this->display('adminlist');
    }
    /**
     * 启用插件
     */
    public function enable(){
        $id     =   I('id');
        $msg    =   array('success'=>'启用成功', 'error'=>'启用失败');
        S('hooks', null);
        $this->resume('Addons', "id={$id}", $msg);
    }

    /**
     * 禁用插件
     */
    public function disable(){
        $id     =   I('id');
        $msg    =   array('success'=>'禁用成功', 'error'=>'禁用失败');
        S('hooks', null);
        $this->forbid('Addons', "id={$id}", $msg);
    }

    /**
     * 设置插件页面
     */
    public function config(){
        $id     =   (int)I('id');
        $addon  =   M('Addons')->find($id);
        if(!$addon)
            $this->error('插件未安装');
        $addon_class = get_addon_class($addon['name']);
        if(!class_exists($addon_class))
            trace("插件{$addon['name']}无法实例化,",'ADDONS','ERR');
        $data  =   new $addon_class;
        $addon['addon_path'] = $data->addon_path;
        $addon['custom_config'] = $data->custom_config;
        $this->meta_title   =   '设置插件-'.$data->info['title'];
        $db_config = $addon['config'];
        $addon['config'] = include $data->config_file;
        if($db_config){
            $db_config = json_decode($db_config, true);
            foreach ($addon['config'] as $key => $value) {
                if($value['type'] != 'group'){
                    $addon['config'][$key]['value'] = $db_config[$key];
                }else{
                    foreach ($value['options'] as $gourp => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                        }
                    }
                }
            }
        }
        $this->assign('data',$addon);
        if($addon['custom_config'])
            $this->assign('custom_config', $this->fetch($addon['addon_path'].$addon['custom_config']));
        $this->display();
    }

    /**
     * 保存插件设置
     */
    public function saveConfig(){
        $id     =   (int)I('id');
        $config =   I('config');
        $flag = M('Addons')->where("id={$id}")->setField('config',json_encode($config));
        if($flag !== false){
            $this->success('保存成功', Cookie('__forward__'));
        }else{
            $this->error('保存失败');
        }
    }

    /**
     * 安装插件
     */
    public function install(){
        $addon_name     =   trim(I('addon_name'));
        $class          =   get_addon_class($addon_name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addons  =   new $class;
        $info = $addons->info;
        if(!$info || !$addons->checkInfo())//检测信息的正确性
            $this->error('插件信息缺失');
        session('addons_install_error',null);
        $install_flag   =   $addons->install();
        if(!$install_flag){
            $this->error('执行插件预安装操作失败'.session('addons_install_error'));
        }
        $addonsModel    =   D('Addons');
        $data           =   $addonsModel->create($info);
        if(is_array($addons->admin_list) && $addons->admin_list !== array()){
            $data['has_adminlist'] = 1;
        }else{
            $data['has_adminlist'] = 0;
        }
        if(!$data){
            $this->error($addonsModel->getError());
        }
        //执行安装ID
        $addonsid = $addonsModel->add($data);
        if($addonsid){
            $config         =   array('config'=>json_encode($addons->getConfig()));
            $addonsModel->where("name='{$addon_name}'")->save($config);
            $hooks_update   =   D('Hooks')->updateHooks($addon_name);
            if($hooks_update){
                S('hooks', null);
                //植入芒果TAG
                if(!empty($info['amangotag'])){
	                $tagname = 'addontag_'.$addon_name;
	                $Tagslists = M('Tagslists');
		                foreach ($info['amangotag'] as $key => $value) {
		                    $tagdata['tagslists_title']   = $tagname;
		                    $tagdata['tagslists_group']   = $key;
		                    $tagdata['tagslists_type']    = 'addon';
		                    if(substr_count($value,'/')>0){
		                        $sel = $value;
		                    } else {
		                        $sel = 'Weixin/'.$value;
		                    }
		                    $tagdata['tagslists_action']  = $addon_name.'/'.$sel;
		                    $tagdata['tagslists_description']  = $info['title'].'插件的TAG';
		                    $Tagslists->add($tagdata);
		                    unset($tagdata);
		                }
                }
                //自动处理微信关键词
                $postlist     = $info['weixinkeyword']['post'];
                $responselist = $info['weixinkeyword']['response'];
                $grouplist    = $info['weixinkeyword']['group'];

                if((string)$info['weixin']=='1'&&!empty($postlist)&&!empty($responselist)){
                    $postmodel     = D('Weixinkeyword'); $post_model = M('Keyword');$response_model = M('Response');

                    $postgroup     = array();
                    $responsegroup = array();
                    //TODO 新增关键词分组
                    //新增POST请求
                    foreach ($postlist as $key => $value) {
                        $postdata = array();
                        if(!empty($value['keyword_rules'])&&!empty($value['keyword_post'])){
                            $postdata = $postmodel->create_post($value);
                            if(is_array($postdata)){
                                //TODO 判断是否已经存在 更新OR新增
                                $datanum  = $post_model->add($postdata);
                                if($datanum>0){
                                    $postgroup[$key] = $datanum;
                                }
                            }
                        }
                    }
                    //新增RESPONSE响应
                    foreach ($responselist as $k => $v) {
                        if(!empty($v['response_name'])){
                            $v['apiid'] = $addonsid;
                            $responsedata = $postmodel->create_response($v);
                            if(is_array($responsedata)){
                                //TODO 判断是否存在 更新OR新增 s:4:"type";s:5:"local";s:3:"num";i:1;s:6:"neiron";i:21;
                                $responsenum  = $response_model->add($responsedata);
                                if($responsenum>0){
                                    $responsegroup[$k] = $responsenum;
                                }
                            }
                        }
                    }
                    //自动组装关键词组
                    if(!empty($grouplist)){
                        foreach ($grouplist as $key => $value) {
                            if(isset($postgroup[$key])&&isset($responsegroup[$value])){
                                $post_model->where(array('id' => $postgroup[$key]))->save(array('keyword_reaponse' => $responsegroup[$value]));
                            }
                        }
                    }
                }

                $this->success('插件安装成功');
            }else{
                $addonsModel->where("name='{$addon_name}'")->delete();
                $this->error('更新钩子处插件失败,请卸载后尝试重新安装');
            }

        }else{
            $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(){
        $addonsModel    =   M('Addons');
        $id             =   trim(I('id'));
        $db_addons      =   $addonsModel->find($id);
        $class          =   get_addon_class($db_addons['name']);
        $this->assign('jumpUrl',U('index'));
        if(!$db_addons || !class_exists($class))
            $this->error('插件不存在');
        session('addons_uninstall_error',null);
        $addons =   new $class;
        $uninstall_flag =   $addons->uninstall();
        if(!$uninstall_flag)
            $this->error('执行插件预卸载操作失败'.session('addons_uninstall_error'));
        $hooks_update   =   D('Hooks')->removeHooks($db_addons['name']);
        if($hooks_update === false){
            $this->error('卸载插件所挂载的钩子数据失败');
        }
        S('hooks', null);
        //TODO 卸载相关关键词
        $delete = $addonsModel->where("name='{$db_addons['name']}'")->delete();
        if($delete === false){
            $this->error('卸载插件失败');
        }else{
            //卸载所有有关TAG
            $Tagslists = M('Tagslists');
            $tagtitle  = 'addontag_'.$db_addons['name'];
            $condition['tagslists_title']  = $tagtitle;
            $Tagslists->where($condition)->delete();
            $this->success('卸载成功');
        }
    }

    /**
     * 钩子列表
     */
    public function hooks(){
        $this->meta_title   =   '钩子列表';
        $map    =   $fields =   array();
        $list   =   $this->lists(D("Hooks")->field($fields),$map);
        int_to_string($list, array('type'=>C('HOOKS_TYPE')));
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list', $list );
        $this->display();
    }

    public function addhook(){
        $this->assign('data', null);
        $this->meta_title = '新增钩子';
        $this->display('edithook');
    }

    //钩子出编辑挂载插件页面
    public function edithook($id){
        $hook = M('Hooks')->field(true)->find($id);
        $this->assign('data',$hook);
        $this->meta_title = '编辑钩子';
        $this->display('edithook');
    }

    //超级管理员删除钩子
    public function delhook($id){
        if(M('Hooks')->delete($id) !== false){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function updateHook(){
        $hookModel  =   D('Hooks');
        $data       =   $hookModel->create();
        if($data){
            if($data['id']){
                $flag = $hookModel->save($data);
                if($flag !== false)
                    $this->success('更新成功', Cookie('__forward__'));
                else
                    $this->error('更新失败');
            }else{
                $flag = $hookModel->add($data);
                if($flag)
                    $this->success('新增成功', Cookie('__forward__'));
                else
                    $this->error('新增失败');
            }
        }else{
            $this->error($hookModel->getError());
        }
    }

    public function execute($_addons = null, $_controller = null, $_action = null){
        if(C('URL_CASE_INSENSITIVE')){
            $_addons        =   ucfirst(parse_name($_addons, 1));
            $_controller    =   parse_name($_controller,1);
        }

        if(!empty($_addons) && !empty($_controller) && !empty($_action)){
            define ( 'ADDON_PUBLIC_PATH', __ROOT__ . '/Addons/' . $_addons . '/View/default/Public' );
            //资源路径
            $publicurl = __ROOT__.'/Addons/'.$_addons.'/Public';
            defined ( 'ADDON_PUBLIC' ) or define ( 'ADDON_PUBLIC', $publicurl );
            defined ( '__ADDONROOT__' ) or define ( '__ADDONROOT__', $publicurl );
            defined ( 'ADDON_ROOT' ) or define ( 'ADDON_ROOT', __ROOT__ . '/Addons/' . $_addons .'/' );
            
            defined ( '_ADDONS' ) or define ( '_ADDONS', $_addons );
            defined ( '_CONTROLLER' ) or define ( '_CONTROLLER', $_controller );
            defined ( '_ACTION' ) or define ( '_ACTION', $_action );
            $TMPL_PARSE_STRING = C('TMPL_PARSE_STRING');
            $TMPL_PARSE_STRING['__ADDONROOT__'] = $publicurl;
            $TMPL_PARSE_STRING['ADDON_PUBLIC']  = $publicurl;
            C('TMPL_PARSE_STRING', $TMPL_PARSE_STRING);
            $Addons = A("Addons://{$_addons}/{$_controller}")->$_action();
        } else {
            $this->error('没有指定插件名称，控制器或操作！');
        }
    }

    public function edit($name, $id = 0){
        $this->assign('name', $name);
        $class = get_addon_class($name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if(!$param)
            $this->error('插件列表信息不正确');
        extract($param);
        $this->assign('title', $addon->info['title']);
        if(isset($model)){
            $addonModel = D("Addons://{$name}/{$model}");
            if(!$addonModel)
                $this->error('模型无法实列化');
            $model = $addonModel->model;
            $this->assign('model', $model);
        }
        if($id){
            $data = $addonModel->find($id);
            $data || $this->error('数据不存在！');
            $this->assign('data', $data);
        }

        if(IS_POST){
            // 获取模型的字段信息
            if(!$addonModel->create())
                $this->error($addonModel->getError());

            if($id){
                $flag = $addonModel->save();
                if($flag !== false)
                    $this->success("编辑{$model['title']}成功！", Cookie('__forward__'));
                else
                    $this->error($addonModel->getError());
            }else{
                $flag = $addonModel->add();
                if($flag)
                    $this->success("添加{$model['title']}成功！", Cookie('__forward__'));
            }
            $this->error($addonModel->getError());
        } else {
            $fields = $addonModel->_fields;
            $this->assign('fields', $fields);
            $this->meta_title = $id? '编辑'.$model['title']:'新增'.$model['title'];
            if($id)
                $template = $model['template_edit']? $model['template_edit']: '';
            else
                $template = $model['template_add']? $model['template_add']: '';
            if ($template)
                $this->display($addon->addon_path . $template);
            else
                $this->display();
        }
    }

    public function addonsshop($addons = 'addons', $type = 'free', $heat = null){
        //插件种类
        $addoninfo = array(
                'addons' => 'http://bbs.amango.net/api.php?mod=js&bid=3',
                'unit'   => 'http://bbs.amango.net/api.php?mod=js&bid=4',
                'theme'  => 'http://bbs.amango.net/api.php?mod=js&bid=5',
        );

        
        //获取官方所有插件
        $ch = curl_init(); // 初始化curl
        curl_setopt( $ch, CURLOPT_POST, 0 ); // 设置为GET方式
        curl_setopt( $ch, CURLOPT_URL, $addoninfo[$addons] ); // 设置链接
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // 设置是否返回信息
        $response   = curl_exec ( $ch ); // 接收返回信息
        $addonslist = array();
        $addonslist = self::setAddons($response);

        //按照付费类型排序
        $res['status'] = empty($addonslist) ? 0 : 1;
        $res['msg']    = empty($addonslist) ? '暂无相关插件' : $addonslist;
        if(IS_AJAX){
            $this->ajaxReturn($res);
        } else {
            $this->assign('shoplist',$addonslist);
            $this->display();
        }
    }
    protected function setAddons($response){
        $addonslist     = array();
        $addonsnewlist  = array();
        $item           = array();
        $detail         = array();
        $addonslist = explode('<;>', $response);
        foreach ($addonslist as $key => $value) {
            if(!empty($value)){
                //总体信息
                $item   = explode('<|>', $value);
                //详细信息
                $detail = explode('-', $item[3]);
                //判断本地文件是否存在 ONETHINK_ADDON_PATH  判断是否已经安装
                $addonsname = $detail[1];
                if(!file_exists(ONETHINK_ADDON_PATH.$addonsname)){
                    $addonsnewlist[$key] = array( 
                        'is_free'    => (false===strpos($detail[0],'【免费】')) ? 0 : 1,
                        'name'       => str_replace('【免费】', '', $detail[0]),
                        'statusname' => '免费下载',
                        'title'      => $detail[1],
                        'vesion'     => $detail[2],
                        'picurl'     => 'http://bbs.amango.net/'.$item[1],
                        'views'      => $item[0],
                        'url'        => 'http://bbs.amango.net/'.$item[2],
                        'author'     => $detail[3],
                        'desc'       => $item[4],
                    );
                } else {
                    $newpath    = '\Addons\\'.$addonsname.'\\'.$addonsname.'Addon';
                    $haveconfig = new $newpath;
                    $oldversion = $haveconfig->info['version'];
                    if($oldversion<$detail[2]){
                        $addonsnewlist[$key] = array( 
                            'is_free'    => (false===strpos($detail[0],'【免费】')) ? 0 : 1,
                            'name'       => str_replace('【免费】', '', $detail[0]),
                            'statusname' => '更新下载',
                            'title'      => $detail[1],
                            'vesion'     => $detail[2],
                            'picurl'     => 'http://bbs.amango.net/'.$item[1],
                            'views'      => $item[0],
                            'url'        => 'http://bbs.amango.net/'.$item[2],
                            'author'     => $detail[3],
                            'desc'       => $item[4],
                        );
                    }
                }
                unset($item);unset($detail);
            }
        }
        unset($addonsnewlist[count($addonsnewlist)-1]);
        return $addonsnewlist;
    }
    public function del($id = '', $name){
        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $class = get_addon_class($name);
        if(!class_exists($class))
            $this->error('插件不存在');
        $addon = new $class();
        $param = $addon->admin_list;
        if(!$param)
            $this->error('插件列表信息不正确');
        extract($param);
        if(isset($model)){
            $addonModel = D("Addons://{$name}/{$model}");
            if(!$addonModel)
                $this->error('模型无法实列化');
        }

        $map = array('id' => array('in', $ids) );
        if($addonModel->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
