<?php

// 芒果常量定义
const AMANGO_VERSION    = '1.0.131218';

/**
 * 芒果   二次开发 公共函数文件
 * 主要定义系统公共函数库
 */
/**
 * Emoji表情解析
 * @return string
 * @param [string] [$str] [将字符串中的[E537] 这类的emoji标签【仅支持SB Unicode】转化为Unicode值 ]
 *        emoji标签 详情网址: http://punchdrunker.github.io/iOSEmoji/table_html/index.html
 */

function emoji($str) {
        return preg_replace_callback('/\[[a-zA-Z0-9]{4}]/',"emoji_replace",$str);
}
function emoji_replace($matches){
    $strarray = json_decode ( '{"0":"\u'.strtr($matches[0], array('['=>'',']'=>'')).'"}', true );
    return $strarray ['0'];
}
/**
 * @return object
 * @author Kevin
 */
function Amango_U($url='',$vars='',$suffix=true,$domain=false){
    $connum  = substr_count($contentStr,'/');
    $url     = ($connum>1) ? $url : 'Home/'.$url;
    $intvars = is_array($vars) ? $vars : array();
    //自动登陆参数
    $autologin = array(
           'ucusername' => 'P_UCUSERNAME',
           'ucpassword' => 'P_UCPASSWORD',
           'stamp'      => time()
    );
    $newvars = array_merge($intvars,$autologin);
    return U($url,$newvars,$suffix=true,$domain=true);
}

/**
 * 实例化   微信插件文件  
 * @param  目标文件夹  Addons://Addons/Controller
 * @author Kevin
 */
function Amango_Addons($Addons,$Controller,$Action,$Param,$Construct){
    static $_addonnews = array();
    $layer      = C('DEFAULT_C_LAYER');
    $Addons     = ucfirst($Addons);
    $Controller = empty($Controller) ? 'Weixin' : ucfirst($Controller);
    $Action     = empty($Action) ? 'index' : $Action;
    //$class = "Addons://{$Addons}/{$Controller}";
    $class = "Addons\\".$Addons."\\".$layer."\\".$Controller.$layer;
    if(isset($_addonnews[$class])){
        return $_addonnews[$class];
    } else {
        if(class_exists($class)) {
            $addon             =   new $class($Param,$Construct);
            $_addonnews[$class] = $action;
            $addon->$Action();
            return $action;
        }else {
            return false;
        }
    }
}

/**
 * 实例化   微信第三方导入 
 * @param  目标文件夹  类名
 * @author Kevin
 */
function Amango_Addons_Import($extraclass,$addonsname,$file='ORG'){
    $file      = empty($file) ? 'ORG' : $file;
    $addonname = empty($addonsname) ? AMANGO_ADDON_NAME : $addonsname;
    $extrafile = AMANGO_FILE_ROOT.'/Addons/'.ucfirst($addonname).'/'.$file.'/'.$extraclass;
    return require_cache($extrafile);
}

/**
 * 实例化   微信第三方导入 
 * @param  目标文件夹  类名
 * @author Kevin
 */
function Amango_Addons_Config($addonsname){
    $addonname = empty($addonsname) ? AMANGO_ADDON_NAME : ucfirst($addonsname);
    $config    =   array();
    $map['name']    = $addonname;
    $map['status']  = 1;
    $config         =  M('Addons')->where($map)->getField('config');
    return json_decode($config, true);
}

/**
 * 获取动态提醒数目  
 * @author Kevin
 */
function Amango_Tips($type,$addons,$default){
        if($type=='system'){
            $tipsnum = '';
        } else {
            $addonname = ucfirst($addons);
            $has       = M('Addons')->where(array('name'=>$addonname,'status'=>1))->find();
            $model = A("Addons://".$addonname."/Home");
            if(false===method_exists($model, 'tips')){
                return '';
            } else {
                $tipsnum   = empty($has) ? '' : A("Addons://".$addonname."/Home")->tips();
            }
        }
        //设置默认
        if(!empty($default)&&empty($tipsnum)){
            $tipsnum   = $default;
        }
            return ($tipsnum>0) ? $tipsnum : '';
}
/**
 * 目录扫描显示目录名数组
 * @author Kevin
 */
function Amango_Scanfiles($current_path='Picture',$outtype=''){
    $current_path   = (empty($current_path)||$current_path=='Picture') ? C('EDITOR_UPLOAD.rootPath') : $current_path;
    //遍历目录取得文件信息
    $file_list  = array();
    if ($handle = opendir($current_path)) {
        while (false !== ($filename = readdir($handle))) {
            if ($filename{0} == '.') continue;
            $file = $current_path . $filename;
            if (is_dir($file)) {
                $file_list[] = $filename;
            }
        }
        closedir($handle);
        return empty($outtype) ? implode(',', $file_list) : $file_list;
    } else {
        return null;
    }
}
/**
 * WEB
 * 实例化   工厂文件  
 * @param  目标文件夹  Admin://Factory/
 * @return object
 * @author Kevin
 */
function Factory($factory){
    // if(!in_array(strtolower($factory), strtolower(C('AMANGO_FACTORY_ADMIN')))){
    //     return false;
    // }
    $factory = (strtolower($factory)=='basic') ? 'Duotw' : $factory;
	    $name = '\Common\Factory\\'.ucfirst($factory).'Factory';
   		$oFactory =  new $name;
        return $oFactory;
}
//数字对比
function num_AgtB($A,$B){
        $result = ($A>$B) ? true : false;
        return $result;
}
//设置  开始时间
function set_starttime($starttime){
        $starttime = (strtotime($starttime)>0) ? $starttime : time();
        return $starttime;
}
//设置  结束时间
//默认延期 一年
function set_endtime($starttime,$endtime){
        $endtime   = strtotime($endtime);
        $starttime = strtotime($starttime);
        $endtime = (empty($endtime)||$endtime<$starttime) ? $starttime+31536000 : $endtime;
        return $endtime;
}
function check_time($starttime,$endtime){
            $start_time = set_starttime($starttime);
            $endtime    = set_endtime($starttime,$endtime);
            $result     = ($endtime<$start_time) ? false : true;
            return $result;
}
/**
 * WEB
 * 微信回复组合 字符串过滤  
 * @return string
 * @author Kevin
 */
function keyword_replace_text($content){
            $content = strip_tags($content,"<img><a><hr>");
			//第二套方案
			$content = str_replace('<hr />','----------', $content);
			//将转义字符转回
			$content = html_entity_decode($content);
            //去除表单的转义
            $content = stripslashes($content);
			$preg    = '/<img[^>]*?alt="([^"]*)"[^>]*>/';
			$content = preg_replace($preg,'${1}', $content);
            //表情转换
            $content = ex_emotion($content);
			$content = str_replace("\t", '', $content);
			$content = str_replace('<br>', '\n', $content);
			$content = str_replace('<br/>', '\n', $content);
			$content = str_replace('&nbsp;', ' ', $content);
			$content = str_replace('"', "'", $content);
            $content = str_replace("\'", "'", $content);
            //转义字符串
            //$content = addslashes($content);
            return $content;
}
function ex_emotion($str){
    $wxemotion = Array(":微笑"=>"/微笑",":撇嘴"=>"/撇嘴",":色"=>"/色",":发呆"=>"/发呆",":得意"=>"/得意",":流泪"=>"/得意",":害羞"=>"/害羞",":闭嘴"=>"/闭嘴",":睡"=>"/睡",":大哭"=>"/大哭",":尴尬"=>"/尴尬",":发怒"=>"/发怒",":调皮"=>"/调皮",":呲牙"=>"/呲牙",":惊讶"=>"/惊讶",":难过"=>"/难过",":酷"=>"/酷",":冷汗"=>"/冷汗",":抓狂"=>"/抓狂",":吐"=>"/吐",":偷笑"=>"/偷笑",":愉快"=>"/愉快",":白眼"=>"/白眼",":傲慢"=>"/傲慢",":饥饿"=>"/饥饿",":困"=>"/困",":惊恐"=>"/惊恐",":流汗"=>"/流汗",":憨笑"=>"/憨笑",":悠闲"=>"/悠闲",":奋斗"=>"/奋斗",":咒骂"=>"/咒骂",":疑问"=>"/疑问",":嘘"=>"/嘘",":晕"=>"/晕",":疯了"=>"/疯了",":衰"=>"/衰",":骷髅"=>"/骷髅",":敲打"=>"/敲打",":再见"=>"/再见",":擦汗"=>"/擦汗",":抠鼻"=>"/抠鼻","鼓掌"=>"/鼓掌",":糗大了"=>"/糗大了",":坏笑"=>"/坏笑",":左哼哼"=>"/左哼哼",":右哼哼"=>"/右哼哼",":哈欠"=>"/哈欠",":鄙视"=>"/鄙视",":委屈"=>"/委屈",":快哭了"=>"/快哭了",":阴险"=>"/阴险",":亲亲"=>"/亲亲",":吓"=>"/吓",":可怜"=>"/可怜",":菜刀"=>"/菜刀",":西瓜"=>"/西瓜",":啤酒"=>"/啤酒",":篮球"=>"/篮球",":乒乓"=>"/乒乓",":咖啡"=>"/咖啡",":饭"=>"/饭",":猪头"=>"/猪头",":玫瑰"=>"/玫瑰",":凋谢"=>"/凋谢","嘴唇"=>"/嘴唇",":爱心"=>"/爱心",":心碎"=>"/心碎",":蛋糕"=>"/蛋糕",":闪电"=>"/闪电",":炸弹"=>"/炸弹",":刀"=>"/刀",":足球"=>"/足球",":瓢虫"=>"/瓢虫",":便便"=>"/便便",":月亮"=>"/月亮",":太阳"=>"/太阳",":礼物"=>"/礼物",":拥抱"=>"/拥抱",":强"=>"/强",":弱"=>"/弱",":握手"=>"/握手",":胜利"=>"/胜利",":抱拳"=>"/抱拳",":勾引"=>"/勾引",":拳头"=>"/拳头",":差劲"=>"/差劲",":爱你"=>"/爱你",":NO"=>"/NO",":OK"=>"/OK","爱情"=>"/爱情",":飞吻"=>"/飞吻",":跳跳"=>"/跳跳",":发抖"=>"/发抖",":怄火"=>"/怄火",":转圈"=>"/转圈",":磕头"=>"/磕头",":回头"=>"/回头",":跳绳"=>"/跳绳",":投降"=>"/投降",":激动"=>"/激动",":乱舞"=>"/乱舞",":献吻"=>"/献吻",":左太极"=>"/左太极",":右太极"=>"/右太极");
        $str = strtr($str,$wxemotion);
        return $str;
}
/**
 * XML解析成数组 要先判断  
 * @return string
 * @author Kevin
 */
function xml_to_array($xml,$deny='') {
        if($xml instanceof SimpleXMLElement) {
        	$packet = array();
                foreach ($xml as $key => $value) {
                    $packet[strtolower($key)] = (string)$value;
                }
            if(!empty($deny)){
                foreach ($deny as $key => $value) {
                	unset($packet[strtolower($key)]);
                }
            }
            return $packet;
        } else {
        	return null;
        }
}
/**
 * 数组解析成XML
 * @return string
 * @author Kevin
 */
function array_to_xml($packet,$tags='',$pre='') {
        if(is_array($packet)) {

        	$xml = '';
        	foreach ($packet as $key => $value) {
                $titlekey  = ucfirst($key);
        		     $xml .=  creat_amango_tag($tags[$titlekey]['head'],$pre)."<{$titlekey}><![CDATA[".creat_amango_tag($tags[$titlekey]['_before'],$pre).$value.creat_amango_tag($tags[$titlekey]['after_'],$pre)."]]></{$titlekey}>".creat_amango_tag($tags[$titlekey]['end'],$pre);
        	}
            return $xml;
        } else {
        	return null;
        }
}
/**
 * Amango TAG生成器
 * @return string
 * @author Kevin
 */
function creat_amango_tag($tagname,$pre) {
    return empty($tagname) ? '' : '<amango:'.$tagname.'tag'.$pre.'>';
}
/**
 * 数组解析成XML
 * @return string
 * @author Kevin
 */
function is_2array($array) {
        if(is_array($array)) {
        	foreach ($array as $value) {
                if(!is_array($value)){
                     return false;
                } else {
                	foreach ($value as $v) {
                		if(is_array($v)){
                             return false;
                		}
                	}
                }
        	}
            return true;
        } else {
        	return false;
        }
}

/**
 * 配置项解析 换行 :
 * @return string
 * @author Kevin
 */
function parse_config($value,$type=3){
    switch ($type) {
        case 3: //解析数组
        $value = str_replace(' ', '', $value);
            $array = preg_split('/[,;\r\n]+/', trim($value, "=>"/",;\r\n"));
            if(strpos($value,':')){
                $value  = array();
                foreach ($array as $val) {
                    list($k, $v) = explode(':', $val);
                    if($k!=""){
                        $value[$k]   = $v;
                    }
                }
            }else{
                $value =    $array;
            }
            break;
    }
    return $value;
}
//芒果   根据模型ID   获取 该模型的全部字段
function get_fields_modelid($model_id='',$fields=''){
     $fieldslist = M('Attribute')->where(array('status'=>1,'model_id' =>$model_id))->field($fields)->select();
     return $fieldslist;
}
//芒果   根据模型ID   获取 继承模型的全部字段
function get_fields_extendid($model_id='',$fields='*'){
     $extendid = D('Category')->where(array('id' => $model_id))->getField('model');
     $fieldslist = M('Attribute')->where(array('model_id' =>$extendid))->field($fields)->select();
     return $fieldslist;
}
//芒果   获取所有模型
function get_all_model($fields='name,id,title,extend'){
    $modelcate = M('Model')->field('name,id,title,extend')->where(array('status' =>1))->select();
    foreach ($modelcate as $key => $value) {
        if($value['extend']==1){
            $modelcate[$key]['name'] = 'document_'.$value['name'];
        }
    }
     return $modelcate;
}
//芒果  数据模型 父级模型列表List  参数type
function get_flycloud_list($type='local'){
    $parentlist = array();
    $parentlist = M('Flycloud')->where(array('status' => 1,'data_type'=>$type))->field('id,data_table,data_name')->select();
    foreach ($parentlist as $key => $value) {
        $tablename = get_table_name($value['data_table']);
        $nums = M($tablename)->count();
        if(empty($nums)){
             unset($parentlist[$key]);
        } else {
            $parentlist[$key]['nums'] = $nums;
        }
    }
     return $parentlist;
}

//芒果  数据模型 父级资源模型ID  获取目标模型所有记录
function get_infoBymodel($string,$sortfield=''){
    // $order   = array("'\r\n'"=>"/", "'\n'"=>"/", "'\r'"=>"/","'\s+'");//正则匹配回车，换行，空格
    // $replace = array('','','',' ');
    // $string  = preg_replace($order,$replace,$string);
    $where   = is_numeric($string) ? array('id' => $string) : array('data_name' => $string);
    $yinyon    = M('Flycloud')->where($where)->field('data_table,data_fields,data_condition')->find();
    $tablename = M('Model')->where(array('id' => $yinyon['data_table']))->field('name,extend')->find();
    $resulttable = ($tablename['extend']==1) ? 'document_'.$tablename['name'] : $tablename['name'];
    //默认 判断字段中是否存在 status 有的话  默认显示true
    $is_status = M($resulttable)->field('status')->find();
    //TODO  ID 暂时不考虑ID
    $fields   = (false!==strpos($yinyon['data_fields'],'id')) ? $yinyon['data_fields'] : 'id,'.$yinyon['data_fields'];
    // dump($is_status);die;
    if(empty($is_status)){
        $lists       = M($resulttable)->field($fields)->select();
    } else {
        $lists       = M($resulttable)->field($fields)->where(array('status' =>1))->select();
    }
    $newinfo = array();
    //sortfield自定义顺序  value cate text
    if(!empty($sortfield)){
        $newfield   = array();
        $sortfields = explode(',', $sortfield);
        foreach ($lists as $key => $value) {
            foreach ($sortfields as $k => $v) {
                $newfield[$k] = $value[$v];
            }
            unset($lists[$key]);
            $lists[$key] = $newfield;
            unset($newfield);
        }
    }
    foreach ($lists as $key => $value) {
        $newinfo[$key] = array_values($value);
    }
     return $newinfo;
}

//芒果  数据模型   根据父级ID   获取资源[表名  查询字段  查询条件]
function get_table_info($string){
    $order   = array("'\r\n'"=>"/", "'\n'"=>"/", "'\r'"=>"/","'\s+'");//正则匹配回车，换行，空格
    $replace = array('','','',' ');
    $string  = preg_replace($order,$replace,$string);
    $where   = is_numeric($string) ? array('id' => $string) : array('data_name' => $string);
    $yinyon    = D('Flycloud')->where($where)->field('data_table,data_fields,data_condition')->find();
    $tablename = D('Model')->where(array('id' => $yinyon['data_table']))->field('name,extend')->find();
    $resulttable['name']      = ($tablename['extend']==1) ? 'document_'.$tablename['name'] : $tablename['name'];
    $resulttable['fields']    = (false!==strpos($yinyon['data_fields'],'id')) ? $yinyon['data_fields'] : 'id,'.$yinyon['data_fields'];
    $resulttable['condition'] = $yinyon['data_condition'];
     return $resulttable;
}
//芒果  获取资源子项详细信息    cateid 父级资源ID  id单项ID
function get_infoByid($cateid,$id){
    $resulttable = get_table_info($cateid);
    //默认 判断字段中是否存在 status 有的话  默认显示true
    $model     = M($resulttable['name']);
    $is_status = $model->field('status')->find();
    $firfield  = explode(',', $resulttable['fields']);
    //读取字段中  首个字段
    $where     = empty($is_status) ? array($firfield[0]=>$id) : array('status' => 1,$firfield[0]=>$id);
    $lists     = $model->field($resulttable['fields'])->where($where)->find();
     return array_values($lists);
}
 // 先这样处理着  调用数据源
function parse_laiyuan_attr($string,$sortfields='') {
    return get_infoBymodel($string,$sortfields);
}
 // 获取模型继承标识  【用于model 首页】
function get_extend_title($id) {
    //暂时不采用连表查询
    $topid   = D('Model')->where(array('id' => $id))->getField('extend');
    $topname = D('Model')->where(array('id' => $topid))->getField('name');
    $href    = '<a data-id="'.$topid.'" href="'.U('Model/edit',array('id' => $topid)).'/newmango/index.php?s=/Admin/model/edit/id/15.html">'.$topname.'</a>';
    echo $href;
}
// 获取字段显示类型  【用于模型字段管理】
function get_formfield_show($type) {
    $formtype = array('','始终','新增','编辑');
    echo $formtype[$type];
}
// 输出完整链接
function get_full_link($url) {
    $url = (false!==strpos($url, 'http://')) ? $url : HEAD_URL.__ROOT__.$url;
    echo $url;
}
// 默认链接
function default_link($url) {
    $url = empty($url) ? U('Wap/Index/index',array('id' =>'__WAP_SIG__'),'','',true) : $url;
    $url = (false!==strpos($url, 'http://')) ? $url : HEAD_URL.$url;
    return $url;
}
// 完整图片链接
function get_cover_pic($url) {
    $picpath  = explode('/', $url);
    $lastpath = end($picpath);
    $endpath  = explode('.', $lastpath);
    if(empty($endpath[1])){
        $url = $url.'.jpg';
    }
    $url = (false===strpos($url, 'http://')) ? HEAD_URL.$url : $url;
    return $url;
}
// 根据  数据模型ID  帖子ID   根据数据模型读取条件  获取详细内容  
function get_tiezi_info($cateid,$id) {
    $readfields = D('Flycloud')->where(array('data_type' => 'category', 'data_table'=>$cateid))->find();
    //除链接的字段
    $fields     = explode(',', $readfields['data_fields']);
    //链接生成模式
    if(empty($id)){
        return false;
    }
    $data = D('Document')->detail($id);
    
    foreach ($fields as $value) {
        $newdata[] = $data[$value];
    }
    $newdata[3] = Amango_U('Article/detail?id='.$id);
    if(is_numeric($newdata[1])){
        $url        = D('Picture')->where(array('id' => $newdata[1]))->getField('path');
        $newdata[1] = (strpos($url, 'http://')>=0) ? $url : HEAD_URL.__ROOT__.$url;
    }
    return $newdata;
}
//数组替换  【旧】的为模板  用【新】替换【旧】   为空则不替换
function array_to_array($new,$old){
    foreach ($new as $key => $value) {
        if(!empty($value)){
            $old[$key] = $value;
        }
    }
    return $old;
}
//芒果 获取资源详情
function get_sorceID_rand($cateid){
            $lists = api('Category/get_category_list',array('cateid'=>$cateid,'field'=>'l.id','order'=>'l.level DESC,l.id DESC'));
            $nums  = count($lists)-1;
            if($nums>=0){
                return $lists[mt_rand(0,$nums)]['id'];
            } else {
                return false;
            }
}
//芒果 获取
function get_sorceDetail($cateid, $id){
                $readfields = D('Flycloud')->where(array('data_type' => 'category', 'data_table'=>$cateid))->find();
                $fields     = explode(',', $readfields['data_fields']);
                if(!empty($id)){
                    /*获取一条记录的详细数据*/
                    // $document = D('Document');
                    // $data = $document->detail($id);
                    $info = D('Document')->field(true)->find($id);
                    $aimtable = get_document_model($info['model_id'], 'name');
                    $detail   = D('Document'.ucfirst($aimtable))->field(true)->find($id);
                    $data     = array_merge($info,$detail);
                    foreach ($fields as $value) {
                        $newdata[] = $data[$value];
                    }
                    if(is_numeric($newdata[1])){
                        $url        = D('Picture')->where(array('id' => $newdata[1]))->getField('path');
                        $newdata[1] = base64_encode(str_replace('\/', '/', (false!==strpos($url, 'http://')) ? $url : HEAD_URL.__ROOT__.$url));
                    }
                        return $newdata;
                }
}
function addons_auto_logo($addons_name,$addons_logo){
    $logourl = __ROOT__.'/Addons/'.ucfirst($addons_name).'/'.$addons_logo;
    return (empty($addons_name)||empty($addons_logo)) ? __ROOT__.'/Public/Admin/default_addon.jpg' : $logourl;
}
function deldir($dir) {
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } 
    }
  }
  closedir($dh);
}
//芒果 执行SQL
function execute_sql_file($sql_path) {
    // 读取SQL文件
    $sql = file_get_contents ( $sql_path );
    $sql = str_replace ( "\r", "\n", $sql );
    $sql = explode ( ";\n", $sql );
    
    // 替换表前缀
    $orginal = 'amango_';
    $prefix  = C('DB_PREFIX');
    $sql     = str_replace("{$orginal}","{$prefix}",$sql);
    $model   = M();
    // 开始安装
    foreach ( $sql as $value ) {
        $value = trim ( $value );
        if (empty ( $value ))
            continue;
        $res = $model->execute ( $value );
    }
}