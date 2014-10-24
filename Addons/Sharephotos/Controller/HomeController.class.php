<?php

namespace Addons\Sharephotos\Controller;
use Home\Controller\AddonsController;

class HomeController extends AddonsController{
    /**
     * 开发说明:三大全局变量  global $_W,$_K;            W[网站信息]  K[默认微信公众号信息]
     *                        session('P');              前台用户信息
     *          获取插件配置  get_addon_config($name);
     */
    //插件前台展示页面
    public function index(){
        $config = Amango_Addons_Config('Sharephotos');

        //$list   = M('Addonssharepics')->where(array('status'=>1))->order('rand()')->limit(8)->select();

        $model    = M('Addonssharepics');
        $total    = $model->where(array('status'=>1))->count();
        $listRows = $config['pagenums'] > 0 ? $config['pagenums'] : 5;
        $page     = new \Think\Page($total, $listRows);
        $list     = $model->where(array('status'=>1))->limit($page->firstRow.','.$page->listRows)->select();

        //按照时间顺序奇偶分配左右
        $paixu   = array('left','right');
        $newlist = array();
        foreach ($list as $key => $value) {
            $newlist[$paixu[($key%2)]][] = $value;
        }
        //微信分享设置
        //随机图片分享
        $picinfo   = M('Addonssharepics')->where(array('status'=>1))->order('rand()')->find();
        $sharename = $picinfo['content']."【".$config['title']."】";
        $shareurl  = addons_url('Sharephotos://Home/index',array(),'home');
        $Shareinfo = array(
                    'ImgUrl'     =>get_cover_pic($picinfo['picurl']),
                    'TimeLink'   =>$shareurl,
                    'FriendLink' =>$shareurl,
                    'WeiboLink'  =>$shareurl,
                    'tTitle'     =>$sharename,
                    'tContent'   =>$sharename,
                    'fTitle'     =>$sharename,
                    'fContent'   =>$sharename,
                    'wContent'   =>$sharename
                    );
        $this->assign('Share',$Shareinfo);
        $this->assign('urlhost',str_replace('.html', '', addons_url('Sharephotos://Home/sharepicsajax')));
        $this->assign('info',$config);
        $this->assign('left',$newlist['left']);
        $this->assign('right',$newlist['right']);
        $this->display();
    }
    //插件Tips返回处理 return 数字
    public function sharepicsajax(){
        $config   = Amango_Addons_Config('Sharephotos');
        $model    = M('Addonssharepics');
        $total    = $model->where(array('status'=>1))->count();
        $listRows = ($config['pagenums'] > 0) ? $config['pagenums'] : 5;
        $page     = new \Think\Page($total, $listRows);
        $list     = $model->where(array('status'=>1))->limit($page->firstRow.','.$page->listRows)->select();
        $newlist  = array();
        foreach ($list as $key => $value) {
            $rangdheight = rand(10,20);
            $newlist[]   = array(
               'tid'       => $value['id'],
               'subject'   => $value['content'],
               'author'    => $value['nickname'],
               'views'     => $value['views'],
               'pid'       => $value['sharetime'],
               'thumb'     => $value['picurl'],
               'picHeight' => $rangdheight,
            );
        }
        $this->ajaxReturn($newlist);
    }
    //点赞
    public function setgood(){
        if(is_login()){
            $id = I('id');
            if(!is_numeric($id)||$id==0){
                $this->error("谢谢您的支持！您已点赞过咯");
            }
            $photocookie  = cookie('addons_sharephotos');
            $photo_cookie = array();
            $photo_cookie = explode(',', $photocookie);
            if(in_array($id, $photo_cookie)){
                $this->error("谢谢您的支持！您已点赞过咯");
            }
            $sharepicsmodel = M('Addonssharepics');
            $infoset  = array();
            $info     = $sharepicsmodel->where(array('id'=>$id))->find();
            $infoset  = explode(',', $info['setgood']);
            $userinfo = session('P');
            if(!in_array($userinfo['id'], $infoset)){
                $photo_cookie[] = $id;
                $infoset[]      = $userinfo['id'];
                $photocookie    = implode(',', $photo_cookie);
                cookie('addons_sharephotos',$photocookie);
                $newdata = array('setgood'=>implode(',', $infoset));
                $sharepicsmodel->where(array('id'=>$id))->save($newdata);
                $sharepicsmodel->where(array('id'=>$id))->setInc('views');
                $this->success("谢谢您的支持！您赞了它一下");
            } else {
                $this->error("谢谢您的支持！您已点赞过咯");
            }
        } else {
            $this->error("请在微信窗口中回复“图吧”进入后,狂按拇指点赞吧！");
        }
    }
    //插件Tips返回处理 return 数字
    public function tips(){
        return '';
    }
    //插件首页用户后台
    public function profile(){
        $this->display();
    }
}
