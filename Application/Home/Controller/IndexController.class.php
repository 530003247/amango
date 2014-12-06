<?php
namespace Home\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {
    //系统首页
    public function index(){
        //如果存在定制页面   跳转到定制页面
        $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);
        /* 芒果微信分享信息   */
        $shareurl  = Amango_U('Index/index');
        $Shareinfo = array(
                    'ImgUrl'     =>'',
                    'TimeLink'   =>$shareurl,
                    'FriendLink' =>$shareurl,
                    'WeiboLink'  =>$shareurl,
                    'tTitle'     =>'同一个芒果,演绎不同的精彩',
                    'tContent'   =>'这里有最新的资讯,最热门的活动,最贴心的服务',
                    'fTitle'     =>'同一个芒果,演绎不同的精彩',
                    'fContent'   =>'这里有最新的资讯,最热门的活动,最贴心的服务',
                    'wContent'   =>'这里有最新的资讯,最热门的活动,最贴心的服务'
                    );
        $this->assign('Share',$Shareinfo);

        $this->assign('category',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',D('Document')->page);//分页
        $display = C('WEB_SITE_DEFAULTINDEX');
        $default = empty($display) ? 'Index/index' : ucfirst($display);
        $this->display($default);
    }
}