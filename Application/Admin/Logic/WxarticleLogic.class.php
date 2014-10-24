<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Logic;

/**
 * 文档模型子模型 - 文章模型
 */
class WxarticleLogic extends BaseLogic{
 /* 自动验证规则 */
    protected $_validate = array();
    /* 自动完成规则 */
    protected $_auto = array();
    /**
     * 获取模型详细信息
     * @param  integer $id 文档ID
     * @return array       当前模型详细信息
     */
    public function detail($id){
        $data = $this->field(true)->find($id);
        if(!$data){
            $this->error = '获取详细信息出错！';
            return false;
        }
        return $data;
    }
    /**
     * 更新数据
     * @param intger $id
     */
    public function update($id = 0){
        $data = $this->create();
        if(!$data){
            return false;
        }
        //TODO:这儿里可以对$data数据进行处理
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

}
