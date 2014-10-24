<?php

namespace Addons\Neighbours\Controller;
use Home\Controller\AddonsController;

class NeighboursController extends AddonsController{
	public function del(){
		$ids = array_unique((array)I('id',0));
		if(empty($ids)){
             $this->error('请选择要删除的隔壁动态！');
		}
		$map = array('id' => array('in', $ids) );
		if(M('Addonsneighbours')->where($map)->delete()){
            $this->success('删除隔壁动态成功！');
        } else {
            $this->error('删除隔壁动态失败！');
        }
    }
	public function del_yesterday(){
		//获取今日时间戳
		$today = strtotime(date("Y-m-d"));
		$map = array('creattime' => array('lt', $today) );
		if(M('Addonsneighbours')->where($map)->delete()){
            $this->success('清空旧动态成功！');
        } else {
            $this->success('暂无旧动态可清！');
        }
    }     
} 
