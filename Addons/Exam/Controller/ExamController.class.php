<?php

namespace Addons\Exam\Controller;
use Home\Controller\AddonsController;

class ExamController extends AddonsController{
	public $q_typearr = array(
		     'text'   => '书面题',
             'image'  => '看图题',
             'audio'  => '听力题',
             'video'  => '视频题',

		);
	public $a_typearr = array(
		     'select' => '单选',
             'bool'   => '判断',
             'text'   => '填空',
             'checkbox' => '多选',

		);
    public function add_test(){
    	if(IS_POST){
    		$Exammodel = D('Addons://Exam/Addonsexam');
            $data = $Exammodel->create();
            if($Exammodel->getError()){
            	$this->error($Exammodel->getError());
            } else {
            	//判断是否为添加 更新
            	$status = ($data['id'])?$Exammodel->where(array('id'=>$data['id']))->save($data):$Exammodel->add($data);
            	$msg    = ($data['id'])?'编辑':'添加';
            	($status>0)?$this->success($msg.'考卷成功！请在列表页为他添加考题吧',U('Addons/adminList',array('name'=>'Exam'))):$this->error($msg.'考卷失败！请检查相关信息是否正确');
            }
    	} else {
    		$id = I('get.id');
    		if(!empty($id)){
                $info = M('Addonsexam')->where(array('id'=>$id))->find();
                $this->assign('info',$info);

    		}
    		$this->assign('meta_title','添加考卷');
            $this->display();
    	}
    }
    public function edit_test(){
        $this->display('add_test');
    }
    public function sort_question(){
          $mod    = M('Addonsques');
          $mapid  = array('id' => intval($_REQUEST['id']));
          $num    = trim($_REQUEST['num']);
            if(is_numeric($num)){
                $data['paixu'] = $num;
                $result = $mod->where($mapid)->save($data);
            }
                $paixu = $mod->where($mapid)->getField('paixu');
                $this->success($paixu);
    }
    public function add_question(){
    	if(IS_POST){
    		$questioninfo = I('post.');
    		if(empty($questioninfo['group'])){
                $this->error('请选择该考题所属的卷子');  
    		}
    		$id = $questioninfo['id'];unset($questioninfo['id']);
    		$list = array();
    		$Addonsques = D('Addons://Exam/Addonsques');
    		//0排序 1题目描述 2题目类型 3额外参数 4答案类型 5答案选项 6正确答案
    		$list[0] = $questioninfo;
            $status = $Addonsques->check_questions($list);
            if($status[0]=='0'){
                $this->error('考题格式错误！错误位置:'.implode(',', $status[1][1]));
            }
                $status = ($id)?$Addonsques->where(array('id'=>$id))->save($status[1][0]):$Addonsques->add($status[1][0]);
            	$msg    = ($id)?'编辑':'添加';
            	($status>0)?$this->success($msg.'考题成功！请在列表页为他添加考题吧',addons_url('Exam://Exam/list_question',array('id'=>$questioninfo['group']))):$this->error($msg.'考题失败！请检查相关信息是否正确');
    	} else {
    		$group = I('get.group');
    	    $model = M('Addonsexam');
            $testlist = $model->field('title,id')->select();
            $id = I('get.id');
            if(!empty($id)){
                $info = M('Addonsques')->where(array('id'=>$id))->find();
                $this->assign('info',$info);
            }
            $this->assign('list',$testlist);
            $this->assign('group',$group);
            $this->assign('meta_title','添加考题');
            $this->display();
    	}
    }
    public function add_questionall(){
    	if(IS_POST){
			//提交的配置
			$groupid   = $_POST['group'];
	        $filePath  = $_POST['excelpath'];
	        //提取有效path
	        $filePathparam = explode('Uploads', $filePath);
	        $filePath  = './Uploads'.$filePathparam[1];
			if(!file_exists($filePath)){
	            $this->error('请选择文件');
			}
				//导入第三方插件
				Amango_Addons_Import('PHPExcel.php','Exam');
				Amango_Addons_Import('PHPExcel/Reader/Excel2007.php','Exam');

				$PHPExcel               = new \PHPExcel(); 
				/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
				$PHPReader              = new \PHPExcel_Reader_Excel2007(); 
				
				if(!$PHPReader->canRead($filePath)){ 
				    $PHPReader = new \PHPExcel_Reader_Excel5(); 
					if(!$PHPReader->canRead($filePath)){ 
						$this->error('无法在'.$filePath.'路径下找到该文件');
					} 
				} 
                $startid = 1;
				$PHPExcel               = $PHPReader->load($filePath); 
				/**读取excel文件中的第一个 0 工作表*/ 
				$currentSheet           = $PHPExcel->getSheet(0); 
				/**取得最大的列号*/ 
				$allColumn              = $currentSheet->getHighestColumn();
				/**取得一共有多少行*/ 
				$allRow                 = $currentSheet->getHighestRow();

				$xlsparam = array('A','B','C');
        foreach ($xlsparam as $key => $value) {
			for( $currentRow = $startid; $currentRow <= $allRow; $currentRow++){
				if(($currentRow%3)!=0){
					$rowinfo = $currentSheet->getCell(strtoupper($value).$currentRow)->getValue();
					$rows[$currentRow][$key] = $rowinfo;
				}
			}
		}
            require_once(ONETHINK_ADDON_PATH.'Exam/functions.php');
            $new_group = array_chunk($rows,2);
            $total     = count($new_group);
            $newrows   = array();
            $i = 0;
            //答案类型
            $a_typearr = array_flip($this->a_typearr);
            //题目类型
	        $q_typearr = array(
		     '文字'   => 'text',
             '图片'   => 'image',
             '音频'   => 'audio',
             '视频'   => 'video',
		    );
            foreach ($new_group as $key => $value) {
            	$num = $i++;
            	$newrows[]  = array(
					'paixu'       => $total-$num,
					'q_title'     => set_kb($value[0][1]),
					'q_titletype' => set_kb($q_typearr[$value[0][0]]),
					'a_typedata'  => set_kb($value[0][2]),
					'a_type'      => set_kb($a_typearr[$value[1][0]]),
					'a_choices'   => set_kb($value[1][2]),
					'q_right'     => set_kb($value[1][1])
            		);
            }
                $status     = D('Addons://Exam/Addonsques')->check_questions($newrows);
                $Addonsques = M('Addonsques');
	            if($status[0]=='0'){
	                $this->error('考题格式错误！错误位置:'.implode(',', $status[1][1]));
	            }
	            foreach ($status[1] as $key => $value) {
	            	$value['group'] = $groupid;
	            	$Addonsques->add($value);
	            }
                $this->success('Excel批量导入考题成功！',addons_url('Exam://Exam/list_question',array('id'=>$groupid)));
    	} else {
	    	$group = I('get.group');
	    	$model = M('Addonsexam');
	        $testlist = $model->field('title,id')->select();
	        $this->assign('list',$testlist);
	        $this->assign('info',$this->test_info($group));
	        $this->assign('group',$group);
	        $this->display();
    	}
    }
    public function list_question(){
    	$id    = I('get.id');

        $model = D('Addons://Exam/Addonsques');
        $total = $model->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 5;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->where(array('group'=>$id))->limit($page->firstRow.','.$page->listRows)->order('paixu DESC')->select();
        $this->assign('_page',$page->show());
        $this->assign('list',$list);
        $this->assign('info',$this->test_info($id));
    	$this->assign('q_typearr',$this->q_typearr);
    	$this->assign('a_typearr',$this->a_typearr);
        $this->display();
    }
    protected function test_info($id){
		if(!empty($id)&&is_numeric($id)){
            $info = M('Addonsexam')->where(array('id'=>$id))->field('title,id')->find();
            return $info;
		} else {
			return null;
		}
    }
    public function log_test(){
        $model     = M('Addonsexamlog');
        $testmodel = M('Addonsexam');
        $total     = $model->count();
        $listRows  = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 5;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->limit($page->firstRow.','.$page->listRows)->order('addtime DESC')->select();
        $this->assign('_page',$page->show());
		foreach ($list as $key => $value) {
			$list[$key]['title'] = $testmodel->where(array('id'=>$value['testid']))->getField('title');
		}
    	$this->assign('list',$list);
        $this->display();
    }
    public function log_del(){
    	if(I('type')=='all'){
            M('Addonsexamlog')->delete();
            $this->success('考试记录清空成功');
    	}
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要删除的考试记录!');
        }
        $map = array('id' => array('in', $id) );
        if(M('Addonsexamlog')->where($map)->delete()){
            $this->success('考试记录删除成功');
        } else {
            $this->error('考试记录删除失败！');
        }
    }
    public function del_test(){
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要删除的考试记录!');
        }
        $quemodel = M('Addonsexamques');
        $logmodel = M('Addonsexamlog');
        $model    = M('Addonsexam');
        foreach ($id as $key => $value) {
        	//删除考试记录
        	$logmodel->where(array('testid'=>$value))->delete();
        	//删除问题记录
        	$quemodel->where(array('group'=>$value))->delete();
        	//删除原卷
        	$model->where(array('id'=>$value))->delete();
        }
            $this->success('该考卷(包括考试记录,考卷试题)删除成功！');
    }
}
