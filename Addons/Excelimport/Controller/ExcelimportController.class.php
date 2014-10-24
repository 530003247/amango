<?php

namespace Addons\Excelimport\Controller;
use Home\Controller\AddonsController;

class ExcelimportController extends AddonsController{
	
	public function index(){
		//提交的配置
		$config    = $_POST['config'];
        $filePath  = AMANGO_FILE_ROOT.$config['excel_path'];
        $currentid = ($config['excel_readxls']>=1) ? $config['excel_readxls']-1 : 0;
        $startid   = ($config['excel_currentrow']>=2) ? $config['excel_currentrow'] : 2;

		if($filePath==AMANGO_FILE_ROOT){
            $this->error('请选择文件');
		}
		$xlsparam = parse_config($_POST['config']['excel_param']);
		//导入第三方插件
		Amango_Addons_Import('PHPExcel.php','Excelimport');
		Amango_Addons_Import('PHPExcel/Reader/Excel2007.php','Excelimport');
		
        //自动生成唯一数据表名
        $table_name = 'addonsexcel'.str_replace('_', '', strtolower($config['excel_tablename']));
        $tablename  = C('DB_PREFIX').$table_name;
$sql = <<<sql
				SHOW TABLES LIKE '{$tablename}';
sql;
		$res = M()->query($sql);
		//判断表唯一  以便生成新表
        if(count($res)>0){
            $this->error('该表名已存在,请换个数据表名');
        } else {
        	if(strpos($_POST['config']['excel_param'],':')===false){
                $this->error('生成数据表时,请务必填写字段读取配置,格式：字段名:读取列标识');
        	} else {
        		$fields = '';
        		$field  = array();
                foreach ($xlsparam as $key => $value) {
                	if(!empty($key)){
	                 	$fields .= "`{$key}` text,";
	                 	$field[] = $key;
                	}
                }
                //生成数据表
$sql = <<<sql
				CREATE TABLE IF NOT EXISTS `{$tablename}` (
				`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键' ,
				{$fields}
				PRIMARY KEY (`id`)
				)
				ENGINE=MyISAM
				DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
				CHECKSUM=0
				ROW_FORMAT=DYNAMIC
				DELAY_KEY_WRITE=0
				;
sql;
                $res = M()->execute($sql);
                if($res === false){
                    $this->error('建立数据表出错,请重新建表');
                }
        	}
        }
        //导入数据表名 存入总表
        D('addonsexcel')->add(array('fileds'=>implode(',', $field),'tablename'=>$tablename));
        
		$PHPExcel               = new \PHPExcel(); 
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
		$PHPReader              = new \PHPExcel_Reader_Excel2007(); 
		
		if(!$PHPReader->canRead($filePath)){ 
		    $PHPReader = new \PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)){ 
				$this->error('无法在'.$filePath.'路径下找到该文件');
			} 
		} 

		$PHPExcel               = $PHPReader->load($filePath); 
		/**读取excel文件中的第一个工作表*/ 
		$currentSheet           = $PHPExcel->getSheet($currentid); 
		/**取得最大的列号*/ 
		$allColumn              = $currentSheet->getHighestColumn();
		/**取得一共有多少行*/ 
		$allRow                 = $currentSheet->getHighestRow(); 

        //初始化 
        $rows = array();
        foreach ($xlsparam as $key => $value) {
			for( $currentRow = $startid; $currentRow <= $allRow; $currentRow++){
				$rowinfo = $currentSheet->getCell(strtoupper($value).$currentRow)->getValue();
				$rows[$currentRow][$key] = ($config['excel_parxhtml']==1) ? strip_tags($rowinfo) : $rowinfo;
			}
        }
        //读取出总记录
        foreach ($rows as $key => $value) {
        	M($table_name)->add($value);
        }
        M('addonsexcel')->where(array('tablename'=>$tablename))->save(array('rows'=>count($rows)));
        $this->success('新增成功', U('Addons/adminList',array('name'=>'Excelimport')));
	}
	public function del(){
		$ids   = empty($_GET['id']) ? $_POST['id'] : array($_GET['id']);
		if(empty($ids)){
             $this->error('请选择要删除的导入数据源');
		}
		dump($ids);die;
		$tablename   = array();
		$addonsexcel = M('addonsexcel');
		foreach ($ids as $key => $value) {
			$tablename[] = $addonsexcel->where(array('id'=>$value))->getField('tablename');
			               $addonsexcel->where(array('id'=>$value))->delete();
		}
        $model = M();
foreach ($tablename as $key => $value) {
$sql = <<<sql
				DROP TABLE {$value};
sql;
        $model->execute($sql);
}
    	$this->success('删除导入的数据源成功');
    }
	public function rowslist(){
		$id   = empty($_GET['id']) ? $this->error('请选择要浏览的导入数据源') : $_GET['id'];
        $info = M('addonsexcel')->where(array('id'=>$id))->find();
        $tablename = str_replace(C('DB_PREFIX'), '', $info['tablename']);
        //默认ID主键
        $stringfileds = 'id,'.$info['fileds'];
        $fileds       = explode(',', $stringfileds);

        //$lists     = M($tablename)->select();
        $model = M($tablename);
        $total = $model->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 20;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        $this->assign('_page',$page->show());
        $this->assign('list',$list);
        $this->assign('tablename', ucfirst($tablename));

        $this->assign('fileds', $fileds);
		$this->display();
    }
/***********自定义查询***********/
    public function diyconfig(){
    	if(IS_POST){
    		foreach ($_POST['config'] as $key => $value) {
    			if(!isset($value)){
    				$this->error($key.'任意一项配置都不能为空');
    			}
    		}
    		$model = M('addonsdiychaxun');
    		if(empty($_POST['id'])){
    			$old = $model->where(array('title'=>$_POST['config']['title']))->find();
    		    if(!empty($old)){
    				$this->error('该标识已被使用,请换个英文标识');
    		    }
    		}

    		//解析读取配置
    		$rulesparam = parse_config($_POST['config']['rules']);
    		$rules      = self::parsrules($rulesparam);

			$data['title']     = $_POST['config']['title'];
			$data['rules']     = $rules;
			$data['replytype'] = $_POST['config']['replytype'];
			$data['tpl']       = $_POST['config']['tpl'];
			$data['tableid']   = is_numeric($_POST['config']['tableid']) ? $_POST['config']['tableid'] : $this->error('数据源ID错误');
			$data['cache']     = is_numeric($_POST['config']['cache']) ? $_POST['config']['cache'] : 0;
            if(!empty($_POST['id'])){
            	$model->where(array('title'=>$_POST['id']))->save($data);
            } else {
            	$model->add($data);
            }
            $type = !empty($_POST['id']) ? '编辑' : '新增';
            $this->success($type.'自定义查询规则成功');
    	} else {
	    	$tablelist = M('addonsexcel')->field('id,tablename')->select();
	    	$this->assign('list',$tablelist);
	        $this->display();
    	}
    }
	protected function parsrules($rulesparam){
		if(empty($rulesparam['weixin'])){
            $this->error('头部请按格式输入【weixin:分割符|sql(safe)|and(or)');
		}
		$newrules = array();
		$head     = explode('|', $rulesparam['weixin']);
		//解析头部		
		$newrules['weixinprx'] = empty($head[0]) ? ',' : $head[0];
		$newrules['modeltype'] = (strtolower($head[1])=='sql') ? 'sql' : 'safe';
        $newrules['logic']     = (strtolower($head[2])=='or') ? 'or' : 'and';
        //去除头部为下面合并
        array_shift($rulesparam);
		if($newrules['modeltype']=='sql'){
		    $newrules['condition'] = implode('', $rulesparam);
		} else {
			$fieldrules = array();
			foreach ($rulesparam as $key => $value) {
				$fieldrules[strtolower($key)] = self::parseconditon($value);
			}
			$newrules['condition'] = $fieldrules;
		}
		return serialize($newrules);
    }
	protected function parseconditon($value){
		$info = explode('|', $value);$count = count($info);
		$type = str_replace(' ', '', strtolower($info[0]));
		if($count<2){
			$this->error($value);
		}
		switch ($type) {
			case 'notin':
				return array('not in',$info[1]);
				break;
			default:
				return array($type,$info[1]);
				break;
		}
    }
	public function deldiy(){
		$ids   = empty($_GET['id']) ? $_POST['id'] : array($_GET['id']);
		if(empty($ids)){
             $this->error('请选择要删除的自定义查询规则');
		}
		$tablename   = array();
		$addonsdiychaxun = M('addonsdiychaxun');
		foreach ($ids as $key => $value) {
			$addonsdiychaxun->where(array('title'=>$value))->delete();
		}
    	$this->success('删除导入的自定义查询规则成功');
    }
    public function editdiy(){
    	$id   = empty($_GET['id']) ? $this->error('请选择要浏览的导入数据源') : $_GET['id'];
        $info = M('addonsdiychaxun')->where(array('title'=>$id))->find();
    	$tablelist = M('addonsexcel')->field('id,tablename')->select();

        $info['rules'] = self::configparse($info['rules']);
        $this->assign('id',$id);
    	$this->assign('list',$tablelist);
        $this->assign('info',$info);
		$this->display('diyconfig');
    }
	protected function configparse($rules){
        $arrrules     = unserialize($rules);
        $stringfields = '';
        $string   = "weixin:".$arrrules['weixinprx'].'|'.$arrrules['modeltype'].'|'.$arrrules['logic'];
        if($arrrules['modeltype']=='safe'){
	        foreach ($arrrules['condition'] as $key => $value) {
	        	$stringfields .= "\n".$key.":".implode('|', $value);
	        }
	        $string = $string.$stringfields;
        } else {
            $string = $arrrules['condition'];
        }
            return $string;
    }
    public function diylist(){
        //$lists     = M($tablename)->select();
        $model = M('addonsdiychaxun');
        $total = $model->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 20;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('_page',$page->show());
        $this->assign('list',$list);
		$this->display();
    }
}
