<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category();

		//频道页只显示模板，默认不读取任何内容
		//内容可以通过模板标签自行定制
        /* 芒果微信分享信息   */
        $shareurl  = Amango_U('Index/index');
        $Shareinfo = array(
					'ImgUrl'     =>'',
					'TimeLink'   =>$shareurl,
					'FriendLink' =>$shareurl,
					'WeiboLink'  =>$shareurl,
					'tTitle'     =>'欢迎来到资讯中心',
					'tContent'   =>'欢迎来到资讯中心',
					'fTitle'     =>'欢迎来到资讯中心',
					'fContent'   =>'欢迎来到资讯中心',
					'wContent'   =>'欢迎来到资讯中心'
        	        );
        $this->assign('Share',$Shareinfo);
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->display($category['template_index']);
	}

	/* 文档模型列表页 */
	public function lists($p = 1){
		/* 分类信息 */
        $category = $this->category();
        $pid      =   I('pid',0);
		/* 获取当前分类列表 */
        $param = array(
			'p'           =>$p,
			'$categoryid' =>$category,
			'list_row'    =>'',
			'extend'      =>true
        );
		$newlist  = api('Document/get_list',$param);
        /* 芒果微信分享信息   */
        $shareurl    = U('Article/lists',array('category'=>$category['name']),'',TRUE);
        $description = empty($category['description']) ? '每天给你提供最优质的内容' : $category['description'];
        $description = "【".$category['title']."】".$description;
        $Shareinfo = array(
					'ImgUrl'     =>'',
					'TimeLink'   =>$shareurl,
					'FriendLink' =>$shareurl,
					'WeiboLink'  =>$shareurl,
					'tTitle'     =>$description,
					'tContent'   =>$description,
					'fTitle'     =>$description,
					'fContent'   =>$description,
					'wContent'   =>$description
        	        );
        /* 芒果微信自动生成发表菜单 description icon*/ 
        //自动生成发表字段
        $this->assign('fields', self::getHomefields($newlist[0]['model_id']));
        $this->assign('Share',$Shareinfo);
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('list', $newlist);
        //pid     
        $this->assign('pid', $pid);
        //model_id 
        $this->assign('model_id', $category['model'][0]);
        //cate_id 
        $this->assign('category_id', $category['id']);
        //是否允许发表新
        $this->assign('allow_publish', $category['allow_publish']);
        //公共title 
        $this->assign('Title',$category['title']);
		$this->display($category['template_lists']);
	}

	/* 获取前台发表字段 */
	protected function getHomefields($model_id){
		$model  = get_document_model($model_id);
        $fields = get_model_attribute($model['id']);

		$newfields = array();
		foreach ($fields as $key => $value) {
			foreach ($value as $k => $v) {
				if($v['home_show']==1){
				    $newfields[] = $v;
				}
			}
		}
         return $newfields;
    }
	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		global $_K;
		/* 标识正确性检测 */
		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}

		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$info  = api('Document/get_detail',array('id'=>$id));
		if(false===$info['status']){
			$this->error($info['info']);
		}
        //芒果用户回复 
        $this->assign('model_id', $info['model_id']);
        $model  = get_document_model($info['model_id']);
        $fields = get_model_attribute($model['id']);
		$newfields = array();
			foreach ($fields[1] as $k => $v) {
				if($v['reply_show']==1){
				    $newfields[] = $v;
				}
			}
		/* 分类信息 */
		$category = $this->category($info['category_id']);

		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else { //使用默认模板
			$tmpl = 'Article/'. get_document_model($info['model_id'],'name') .'/detail';
		}

		/* 更新浏览数 */
		$map = array('id' => $id);
		D('Document')->where($map)->setInc('view');

        /* 芒果微信分享信息   */
        $shareurl  = Amango_U('Article/detail?id='.$id);
        $biaoshi   = $info['title']."来自：".C('WEB_SITE_TITLE');
        $Shareinfo = array(
					'ImgUrl'     =>get_cover_pic($info['cover_id']),
					'TimeLink'   =>$shareurl,
					'FriendLink' =>$shareurl,
					'WeiboLink'  =>$shareurl,
					'tTitle'     =>$biaoshi,
					'tContent'   =>$biaoshi,
					'fTitle'     =>$biaoshi,
					'fContent'   =>$biaoshi,
					'wContent'   =>$biaoshi
        	        );
        $this->assign('Share',$Shareinfo);
        //是否允许发表新
        $this->assign('reply', $category['reply']);
        $this->assign('reply_show', $category['reply_show']);
        $this->assign('fields', $newfields);
        //一键关注链接
        $this->assign('accountsub', $_K['DEFAULT']['account_sub']);
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('page', $p); //页码
        //公共title 
        $this->assign('Title',$info['title']);

		$this->display($tmpl);
	}
	/* 获取前台回复字段 */
	protected function getReplyfields($fields){
		$newfields = array();
		foreach ($fields as $key => $value) {
			foreach ($value as $k => $v) {
				if($v['reply']==1){
				    $newfields[] = $v;
				}
			}
		}
         return $newfields;
    }
	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}
    /**
     * 文档  公共添加
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function add(){
    	$this->login();
    	//芒果  前台自助 uid
    	$_POST['uid'] = session('user_auth.uid');
        $category_id  = I('post.category_id',0);
        $model_id     = I('post.model_id',0);
        empty($category_id) && $this->error('参数不能为空！');
        empty($model_id) && $this->error('该分类未绑定模型！');
        //获取参数
        $category = D('Category')->info($category_id);
        //检查该分类是否允许发布
        //$allow_publish = D('Document')->checkCategory($cate_id);
        ($category['allow_publish']!=2) && $this->error('该分类不允许发布内容！');
		/* 保存文档内容 */
		$Document = D('Document');
		$status = $Document->update();

		if($status){
			/* 保存成功，处理插件数据 */
			$this->success('发布成功，请等待审核！', U('Article/lists?category='.$category['name']));
		} else {
			$this->error($Document->getError());
		}
    }

    /**
     * 文档  公共回复  
     * @author ChenDenlu <530003247@vip.qq.com>
     */
    public function reply($id){
    	//$this->login();
    	$_POST['id'] || $this->error('请选择要回复的资讯');
		$Document = D('Document');
		$status = $Document->reply($_POST['id']);
		(true!==$status) ? $this->error($status) : $this->success('回复成功！', U('Article/detail?id='.$id));
    }
}
