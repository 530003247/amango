(function() {
  var defaultShareTitle, uniqueQuestion, weixinData;
  var errorScore = new Array();
  weixinData = window.amangoshare;

  (function() {
    var onBridgeReady;
    onBridgeReady = function() {
      return WeixinJSBridge.on('menu:share:timeline', function(argv) {
        return WeixinJSBridge.invoke('shareTimeline', weixinData, function() {});
      });
    };
    if (document.addEventListener) {
      return document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
      document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
      return document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
  })();
  uniqueQuestion = (function() {
    function uniqueQuestion(dataJson) {
      this.startDiv;
      this.endDiv;
      this.queDiv;
      this.queList;
      this.btnStart;
      this.dataArray = dataJson['queData'];
      this.scoreArray = dataJson['scoreArray'];
      this.fullScore = dataJson['fullScore'];
      this.score = 0;
      this.nowQueIndex = 0;
      this.averageScore;
      this.init();
    }

    uniqueQuestion.prototype.init = function() {
      var _this = this;
      this.startDiv = jQuery(".cBodyStart");
      this.endDiv = jQuery(".cBodyEnd");
      this.queDiv = jQuery(".cBodyQue");
      this.queList = jQuery("#queList");
      this.averageScore = this.fullScore / this.dataArray.length;
      console.log(this.averageScore);
      return this.startDiv.click(function() {
        _this.startDiv.hide();
        return _this.startAnswer();
      });
    };

    uniqueQuestion.prototype.startAnswer = function() {
      this.nowQueIndex = -1;
      return this.changeQue();
    };

    uniqueQuestion.prototype.getWordByIndex = function(index) {
      switch (index) {
        case 0:
          return 'A';
          break;
        case 1:
          return 'B';
          break;
        case 2:
          return 'C';
          break;
        case 3:
          return 'D';
          break;
        case 4:
          return 'E';
          break;
        case 5:
          return 'F';
          break;
      }
    };
    //生成选项列表:  单选，多选，判断，填空
    uniqueQuestion.prototype.createQueDiv = function() {
      var choicesArray, index, item, liHtml, nowDataJson, nowWord, queHtml, title, type, titleStr, _i, _len, typedata, extendhtml='',quesid;
      nowDataJson = this.dataArray[this.nowQueIndex];
      title = nowDataJson['title'];
	  choicesArray = nowDataJson['choices'];
	  type  = nowDataJson['type'];//新增
	  titletype = nowDataJson['titletype'];//新增
	  typedata  =  nowDataJson['typedata'];//新增
	    quesid  =  nowDataJson['quesid'];//新增

	  //新增 题目 音频 图片 视频 链接
      switch (titletype) {
        case 'image':
		  //特有属性 imagealt
		  var imagedivHtml = '';
		      for(var ii=0;ii<typedata.length;++ii){
			      imagedivHtml  = "<p><img src='" + typedata[ii] + "' class='img-rounded img-responsive'></p>";
                  extendhtml   += imagedivHtml;
			   }
          break;
        case 'audio':
		  //特有属性 audio
		  var audioHtml = '';
		      for(var ii=0;ii<typedata.length;++ii){
			      audioHtml   = '<p><audio src="'+typedata[ii]+'" loop="loop" type="audio/mp3" controls="controls"></audio></p>';
                  extendhtml += audioHtml;
			   }
          break;
        case 'video':
		  //特有属性 video
		  var videoHtml = '';
		      for(var ii=0;ii<typedata.length;++ii){
				  //videoHtml  = '<br><iframe height=300 width=100% src="' + typedata[ii] + '" frameborder=0 allowfullscreen></iframe>';
			      videoHtml  = "<p><video src='" + typedata[ii] + "'  controls='controls'></video></p>";
                  extendhtml   += videoHtml;
			   }
          break;
        case 'link':
		  //特有属性 linkurl
          extendhtml  = '<a href="' + typedata + '" target="blank">'+typedata+'</a>';
          break;
        default :
          extendhtml = '';
          break;
      }
	     var questionid = this.nowQueIndex + 1;
	     titleStr = "<strong><var>" + questionid + ".</var>" + title + '</strong>' +extendhtml;
	  queHtml = "<div class='title' id='questitle' questionid='" + quesid + " ' > " + titleStr + "</div><ul id='queList'>";
	  //新增 生成列表类型
      switch (type) {
        case 'bool':
			var boolarr  = ['F','T'];
		    var boolitem = ['错误','正确'];
      for (index = _i = 0, _len = 2; _i < _len; index = ++_i) {
        item    = boolitem[index];
        nowWord = boolarr[index];
        liHtml = "<li><span>" + nowWord + ". " + item + "</span></li>";
        queHtml += liHtml;
      }
          break;
        case 'text':
			var re  = new RegExp('__',"g");
            var arr = title.match(re);
      for (index = _i = 0, _len = arr.length; _i < _len; index = ++_i) {
        liHtml = "<li><div class='form-group'><label for='textid"+(_i+1)+"' class='control-label'>"+(_i+1)+".</label>&nbsp;&nbsp;<input id='textid"+(_i+1)+"'type='text' class='form-control' value='' name='text'></div></li>";
        queHtml += liHtml;
      }
	      queHtml += "<input id='submitanswer' type='submit' class='btn btn-success' datatype='text'  value='下一题'>";
          break;
        case 'checkbox':
      for (index = _i = 0, _len = choicesArray.length; _i < _len; index = ++_i) {
        item = choicesArray[index];
        nowWord = this.getWordByIndex(index);
        liHtml = "<li><span><div class='checkbox'><label><input type='checkbox' class='checkbox' value='" + index + "' name='text'>" + nowWord + ". " + item + " </label></div></span></li>";
        queHtml += liHtml;
      }
	      queHtml += "<input id='submitanswer' class='btn btn-success' type='submit' datatype='checkbox' value='下一题'>";
          break;
        default:
      for (index = _i = 0, _len = choicesArray.length; _i < _len; index = ++_i) {
        item = choicesArray[index];
        nowWord = this.getWordByIndex(index);
        liHtml = "<li><span>" + nowWord + ". " + item + "</span></li>";
        queHtml += liHtml;
      }
          break;
      }
      queHtml += '</ul>';
      this.queDiv.hide();
      this.queDiv.html(queHtml);
      this.queDiv.fadeIn();
      return this.addEvent();
    };
    //答案匹配值
    uniqueQuestion.prototype.ifRightChoice = function(choiceIndex) {
      var nowdatajson;
      nowdatajson = this.dataArray[this.nowQueIndex];
      console.log(choiceIndex, nowdatajson['rightIndex']);
      if (nowdatajson['rightIndex'] == choiceIndex) {
        return true;
      } else {
        return false;
      }
    };

    uniqueQuestion.prototype.addEvent = function() {
      var _this,datatype;
	  //默认监听select 新增按钮 判断提交按钮是否存在
    if(jQuery("#queList #submitanswer").length>0){
		var dataarray = new Array();
	  //判断数据类型 datatype
	  datatype = jQuery("#queList #submitanswer").attr('datatype');
	  this.queList = jQuery("#queList #submitanswer");
	  console.log(this.queList, "quelist");
      _this = this;
      return this.queList.click(function() {
        var nowEle, nowIndex;
        nowEle = jQuery(this);
        nowIndex = nowEle.index();

      switch (datatype) {
        case 'text':
		  $("#queList input[type='text']").each(function(i){
			  dataarray.push(this.value);
          });
		      nowIndex = dataarray.join(',');
          break;
        case 'checkbox':
		  $("#queList input[type='checkbox']:checked").each(function(i){
			  dataarray.push(this.value);
          });
		      nowIndex = dataarray.join(',');
          break;
      }
        nowEle.addClass("active");
        console.log(nowIndex, 'nowIndex');
        if (_this.ifRightChoice(nowIndex)) {
          _this.score += _this.averageScore;
		  console.log("success");
        } else {
		  errorScore.push(jQuery("#questitle").attr('questionid').trim());
          console.log("wrong choice!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
        }
        console.log(_this.score);
        return setTimeout((function() {
          return _this.changeQue();
        }), 40);
      });
    }else{
      this.queList = jQuery("#queList li");
      console.log(this.queList, "quelist");
      _this = this;
      return this.queList.click(function() {
        var nowEle, nowIndex;
        nowEle = jQuery(this);
        nowIndex = nowEle.index();
        nowEle.addClass("active");
        console.log(nowIndex, 'nowIndex');
        if (_this.ifRightChoice(nowIndex)) {
          _this.score += _this.averageScore;
		  console.log("success");
        } else {
		  //错误记录题号 
		  errorScore.push(jQuery("#questitle").attr('questionid').trim());
          console.log("wrong choice!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
        }
        console.log(_this.score);
        return setTimeout((function() {
          return _this.changeQue();
        }), 40);
      });
     }
    };

    uniqueQuestion.prototype.changeQue = function() {
      if (this.nowQueIndex < this.dataArray.length - 1) {
        console.log("nowQueIndex", this.nowQueIndex);
        this.nowQueIndex++;
        return this.createQueDiv();
      } else {
        console.log(this.score, "score!!!!!!!!!!!!!!!!!!!!!!!!!!");
        console.log("finish");
        return this.endAnswer();
      }
    };

    uniqueQuestion.prototype.getcommentIndex = function(nowIndex) {
      var nowItem;
      nowItem = this.scoreArray[nowIndex];
      console.log(this.score, nowIndex, nowItem);
      if (this.score > nowItem['score']) {
        return this.getcommentIndex(nowIndex + 1);
      } else {
        return nowIndex;
      }
    };

    uniqueQuestion.prototype.getCommentByScore = function() {
      var comment, commentIndex;
      commentIndex = 0;
      commentIndex = this.getcommentIndex(commentIndex);
      console.log(commentIndex);
      comment = this.scoreArray[commentIndex]['comment'];
      console.log(comment);
      return comment;
    };

    uniqueQuestion.prototype.endAnswer = function() {
      var comment, endHtml, followHref;
      comment = this.getCommentByScore();
      this.score = parseInt(this.score);
      followHref = window.buttonurl[0];
	  nextHref   = window.buttonurl[1];
	  aboutHref  = window.buttonurl[2];
	  var errorparam = errorScore.join("X");
	  var sharbtn = '';var sharbtn = '';var paimingbtn = '';var errorbtn = '';
	  //分享设置
	  if(followHref!=''){
		  sharbtn = "<a class='btn btn-lg btn-success' id='btn-share' onclick='showGuide();'>分享到朋友圈</a>";
	  }
	  if(nextHref!=''){
		  paimingbtn = "<a class='btn btn-lg btn-primary' id='btn-share' href='" + nextHref + "'>考试排行榜</a>";
	  }
	  if(aboutHref!=''&&errorparam!=''){
		  errorbtn = "<a class='btn btn-lg btn-danger' id='btn-share' href='" + aboutHref + "/errorparam/" + errorparam + "'>错题分析</a>";
	  }
	  //提交分数
    $.post(window.buttonurl[3], { score: this.score,error:errorparam }, function(jsondata){
		return true;
	});
      endHtml = "<div class='score-holder'><span id='score'>获得：" + this.score + "分</span></div><div class='infos'><div><b>详细分析:</b></div><p id='comment'><abbr title='attribute'>" + comment + "</abbr></p>" + sharbtn + paimingbtn + errorbtn + "</div>";
      this.queDiv.hide();
      this.startDiv.hide();
      this.endDiv.append(endHtml);
      this.endDiv.fadeIn();
      weixinData.title = '我得了:'+this.score+'分~' + comment;
      return weixinData.desc = '我得了:'+this.score+'分~' + comment;
    };
    return uniqueQuestion;

  })();

  jQuery(document).ready(function() {
    var divisor, joinEm, nowTime, people, timeRange;
    console.log("ready");
    window.nowUQ = new uniqueQuestion(window.nowdatajson);
    joinEm = jQuery(".joinPeople em");
    nowTime = Date.parse(new Date());
    timeRange = nowTime - 1406699956000;
    divisor = 0.0017;
    people = parseInt(timeRange * divisor) + 561;
    joinEm.html(people);
    window.hideGuide = function() {
      var theGuidePage;
      theGuidePage = jQuery("#share-guide");
      return theGuidePage.hide();
    };
    return window.showGuide = function() {
      var theGuidePage;
      theGuidePage = jQuery("#share-guide");
      return theGuidePage.show();
    };
  });

}).call(this);
