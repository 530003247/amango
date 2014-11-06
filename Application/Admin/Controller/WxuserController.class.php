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
 * 微信关注用户列表
 */
class WxuserController extends AdminController {

    /**
     * 微信用户列表首页
     * @return none
     */
    public function lists(){
        $condition = array();
        $where['fromusername']  = array('like', '%'.$_GET['title'].'%');
        $where['ucusername']    = array('like','%'.$_GET['title'].'%');
        $where['_logic']        = 'or';
        $condition['_complex']  = $where;
        if(isset($_GET['guanzhu'])){
            $condition['follow']  = array('gt',$_GET['guanzhu']);
        }
        $model = D('Weixinmember');
        $total = $model->count();
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 5;
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $list = $model->where($condition)->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        $this->assign('_page',$page->show());
        $this->assign('list',$list);
        $this->assign('meta_title','关注者管理');
        $this->display();
    }

    /**
     * 编辑用户资料
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            //过滤非编辑字段
            foreach ($_POST as $key => $value) {
                if(!in_array($key, array('nickname','cate_group','ucpassword','id','lastkeyword','lastmodel','lastclick'))){
                    unset($_POST[$key]);
                }
            }
            
            $id     = $_POST['id'];unset($_POST['id']);
            $updata = array('id'=>$id,'data'=>$_POST);
            $info   = api('Wxuser/update_info',$updata);
            return (false===$info) ? $this->error('编辑用户信息失败！') : $this->success('编辑用户信息成功！');
        } else {
            $id = I('id');
            if(empty($id)){
                $this->error('请选择要查看的用户');
            }
            $info = array();
            $sex  = array('0'=>'女','1'=>'男');
            
            /* 获取数据 */
            $info = api('Wxuser/get_info',array('id'=>$id ));
            $info['sex'] = $sex[$info['sex']];
            $this->assign('info', $info);
            $this->assign('meta_title','关注者管理');
            $this->display();
        }
    }

    /**
     * 冻结账户
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $wxuser = M('Weixinmember');

        if(is_array($id)){
            foreach ($id as $key => $value) {
                $sql = "update ".C('DB_PREFIX')."weixinmember set status=(status+1)%2 where id='$value'";
                $wxuser->execute($sql);
            }
                $this->success('冻结/解冻用户状态成功！');
        }
            $this->error('删除失败！');
    }
    /**
     * 账号收藏查看
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function ucenter(){
        $type = I('type');
        switch ($type) {
            case 'plus':
                $ucentername = '用户发布中心';
                break;
            case 'marks':
                $ucentername = '用户收藏中心';
                break;
            case 'canyu':
                $ucentername = '用户参与中心';
                break;
            default:
                $this->error('请选择要查看的用户数据类型');
                break;
        }


        $this->assign('ucentertitle',$ucentername);
        $this->assign('meta_title','关注者管理');
        $this->display('ucenterlist');
        // if ( empty($id) ) {
        //     $this->error('请选择要操作的数据!');
        // }

        // $wxuser = M('Weixinmember');

        // if(is_array($id)){
        //     foreach ($id as $key => $value) {
        //         $sql = "update ".C('DB_PREFIX')."weixinmember set status=(status+1)%2 where id='$value'";
        //         $wxuser->execute($sql);
        //     }
        //         $this->success('冻结/解冻用户状态成功！');
        // }
        //     $this->error('删除失败！');
    }
}
