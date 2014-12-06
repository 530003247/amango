/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

KindEditor.plugin('emoticons', function(K) {
	var self = this, name = 'emoticons',
		path = (self.emoticonsPath || self.pluginsPath + 'emoticons/images/'),
		allowPreview = self.allowPreviewEmoticons === undefined ? true : self.allowPreviewEmoticons,
		currentPageNum = 1;
	self.clickToolbar(name, function() {
		var rows = 5, cols = 9, total = 137, startNum = 0,
			cells = rows * cols, pages = Math.ceil(total / cells),
			colsHalf = Math.floor(cols / 2),
			wrapperDiv = K('<div class="ke-plugin-emoticons"></div>'),
			elements = [],
			menu = self.createMenu({
				name : name,
				beforeRemove : function() {
					removeEvent();
				}
			});
		menu.div.append(wrapperDiv);
		var previewDiv, previewImg;
		if (allowPreview) {
			previewDiv = K('<div class="ke-preview"></div>').css('right', 0);
			previewImg = K('<img class="ke-preview-img" src="' + path + startNum + '.gif" />');
			wrapperDiv.append(previewDiv);
			previewDiv.append(previewImg);
		}
		function bindCellEvent(cell, j, num) {
			if (previewDiv) {
				cell.mouseover(function() {
					if (j > colsHalf) {
						previewDiv.css('left', 0);
						previewDiv.css('right', '');
					} else {
						previewDiv.css('left', '');
						previewDiv.css('right', 0);
					}
					previewImg.attr('src', path + num + '.gif');
					K(this).addClass('ke-on');
				});
			} else {
				cell.mouseover(function() {
					K(this).addClass('ke-on');
				});
			}
			cell.mouseout(function() {
				K(this).removeClass('ke-on');
			});
			cell.click(function(e) {
	//芒果微信修改表情
	            var amango_bq = new Array(":微笑",":撇嘴",":色",":发呆",":得意",":流泪",":害羞",":闭嘴",":睡",":大哭",":尴尬",":发怒",":调皮",":呲牙",":惊讶",":难过",":酷",":冷汗",":抓狂"
				,":吐",":偷笑",":愉快",":白眼",":傲慢",":饥饿",":困",":惊恐",":流汗",":憨笑",":悠闲",":奋斗",":咒骂",":疑问",":嘘",":晕",":疯了",":衰",":骷髅",":敲打",":再见",":擦汗",":抠鼻"
				,"鼓掌",":糗大了",":坏笑",":左哼哼",":右哼哼",":哈欠",":鄙视",":委屈",":快哭了",":阴险",":亲亲",":吓",":可怜",":菜刀",":西瓜",":啤酒",":篮球",":乒乓",":咖啡",":饭",":猪头",":玫瑰",":凋谢"
				,"嘴唇",":爱心",":心碎",":蛋糕",":闪电",":炸弹",":刀",":足球",":瓢虫",":便便",":月亮",":太阳",":礼物",":拥抱",":强",":弱",":握手",":胜利",":抱拳",":勾引",":拳头",":差劲",":爱你",":NO",":OK"
				,"爱情",":飞吻",":跳跳",":发抖",":怄火",":转圈",":磕头",":回头",":跳绳",":投降",":激动",":乱舞",":献吻",":左太极",":右太极"
				,"\\ue21c","\\ue21d","\ue21e","\ue21f","\ue220","\ue221","\ue222","\ue223","\ue224","\ue225","\ue232","\ue233","\ue234","\ue235","\ue236","\ue237","\ue238","\ue239"
				,"\ue24d","\ue24c","\ue212","\ue213","\ue214","\ue22a","\ue22b","\ue229","\ue138","\ue139","\ue217","\ue12e","\ue251","\ue336","\ue337","\ue332","\ue333","\ue334","\ue536","\ue04a","\ue04b","\ue11d","\ue13d","\ue330","\ue103"
				,"\ue104","\ue125","\ue513","\ue252");



			//上半是QQ表情  下半是emoji表情
				
				self.insertHtml('<img src="' + path + num + '.gif" alt="'+amango_bq[num]+'" data="amango"/>').hideMenu().focus();
				e.stop();
			});
		}
		function createEmoticonsTable(pageNum, parentDiv) {
			var table = document.createElement('table');
			parentDiv.append(table);
			if (previewDiv) {
				K(table).mouseover(function() {
					previewDiv.show('block');
				});
				K(table).mouseout(function() {
					previewDiv.hide();
				});
				elements.push(K(table));
			}
			table.className = 'ke-table';
			table.cellPadding = 0;
			table.cellSpacing = 0;
			table.border = 0;
			var num = (pageNum - 1) * cells + startNum;
			for (var i = 0; i < rows; i++) {
				var row = table.insertRow(i);
				for (var j = 0; j < cols; j++) {
					var cell = K(row.insertCell(j));
					cell.addClass('ke-cell');
					bindCellEvent(cell, j, num);
					var span = K('<span class="ke-img"></span>')
						.css('background-position', '-' + (24 * num) + 'px 0px')
						.css('background-image', 'url(' + path + 'static.gif)');
					cell.append(span);
					elements.push(cell);
					num++;
				}
			}
			return table;
		}
		var table = createEmoticonsTable(currentPageNum, wrapperDiv);
		function removeEvent() {
			K.each(elements, function() {
				this.unbind();
			});
		}
		var pageDiv;
		function bindPageEvent(el, pageNum) {
			el.click(function(e) {
				removeEvent();
				table.parentNode.removeChild(table);
				pageDiv.remove();
				table = createEmoticonsTable(pageNum, wrapperDiv);
				createPageTable(pageNum);
				currentPageNum = pageNum;
				e.stop();
			});
		}
		function createPageTable(currentPageNum) {
			pageDiv = K('<div class="ke-page"></div>');
			wrapperDiv.append(pageDiv);
			for (var pageNum = 1; pageNum <= pages; pageNum++) {
				if (currentPageNum !== pageNum) {
					var a = K('<a href="javascript:;">[' + pageNum + ']</a>');
					bindPageEvent(a, pageNum);
					pageDiv.append(a);
					elements.push(a);
				} else {
					pageDiv.append(K('@[' + pageNum + ']'));
				}
				pageDiv.append(K('@&nbsp;'));
			}
		}
		createPageTable(currentPageNum);
	});
});
