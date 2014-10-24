/**
 * 亿忆网图秀功能JS
 * 
 * 设计思路：
 * 		
 *  相对于电脑网页版瀑布流功能，手机版的不同在于手机屏幕较小，基本只能在水平方向
 * 	排两列！同时，网页版多使用绝对定位来计算各个图片块的问题，使用的是px为单位！
 *  在手机端如果考虑到手机分辨率兼容性的问题，则需要用em做单位，显然在绝对定位时会有麻烦，
 *  因为js获取的都是px单位的值（当然，appcan页面初始化时已经设定了px和em的比例值，如果不嫌麻烦，也可以每次去转换）！
 *  
 *  基于手机浏览的特性和局限，在此使用另外一种设计思路如下：
 *  
 *  将屏幕划分为两栏，各占50%宽，那么在追加图片块时就不用考虑绝对定位的问题，直接在左右每个div里面追加图片块即可！
 *  然后在追加图片块时，首先获取左右两个div的高度，将最近一个图片块追加到高度小的那个即可！
 *  
 * 
 * @author		布衣才子
 * @date		2012-09-20
 * @email		work.jerryliu@gmail.com
 * @qq			394969553
 * @version		v1.0
 * @copyright	copyright 2012-2014	YeeYi.com All Rights Reserved	
 */


/**
 * 
 * 服务器访问地址
 */
var apiHost = "http://www.yeeyi.com/mobile/app_pic.php?jsoncallback=?";

var page = 1;

/**
 * base64 加密对象初始化
 */
var b64 = new Base64();

/**
 * 网络请求函数
 * @param {Object} url  请求地址
 * @param {Object} callback	  回调函数
 */
function xmlHttp(url,callback){
	if(url == ''){
		alert('请求地址不能为空！');
	}else{
		$.getJSON(url,callback);
	}
}

/**
 * 获取活动列表
 */
function  getPicList(){
	var url = apiHost + '&ac=pic';
	page = 1;
	uexWindow.toast("1","5","数据加载中…","0");
	xmlHttp(url,showList);
}

/**
 * 活动列表回调函数，处理返回的数据，显示在界面上
 * @param {Object} items
 */
function showList(items){
	
	var leftPicObj = $("#leftPic");
	var rightPicObj = $("#rightPic");
	
	leftPicObj.html('');
	rightPicObj.html('');
	
	var leftHeight = 0;
	var rightHeight = 0;
	
	for(var i in items){
		var item = items[i];
		var thumb = 'http://yeeyi.com/bbs/data/attachment/forum/mobile/'+item.thumb;
		
		//可以使用图片缓存
		//imgCache('p'+item.tid,thumb);
		
		
		leftHeight = $("#leftPic").height();
		rightHeight = $("#rightPic").height();	
		
		if(leftHeight > rightHeight){
			//如果右侧高度小，则追加到右侧
			var trHead = '<div class="blockRight" onclick="goThreadWindow('+item.tid+')">';
			var trPic = '<img style="min-height:'+item.picHeight+'em" src="'+thumb+'" id="p'+item.tid+'">';
			var trTitle = '<div class="pictitle"><div class="subject">'+b64.decode(item.subject)+'</div>';
			var trAddinfo = '<div class="addinfo"><div class="author">'+b64.decode(item.author)+'</div><div class="view">View('+item.views+')</div> </div></div></div>';
			tr = trHead + trPic + trTitle + trAddinfo;
			rightPicObj.append(tr);
		}else{
			//反之，如果右侧高度大，则追加到左侧
			var trHead = '<div class="blockLeft" onclick="goThreadWindow('+item.tid+')">';
			var trPic = '<img style="min-height:'+item.picHeight+'em" src="'+thumb+'" id="p'+item.tid+'">';
			var trTitle = '<div class="pictitle"><div class="subject">'+b64.decode(item.subject)+'</div>';
			var trAddinfo = '<div class="addinfo"><div class="author">'+b64.decode(item.author)+'</div><div class="view">View('+item.views+')</div> </div></div></div>';
			tr = trHead + trPic + trTitle + trAddinfo;
			leftPicObj.append(tr);
		}
	}
	uexWindow.closeToast();
	
}



/**
 * 获取下一页活动
 */
function  getMorePic(){
	page = page + 1;
	var url = apiHost + '&ac=pic&page='+page;
	$("#nextpage").text("数据加载中……");
	xmlHttp(url,showMoreList);
}

/**
 * 下一页活动列表回调函数
 * @param {Object} items
 */
function showMoreList(items){
	var leftPicObj = $("#leftPic");
	var rightPicObj = $("#rightPic");
	j = 0;
	var leftHeight = 0;
	var rightHeight = 0;
	for(var i in items){
		var item = items[i];
		var thumb = 'http://yeeyi.com/bbs/data/attachment/forum/mobile/'+item.thumb;
		//imgCache('p'+item.tid,thumb);
		var leftHeight = $("#leftPic").height();
		var rightHeight = $("#rightPic").height();	
		if(leftHeight > rightHeight){
			var trHead = '<div class="blockRight" onclick="goThreadWindow('+item.tid+')">';
			var trPic = '<img style="min-height:'+item.picHeight+'em" src="'+thumb+'" id="p'+item.tid+'">';
			var trTitle = '<div class="pictitle"><div class="subject">'+b64.decode(item.subject)+'</div>';
			var trAddinfo = '<div class="addinfo"><div class="author">'+b64.decode(item.author)+'</div><div class="view">View('+item.views+')</div> </div></div></div>';
			tr = trHead + trPic + trTitle + trAddinfo;
			rightPicObj.append(tr);
		}else{
			var trHead = '<div class="blockLeft" onclick="goThreadWindow('+item.tid+')">';
			var trPic = '<img style="min-height:'+item.picHeight+'em" src="'+thumb+'" id="p'+item.tid+'">';
			var trTitle = '<div class="pictitle"><div class="subject">'+b64.decode(item.subject)+'</div>';
			var trAddinfo = '<div class="addinfo"><div class="author">'+b64.decode(item.author)+'</div><div class="view">View('+item.views+')</div> </div></div></div>';
			tr = trHead + trPic + trTitle + trAddinfo;
			leftPicObj.append(tr);
		}
	}
	$("#nextpage").text("查看下8条");
}

/**
 * 图片缓存函数
 * @param {Object} rid	Dom ID
 * @param {Object} imgurl	网络图片路径
 */
function imgCache(rid,imgurl){
	//获取图片后缀
	var extArr = new Array();
	extArr = imgurl.split('.');
	var ext = extArr[extArr.length-1];
	zy_imgcache(rid,imgurl,imgurl,changeImgDom,err,null,ext);
}

/**
 * 缓存失败时的回调函数
 * @param {Object} rid	Dom ID
 * @param {Object} imgurl	网络图片路径
 */
function err(rid,imgurl){
	$('#'+rid).attr('src',imgurl);
}

/**
 * 缓存成功时的回调函数
 * @param {Object} rid
 * @param {Object} imgurl
 */
function changeImgDom(rid,imgurl){
	$('#'+rid).attr('src',imgurl);
}


/**
 * 帖子详情窗口跳转
 * @param {Object} tid
 */
function goThreadWindow(tid){
	//Do Nothing
	//此处就可以处理点击时的跳转了，比如查看详细帖子内容
}
