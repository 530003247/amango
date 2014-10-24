<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 后台配置控制器
 * @author ChenDenlu <530003247@vip.qq.com>
 */
class FlycloudController extends AdminController {
    
    public function lists(){
           R('Think/lists',array('model' => 'flycloud','p'=>$_GET['p'],'Think:lists'));
    }

    public function edit(){
        if(IS_POST){
            if(empty($_POST['data_name'])){
                     $this->error('数据名称不能为空哦~有意义的名称有利于以后数据调用');
            }
            if(empty($_POST['data_name'])){
                     $this->error('请选择要调用的数据表');
            }
            if(empty($_POST['field_sort'])){
                     $this->error('请选择表中的对应读取字段');
            }
            $_POST['data_fields'] = implode(',', $_POST['field_sort'][2]);
            unset($_POST['field_sort']);
            //TODO  类型 为图文时候  condition调整为 U函数解析规则
            if($_POST['data_type'] == 'category'){
                $_POST['data_condition'] = $_POST['urlparax'];
            }
            $data  = D('Flycloud')->create();
            $is_ok = D('Flycloud')->save($data);
            if($is_ok){
                $this->success('编辑数据模型成功',U('Flycloud/lists'));
            } else {
                $this->error('编辑数据模型失败');
            }
        } else {
            $info = D('Flycloud')->where(array('id' => $_GET['id']))->find();
            if($info['data_type']=='category'){
                $tree = D('Category')->getTree(0,'id,title,pid,status');
                $cate_list = array();
                foreach ($tree as $key => $val) {
                    if(is_array($val['_'])){
                        foreach ($val['_'] as $v) {
                            if($v['status']==1){
                                $cate_list[] = array('id'=>$v['id'],'name'=>$val['id'],'title'=>$v['title']);
                            }
                        }
                    }
                }
                $this->assign('tablename', $cate_list);
                $basicfield  = get_fields_modelid(1,$fields='*');

                $allfields = $basicfield;
            } else {
                $this->assign('tablename', get_all_model($fields='name,id,title,extend'));
                $allfields = get_fields_modelid($info['data_table'],'id,name,title');
            }
            //if(empty($allfie['model'])){
            // } else {
            //     $allfields = get_fields_modelid(1,'id,name,title');
            // }
            $this->assign('allfields', $allfields);
            $exread = explode(',', $info['data_fields']);
            foreach ($allfields as $key => $value) {
                if(!in_array($value['name'], $exread)){
                   unset($allfields[$key]);
                }
            }
            $this->assign('readfields', $allfields);
            $this->assign('info', $info);
            $this->display();
        }
    }
    /**
     * 新增
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function add(){
        if(IS_POST){
            if(empty($_POST['data_name'])){
                     $this->error('数据名称不能为空哦~有意义的名称有利于以后数据调用');
            }
            if(empty($_POST['data_name'])){
                     $this->error('请选择要调用的数据表');
            }
            if(empty($_POST['field_sort'])){
                     $this->error('请选择表中的对应读取字段');
            }
            $_POST['data_fields'] = implode(',', $_POST['field_sort'][2]);
            unset($_POST['field_sort']);
            //TODO  类型 为图文时候  condition调整为 U函数解析规则
            if($_POST['data_type'] == 'category'){
                $_POST['data_condition'] = $_POST['urlparax'];
            }
            $data  = D('Flycloud')->create();
            $is_ok = D('Flycloud')->add($data);
            if($is_ok){
                $this->success('新增数据模型成功',U('Flycloud/lists'));
            } else {
                $this->error('新增数据模型失败');
            }
        } else {
            $this->assign('tablename', get_all_model($fields='name,id,title,extend'));
            $this->meta_title = '新增菜单';
            $this->display();
        }
    }
    Static Public function cateforlevel($cate,$name='_',$pid=0){
            $arr = array();
            foreach ($cate as $v) {
                if($v['status']==1){
                    if($v['pid']==$pid){
                        $v[$name] = self::cateforlevel($cate,$name,$v['id']);
                        $arr[] = $v;
                    } 
                } 
            }
            return $arr;
    }

    public function del($model = null, $ids=null){
        $model = M('Model')->find($model);
        $model || $this->error('模型不存在！');

        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $Model = M(get_table_name($model['id']));
        $map = array('id' => array('in', $ids) );
        if($Model->where($map)->delete()){
            $this->success('删除数据模型成功',U('Flycloud/lists'));
        } else {
            $this->error('删除数据模型失败！');
        }
    }
    
    /**
     * 动态显示表中字段
     */
    public function ajax_info(){
        if(IS_AJAX){
            if($_POST['cateid']=='local'){
                $data['status'] = 1;
                $modellist = get_all_model($fields='name,id,title,extend');
                foreach ($modellist as $key => $value) {
                    if($value['extend']==0){
                        $newlist[$key] = array_values($value);
                    }
                }
                $data['msg'] = $newlist;
            }
            //$val['id']
            if($_POST['cateid']=='category'){
                $data['status'] = 1;
                $tree = D('Category')->getTree(0,'id,title,pid,status');
                $cate_list = array();
                foreach ($tree as $key => $val) {
                    if(is_array($val['_'])){
                        foreach ($val['_'] as $v) {
                            if($v['status']==1){
                                $cate_list[] = array($val['id'],$v['id'],$v['title']);
                            }
                        }
                    }
                }
                $data['msg'] = $cate_list;
            }
                $this->ajaxReturn($data); 
        }
    }

    /**
     * 动态显示表中字段
     */
    public function getfields(){
        if(IS_AJAX){
            if(I('post.type')=='local'){
                $fields = get_fields_modelid(I('post.cate'),'id,name,title');
                if(empty($fields)){
                        $data['status'] = 0;
                        $data['errmsg'] = '暂无此模型相关字段信息';
                        $this->ajaxReturn($data);
                } else {
                        $data['status'] = 1;
                        $data['msg'] = $fields;
                        $this->ajaxReturn($data);
                }
            }
            if(I('post.type')=='category'){
                $basicfield  = get_fields_modelid(1,$fields='*');
                $extendfield = get_fields_extendid(I('post.cate'),$fields='*');
                $newfields   = array();
                foreach ($basicfield as $key => $value) {
                    $newfields[$key] = array('name' => $value['name'], 'title' => $value['title']);
                }
                foreach ($extendfield as $key => $value) {
                    $newfields[] = array('name' => $value['name'], 'title' => $value['title']);
                }
                        $data['status'] = 1;
                        $data['msg'] = $newfields;
                        $this->ajaxReturn($data);
            }
        }
    }
}