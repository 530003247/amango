<?php
namespace Addons\SnatchTieba\Controller;
use Common\Controller\Bundle;

/**
 * SnatchTieba微信处理Bundle
 */
class WeixinController extends Bundle{
	public function index(){
		//获取该插件配置参数
		$config = Amango_Addons_Config();
		//查看缓存是否存在
		$article = S('ADDONS_SnatchTieba');
        if(empty($article)){
			Amango_Addons_Import('phpQuery/phpQuery.php');
			\phpQuery::$defaultCharset = 'GBK';
	        \phpQuery::newDocumentFile('http://tieba.baidu.com/f?kw='.urlencode($config['tieba_name']).'&fr=ala0'); 
	        $articlecontent = array();
	        $artlist    = \pq(".j_thread_list");
	                foreach($artlist as $li){ 
	                    //获取评论数
	                    $tz_commont = iconv('GBK', 'UTF-8', \pq($li)->find('.threadlist_rep_num')->html());
	                    //获取标题
	                    $tz_title   = iconv('GBK', 'UTF-8', \pq($li)->find('a.j_th_tit')->html());
	                    //获取内容
	                    $tz_content = iconv('GBK', 'UTF-8', \pq($li)->find('.threadlist_abs_onlyline')->html());           
	                    $tz_content = preg_replace('/s/', '', $tz_content);
	                    $tz_content = str_replace('<!---->', '', $tz_content);
	                    //获取链接
	                    $tz_link    = 'http://tieba.baidu.com'.iconv('GBK', 'UTF-8', \pq($li)->find('a.j_th_tit')->attr('href'));  
	                    //获取作者
	                    $tz_author  = strip_tags(iconv('GBK', 'UTF-8', \pq($li)->find('span.tb_icon_author a')->html()));
	                    $tz_author  = preg_replace('/s/', '', $tz_author);
	                    //获取回复者
	                    $tz_reply   =  iconv('GBK', 'UTF-8', \pq($li)->find('span.tb_icon_author_rely a')->html());
	                    //获取回复时间
	                    $tz_replytime  = \pq($li)->find('span.j_reply_data')->text();
	                    $tz_replytime  = preg_replace('/s/', '', $tz_replytime);
	                    //获取图片
	                    $tz_pic     = iconv('GBK', 'UTF-8', \pq($li)->find('img')->attr('original'));
	                    if(!in_array($tz_title, $toptitle)){
	                        $articlecontent['other'][] = array(
	                          'Title'       => (1==$config['tieba_extra']) ? "[".$tz_commont."]".$tz_title."\n".$tz_content."\n作者:".$tz_author."|回复:".$tz_reply."-".$tz_replytime : $tz_title."\n".$tz_content,
	                          'Description' => '',
	                          'PicUrl'      => empty($tz_pic) ? '' : $tz_pic,
	                          'Url'         => $tz_link,
	                        );
	                    }
	                }
	        $allownums = ($config['tieba_nums']>8) ? 8 : $config['tieba_nums'];
	        $allownums = ($allownums>=1) ? $allownums : 1;
	        if($config['tieba_jinghua']==1){
	            $arttoplist = \pq(".thread_top");
	                foreach($arttoplist as $li){ 
	                    //获取评论数
	                    $tz_commont = iconv('GBK', 'UTF-8', \pq($li)->find('.threadlist_rep_num')->html());
	                    //获取标题
	                    $tz_title   = iconv('GBK', 'UTF-8', \pq($li)->find('a.j_th_tit')->html());
	                    //获取链接
	                    $tz_link    = 'http://tieba.baidu.com'.iconv('GBK', 'UTF-8', \pq($li)->find('a.j_th_tit')->attr('href'));  
	                    //获取作者
	                    $tz_author  = strip_tags(iconv('GBK', 'UTF-8', \pq($li)->find('span.tb_icon_author a')->html()));
	                    $toptitle[] = $tz_title; 
	                    $tz_author  = preg_replace('/s/', '', $tz_author);
	                        $articlecontent['top'][]   = array(
	                          'Title'       => "[".$tz_commont."]".$tz_title,
	                          'Description' => '',
	                          'PicUrl'      => empty($tz_pic) ? '' : $tz_pic,
	                          'Url'         => $tz_link,
	                        );
	                }
	                $article = self::havejinghua($articlecontent['top'],$articlecontent['other'],$allownums);
	        } else {
	        	    $article = self::deljinghua($articlecontent['other'],$allownums);
	        }
	        \phpQuery::unloadDocuments();
	        if($config['tieba_cache']>0&&!empty($article)){
	       	    S('ADDONS_SnatchTieba',$article,$config['tieba_cache']);
	        }
        }
        $this->assign('Duotw',$article);
        $this->display();
	}
	//包含精华帖子
	protected function havejinghua($top,$other,$allownums){
		$topnum    = count($top);
		$othernum  = count($other);
		//
		if($topnum==0&&$othernum==0){
            $this->error('Sorry~该贴吧目前挤爆咯~再回复试试');
		}

		$total     = $topnum+$othernum;
		if($allownums>=$total){
            $allownums = $total;
		}
        $twcontent = array();
		$othernum  = ($topnum>=$allownums) ? 0 : $allownums-$topnum;
		if($othernum==0){
	        $i = 0;
	        foreach ($top as $key => $value) {
	            $twcontent[] = $value;
	            if($i==$allownums-1){
	                break;
	            }
	            $i++;
	        }
		} else {
            $twcontent = $top;
	        $i = 0;
	        foreach ($other as $key => $value) {
	            $twcontent[] = $value;
	            if($i==$othernum-1){
	                break;
	            }
	            $i++;
	        }
		}
        return $twcontent;
	}
	//不含精华帖子
	protected function deljinghua($other,$allownums){
		$othernum  = count($other);
		$othernum  = ($othernum>8) ? 8 : $topnum;
		$othernum  = ($othernum>=1) ? $topnum : $this->error('Sorry~该贴吧目前挤爆咯~');

		$allownums = ($othernum>=$allownums) ? $allownums : $othernum;
        $i = 0;
        foreach ($other as $key => $value) {
            $twcontent[] = $value;
            if($i==$allownums-1){
                break;
            }
            $i++;
        }
        return $twcontent;
	}
	public function xmltags(){
        return '这是插件tags';
	}
	public function run(){
        return '';
	}
}
