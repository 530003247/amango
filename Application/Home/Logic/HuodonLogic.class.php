<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Home\Logic;

/**
 * 文档模型子模型 - 文章模型
 */
class HuodonLogic extends BaseLogic{
	/* 自动验证规则 */
	protected $_validate = array(
		array('huodondesc', 'require', '活动介绍不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
		array('huodonjuban', 'require', '活动举办方不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
		array('huodonaddress', 'require', '活动举办地址不能为空！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
		array('huodonextra', 'number', '请填写活动限制参加人数！', self::MUST_VALIDATE , '', self::MODEL_BOTH),
	);

	/* 自动完成规则 */
	protected $_auto = array();

	/**
	 * 新增或添加一条文章详情
	 * @param  number $id 文章ID
	 * @return boolean    true-操作成功，false-操作失败
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function update($id){
		/* 获取文章数据 */ //TODO: 根据不同用户获取允许更改或添加的字段
		$data = $this->create();
		if(!$data){
			return false;
		}
		
		/* 添加或更新数据 */
		if(empty($data['id'])){//新增数据
			$data['id'] = $id;
			$id = $this->add($data);
			if(!$id){
				$this->error = '新增详细内容失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->save($data);
			if(false === $status){
				$this->error = '更新详细内容失败！';
				return false;
			}
		}

		return true;
	}
	/**
	 * 回复文档
	 * @param  number $id 文章ID
	 * @param  [data] [初始化数据]
	 * @return boolean  true-操作成功，false-操作失败
	 */
	public function reply($data){
        return true;
	}
}
