<?php
namespace Admin\Controller;

class ThemeController extends AdminController {
    protected $diypagename   = 'Diypages';
    public $theme_files_desc = array(
                'Article'    => '资讯界面,资讯文档模板存放处',
                'Base'       => '基础引用,模板渲染的基础模板文件存放处',
                'Category'   => '文档分类,分类模板存放处',
                'Formfields' => '表单字段,各个表单控件存放处',
                'Index'      => '首页界面',
                'Public'     => '公共调用,适合公共调用的模板存放处',
                'User'       => '用户界面',
                'ASSET'      => '静态资源文件夹,例如 CSS JS 图片等等'
        );
    /**
     * 主题管理首页
     */
    public function index($themes_dir=''){
        if(!$themes_dir)
            $themes_dir =  AMANGO_FILE_ROOT.'/Application/Home/'.C('default_v_layer').'/';
        $dirs = array_map('basename',glob($themes_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($themes_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        //读取已有参数
        $WEB_SITE_THEME = M('Config')->where(array('name'=>'WEB_SITE_THEME'))->getField('value');
        $newthemelist   = array();
        //过滤非英文字符串
        foreach ($dirs as $key => $value) {
            $tpl_info = array();
            if(preg_match('/[^\x00-\x80]/',$value)){ 
                unset($dirs[$key]);
            } else {
                $tpl_info = api('System/getThemeinfo',array('themename'=>$value,'config'=>''));
                //基础信息
                $newthemelist[$key]            = $tpl_info['INFO'];
                $newthemelist[$key]['config']  = $tpl_info['CONFIG'];
                //使用状态 
                if($value!=$WEB_SITE_THEME){
                    $newthemelist[$key]['uninstall'] = 1;
                }
            }
        }
        $this->assign('meta_title','主题管理');
        $this->assign('themes',count($newthemelist));
        $this->assign('_list',$newthemelist);
        $this->display();
    }
    public function install(){
        $themename = I('get.themename');
        if(empty($themename)){
            $this->error('请使用已上传的主题');
        }
        $themepath = AMANGO_FILE_ROOT . '/Application/Home/'.C('default_v_layer').'/'.$themename;
        $themepath = $themepath.'/Config.php';
        if(!file_exists($themepath)){
            $this->error('请使用已上传的主题');
        } else {
            $status = M('Config')->where(array('name'=>'WEB_SITE_THEME'))->save(array('value'=>$themename));
            if($status>0){
                //清除缓存
                deldir("./Runtime/Cache/Admin");
                deldir("./Runtime/Cache/Home");
                deldir("./Runtime/Temp");
                $this->success('切换主题成功！');
            } else {
                $this->error('切换主题失败！');
            }
        }
    }
    public function init_theme($WEB_SITE_THEME){
        $themepath   = AMANGO_FILE_ROOT . '/Application/Home/'.C('default_v_layer').'/'.$WEB_SITE_THEME.'/Config.php';
        $thememparam = include_once($themepath);
        return $thememparam;
    }
    public function edit(){
        $WEB_SITE_THEME = M('Config')->where(array('name'=>'WEB_SITE_THEME'))->getField('value');
        $theme_param    = $this->init_theme($WEB_SITE_THEME);
        $current_path = AMANGO_FILE_ROOT . '/Application/Home/'.C('default_v_layer').'/'.$WEB_SITE_THEME;
        if(!empty($this->diypagename)){
            $diypath  = $current_path.'/'.$this->diypagename.'/';
            if(!is_dir($diypath)){
                create_dir_or_files(array(0=>$diypath));
            }
            $this->assign('diypagepath',$diypath);
        }
         //$current_path      = '.'.__ROOT__ . '/Application/Home/'.C('default_v_layer').'/'.$WEB_SITE_THEME;
        $this->defaultpath = AMANGO_FILE_ROOT . '/Application/Home/'.C('default_v_layer').'/';
        $tree = $this->scan_themes($current_path,$ext='_',$denyfiel='ASSET',$parent='.');
        $this->assign('tree',$tree);
        C('_SYS_GET_THEME_TREE_',true);
        $this->assign('title',$theme_param['INFO']['title']);
        $this->assign('theme_files_desc',$this->theme_files_desc);
        $this->assign('meta_title','主题编辑');
        $this->display();
    }
    public function add(){
        $path = I('get.path');
        echo '添加模板';
    }
    public function tree($tree = null){
        C('_SYS_GET_THEME_TREE_') || $this->_empty();
        $this->assign('tree', $tree);
        $this->display('tree');
    }
    //避免scan函数被禁止
    public function scan_themes($path,$ext,$denyfiel,$parent){
        $files = array();
        $current_path = $path;
        //$current_path = realpath($path);
        if (!file_exists($current_path)) return false;
            if (is_dir($current_path)) {
                if ($handle = opendir($current_path)) {
                    while (false !== ($filename = readdir($handle))) {
                        //判断名字中是否含有中文
                        if(!preg_match('/[^\x00-\x80]/',$filename)){ 
                            if ($filename{0} == '.') continue;
                            //if ($filename == 'ASSET') continue;
                            $file     = $current_path . '/' . $filename;
                            $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (is_dir($file)) {
                                $files[] = array(
                                    'is_dir'   => 1,
                                    'dir_path' => str_replace('/','-',str_replace($this->defaultpath, '', $current_path)),
                                    'filename' => str_replace('.'.$file_ext, '', $filename),
                                    'filetype' => $file_ext,
                                    'filesize' => '',
                                    'datetime' => date('Y-m-d H:i:s', filectime($file)),
                                      $ext     => $this->scan_themes($file,$ext,$denyfiel,$parent),
                                );
                            } else {
                                $nowsize  = filesize($file);
                                $files[] = array(
                                    'is_dir'   => 0,
                                    'dir_path' => str_replace('/','-',str_replace($this->defaultpath, '', $current_path)),
                                    'filename' => str_replace('.'.$file_ext, '', $filename),
                                    'filetype' => $file_ext,
                                    'filesize' => format_bytes($nowsize),
                                    'datetime' => date('Y-m-d H:i:s', filectime($file)),
                                );
                            }
                        }
                    }
                    closedir($handle);
                }
            }
            return $files;
    }
    protected function setFullpath($dir,$filename,$filetype='/'){
        //目录
        if(empty($filetype)){
            $ext = '/';
        } else {
            $ext = '.'.$filetype;
        }
        return AMANGO_FILE_ROOT.'Application/Home/'.C('default_v_layer').'/'.str_replace('-', '/', $dir).'/'.$filename.$ext;
    }
    public function deltpllists(){
        //deldir
        $info    = I('get.');
        $delinfo = $this->setFullpath($info['dir'],$info['filename'],$info['type']);
        //清空文件夹
        if(empty($info['type'])){
            deldir($delinfo);
            rmdir($delinfo);
            $msg = '文件夹';
        } else {
            unlink($delinfo);
            $msg = '文件';
        }
        $this->success('删除模板'.$msg.'成功！');
    }
    public function tpllists(){
        if(IS_POST){
            $info = I('post.');
            if($info['createtype']=='dir'){
                $file_ext = explode('.', $info['createname']);
                if(!empty($file_ext[1])||preg_match('/[^\x00-\x80]/',$info['createname'])){
                    $this->error('文件夹名称应为英文字符和数字');
                }
                $filepath = $this->setFullpath($info['createpath'],$info['createname'],'');
                if(is_dir($filepath)){
                    $this->error('该文件(文件夹)已经存在，请换个标识');
                } else {
                    create_dir_or_files(array(0=>$filepath));
                    $this->success($info['createname'].'文件夹创建成功!路径为：'.$filepath,U('edit'));
                }
            } else {
                import('Common.ORG.Input');
                $content = \Input::getVar($info['content']);
                $file_ext = explode('.', $info['createname']);

                if(count($file_ext)>0){
                    $ext      = end($file_ext);
                    $filename = str_replace('.'.$ext, '', $info['createname']);
                    //创建路径 file_put_contents("{$addon_dir}Model/{$data['info']['name']}Model.class.php", $addonModel);
                    $filepath = $this->setFullpath( $info['createpath'],$filename,$ext);
                    //判断文件是否存在 否则创建
                    if(!file_exists($filepath)){
                        create_dir_or_files(array(0=>$filepath));
                        //是否存在
                        if(!file_exists($filepath)){
                            $this->error('模板文件新建失败，后缀标识为.，请确保文件名请使用非中文和标点，当前路径为合法路径');
                        }
                    }
                    if (!is_writable($filepath)) {
                        $this->error("文件 {$info['createname']} 不可写！");
                    }
                        file_put_contents($filepath, $content);
                        $this->success('创建【'.$info['createname'].'】文件成功！',U('edit'));
                } else {
                    $this->error('请输入有效的文件后缀名！');
                }
            }
        } else {
            $this->assign('meta_title','模板编辑');
            $info = I('get.');
            if(empty($info['type'])){
                $filename = $info['filename'];
                $showpath = str_replace('-', '/', $info['dir']).'/'.$info['filename'];                
                $this->assign('editpath',$showpath);
                $this->assign('title','新增');
                $this->assign('nowpath',$showpath);
            } else {
                $filename = $info['filename'].'.'.$info['type'];
                $showpath = str_replace('-', '/', $info['dir']);

                $filepath = $this->setFullpath($info['dir'],$info['filename'],$info['type']);
                //标签合法
                $content  = file_get_contents($filepath);
                import('Common.ORG.Input');

                $content = \Input::forTarea($content);
                $this->assign('content',$content);

                $this->assign('title','编辑');
                $this->assign('editpath',$showpath);
                $this->assign('nowpath',$showpath.'/'.$filename);
                $this->assign('fileinfo',$filename);
                $this->assign('filetype','file');
            }
                $this->display();
        }
    }
    public function pagelists(){
        //判断是否存在自定义页面文件夹
        $WEB_SITE_THEME = M('Config')->where(array('name'=>'WEB_SITE_THEME'))->getField('value');

        $this->assign('diypath',$WEB_SITE_THEME.'/'.$this->diypagename.'/');
        $this->assign('meta_title','自定义页面列表');
        $this->display();
    }
    public function addpages(){
        //判断是否存在自定义页面文件夹
        $this->display();
    }
    //展示页面
    public function _empty($pagesname){
        echo 2112;
    }
    public function pages($name){
        //展示单页面
        echo '这里是单页面'.$id;
    }
}
