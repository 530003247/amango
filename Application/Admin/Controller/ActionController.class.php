<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 行为控制器
 * @author huajie <banhuajie@163.com>
 */
class ActionController extends AdminController {

    /**
     * 行为日志列表
     * @author huajie <banhuajie@163.com>
     */
    public function actionLog(){
        //获取列表数据
        $map['status']    =   array('gt', -1);
        $list   =   $this->lists('ActionLog', $map);
        int_to_string($list);
        foreach ($list as $key=>$value){
            $model_id                  =   get_document_field($value['model'],"name","id");
            $list[$key]['model_id']    =   $model_id ? $model_id : 0;
        }
        $this->assign('_list', $list);
        $this->meta_title = '行为日志';
        $this->display();
    }

    /**
     * 查看行为日志
     * @author huajie <banhuajie@163.com>
     */
    public function edit($id = 0){
        empty($id) && $this->error('参数错误！');

        $info = M('ActionLog')->field(true)->find($id);

        $this->assign('info', $info);
        $this->meta_title = '查看行为日志';
        $this->display();
    }

    /**
     * 删除日志
     * @param mixed $ids
     * @author huajie <banhuajie@163.com>
     */
    public function remove($ids = 0){
        empty($ids) && $this->error('参数错误！');
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $res = M('ActionLog')->where($map)->delete();
        if($res !== false){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败！');
        }
    }

    /**
     * 清空日志
     */
    public function clear(){
        $res = M('ActionLog')->where('1=1')->delete();
        if($res !== false){
            $this->success('日志清空成功！');
        }else {
            $this->error('日志清空失败！');
        }
    }

    /**
     * 清空日志
     */
    public function delcache(){
        if(IS_AJAX){
            $type = in_array(strtolower(I('type')), array('admin','weixin','home')) ? I('type') : 'all';
            //清除分组模板缓存
            switch (strtolower($type)) {
                case 'admin':
                    if(is_dir("./Runtime/Cache/Admin")){
                            deldir("./Runtime/Cache/Admin"); 
                    }
                    break;
                case 'home':
                    if(is_dir("./Runtime/Cache/Home")){
                            deldir("./Runtime/Cache/Home"); 
                    }
                    break;           
                default:
                    if(is_dir("./Runtime/Cache")){
                            deldir("./Runtime/Cache"); 
                    }    
                    break;
            }
            //清除缓存
            if(is_dir("./Runtime/Temp")){
                    deldir("./Runtime/Temp"); 
            }
            //清除日志
            if(is_dir("./Runtime/Logs")){
                    deldir("./Runtime/Logs"); 
            }
            $this->success('清空缓存成功成功！');
        } else {
            $this->assign('meta_title','缓存管理');
            $this->display();
        }
    }

}
