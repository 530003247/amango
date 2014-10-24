<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;
/**
 * ThinkCMS 系文档模型标签库
 */
class Amango extends TagLib{
	/**
	 * 定义标签列表
	 * @var array
	 */
	protected $tags   =  array(
		'userinfo'       => array('attr' => 'name,useruid', 'close' => 1),
		'addonsuserlist' => array('attr' => 'name,addons', 'close' => 1),
	);
	//用户个人信息 默认获取 登陆用户
	public function _userinfo($tag, $content){
		$useruid   = $tag['useruid'];
		$name      = $tag['name'];
		$parse  = '<?php ';
		$parse .= '$useruid = \''.$useruid.'\';';
		$parse .= '$__USERIFNO__[0] = empty($useruid) ? session(\'P\'):M(\'Weixinmember\')->where(array(\'id\'=>$useruid))->find();';
		$parse .= ' ?>';
		$parse .= '<volist name="__USERIFNO__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
	//插件  首页用户中心
	public function _addonsuserlist($tag, $content){
		$name      = $tag['name'];
		//获取所有带用户中心
		$profilelist = M('Addons')->where(array('status'=>1,'has_profile'=>1))->select();
		//addons_url('插件名://控制器/操作方法',额外参数数组,所属分组)
        foreach ($profilelist as $key => $value) {
        	$profilelist[$key]['profilename'] = $value['title'];
        }
		$parse   = '<?php ';
		$parse .= '$__ADDONSLIST__ = '.var_export($profilelist, true).';';
		$parse .= ' ?>';
		$parse .= '<volist name="__ADDONSLIST__" id="'. $name .'">';
		$parse .= $content;
		$parse .= '</volist>';
		return $parse;
	}
}