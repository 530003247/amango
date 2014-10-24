<?php

namespace Addons\Xitedu\Controller;
use Home\Controller\AddonsController;

class XiteduController extends AddonsController{
	public function del(){
        $ids = array_unique((array)I('id',0));
        if ( empty($ids) ) {
            $this->error('请选择要删除的学生教务信息!');
        }
        $Addonsqginfo  = M('Addonseduinfo');
        $Addonsqgclass = M('Addonsclass');
		foreach ($ids as $key => $value) {
			$xuehao = $Addonsqginfo->where(array('id'=>$value))->getField('xuehao');
			           $Addonsqginfo->where(array('id'=>$value))->delete();
			           $Addonsqgclass->where(array('xuehao'=>$xuehao))->delete();
		}
		    $this->success('删除学生教务信息成功!');
    }
}
