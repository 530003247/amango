<?php

namespace Addons\Sharephotos\Controller;
use Home\Controller\AddonsController;

class SharephotosController extends AddonsController{
	public function del(){
        $ids = array_unique((array)I('id',0));
        if ( empty($ids) ) {
            $this->error('请选择要删除的分享内容!');
        }
        $shrepics = M('Addonssharepics');
		foreach ($ids as $key => $value) {
			$picurl = $shrepics->where(array('id'=>$value))->getField('picurl');
			          $shrepics->where(array('id'=>$value))->delete();
			          unlink($picurl);
		}
		    $this->success('删除分享内容成功!');
    }
	public function setstatus(){
        $ids = array_unique((array)I('id',0));
        if ( empty($ids) ) {
            $this->error('请选择要审核的分享内容!');
        }
        $shrepics = M('Addonssharepics');
		foreach ($ids as $key => $value) {
			$status = $shrepics->where(array('id'=>$value))->getField('status');
			$status = $status+1;
			          $shrepics->where(array('id'=>$value))->save(array('status'=>($status%2)));
		}
		    $this->success('审核分享内容成功!');
    }
}
