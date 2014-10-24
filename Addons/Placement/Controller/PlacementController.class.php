<?php
namespace Addons\Placement\Controller;
use Home\Controller\AddonsController;

class PlacementController extends AddonsController{
	public function saveconfig(){
        if(empty($_POST['placement_add'])||!is_numeric($_POST['placement_add'])){
            $this->error('请选择要植入的响应体！');
        }
        $statr = str_replace(':', '', $_POST['placement_start']);
        $end   = str_replace(':', '', $_POST['placement_end']);
        if($statr>=$end){
            $this->error('植入的信息【开始时间】不能大于【结束时间】！');
        }
        $config['placement_start']     = $_POST['placement_start'];
        $config['placement_end']       = $_POST['placement_end'];
        $config['placement_keygroup']  = $_POST['placement_keygroup'];
        $config['placement_usergroup'] = $_POST['placement_usergroup'];
        $config['placement_add']       = $_POST['placement_add'];
        $config['placement_status']    = 1;
        //回复体的类型
        $response_reply = M('Response')->where(array('id'=>$_POST['placement_add']))->getField('response_reply');
        $config['placement_addtype']   = $response_reply;
        $id         =   (int)I('id');
        if(empty($id)){
            $flag = M('Addonsplacement')->add($config);
            $msg  = '新增';
        } else {
            $flag = M('Addonsplacement')->where(array('id'=>$id))->save($config);
            $msg  = '修改';
        }
        if($flag !== false){
            $this->success($msg.'植入信息成功');
        }else{
            $this->error($msg.'植入信息失败');
        }
    }
    public function del(){
        $ids = array_unique((array)I('id',0));
        if ( empty($ids) ) {
            $this->error('请选择要删除的植入信息!');
        }
        $Addonsplacement  = M('Addonsplacement');
        foreach ($ids as $key => $value) {
            $Addonsplacement->where(array('id'=>$value))->delete();
        }
            $this->success('删除植入信息成功!');
    }
    public function detail(){
        $id         =   (int)I('id');
        $placement  =   M('Addonsplacement')->find($id);
        $this->assign('info',$placement);
        $this->display();
    }
}
