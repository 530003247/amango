<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Admin\Logic;

/**
 * 文档模型子模型 - 文章模型
 */
class HuodonLogic extends BaseLogic{
	/* 自动验证规则 */
	protected $_validate = array(
		array('huodondesc', 'require', '活动介绍不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
		array('huodonjuban', 'require', '活动举办方不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
		array('huodonchenban', 'require', '活动承办方不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
		array('huodonaddress', 'require', '活动举办地址不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
	);
	/* 自动完成规则 */
	protected $_auto = array(
		array('huodontime', 'setdate', self::MODEL_BOTH, 'callback'),
	);
    protected function setdate(){
        return (empty($_POST['huodontime'])||strtotime($_POST['huodontime'])<time()) ? time() : strtotime($_POST['huodontime']);

    }

	/**
	 * 新增或添加一条文章详情
	 * @param  number $id 文章ID
	 * @return boolean    true-操作成功，false-操作失败
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function update($id = 0){
		/* 获取文章数据 */
		$data = $this->create();
		if($data === false){
			return false;
		}

		/* 添加或更新数据 */
		if(empty($data['id'])){//新增数据
			$data['id'] = $id;
			$id = $this->add($data);
			if(!$id){
				$this->error = '新增活动内容失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->save($data);
			if(false === $status){
				$this->error = '更新活动内容失败！';
				return false;
			}
		}

		return true;
	}

	/**
	 * 保存为草稿
	 * @return true 成功， false 保存出错
	 * @author huajie <banhuajie@163.com>
	 */
	public function autoSave($id = 0){
		$this->_validate = array();

		/* 获取文章数据 */
		$data = $this->create();
		if(!$data){
			return false;
		}

		/* 添加或更新数据 */
		if(empty($data['id'])){//新增数据
			$data['id'] = $id;
			$id = $this->add($data);
			if(!$id){
				$this->error = '新增活动内容失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->save($data);
			if(false === $status){
				$this->error = '更新活动内容失败！';
				return false;
			}
		}

		return true;
	}

}
