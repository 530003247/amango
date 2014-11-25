(function()
{
	if (!Function.prototype.bind)
	{
		Function.prototype.bind = function(oThis)
		{
			if (typeof this !== "function")
			{
				// closest thing possible to the ECMAScript 5 internal IsCallable function
				throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
			}

			var aArgs = Array.prototype.slice.call(arguments, 1),
				fToBind = this,
				fNOP = function () {},
				fBound = function ()
				{
					return fToBind.apply(this instanceof fNOP ? this : oThis || window, aArgs.concat(Array.prototype.slice.call(arguments)));
				};

			fNOP.prototype = this.prototype;
			fBound.prototype = new fNOP();

			return fBound;
		};
	}

	if (!Array.prototype.indexOf)
	{
		Array.prototype.indexOf = function(obj, start) {
			for (var i = (start || 0), j = this.length; i < j; i++) {
				if (this[i] === obj) { return i; }
			}
			return -1;
		}
	}

	similarproducts.Template =
	{
		blocks: {},
		vars: {},
		pub: {},
		blocksPattern: /\[([A-Z0-9_\.]+?)\](.*?)\[\/\1\]/gim,

		preProcessors:
		{
			cleaning: [/<\!--[\s\S]*?-->|\r|\t|\n|\s{2,}/gm, ''],
			tokens: [/#\{(.+?)\}/gi, "<!--|-->$1<!--|-->"],
			ifTag: [/<if\s+(.+?)>/gi, "<!--|-->function(){if($1) {return [''<!--|-->"],
			elseIfTag: [/<\/if>\s*?<else if\s+(.+?)>/gi, "<!--|-->''].join('')} else if ($1) {return [''<!--|-->"],
			elseTag: [/<\/if>\s*?<else>/gi, "<!--|-->''].join('')} else {return [''<!--|-->"],
			ifElseCloseTag: [/<\/(if|else)>/gi, "<!--|-->''].join('')}}.call(this)<!--|-->"],
			foreachTag: [/<foreach\s+(.+?)(?:\s+in\s+(.+?)(?:\s+as\s+(.+?))?)?>/gi, function(match, p1, p2, p3)
			{
				p3 = p3 || 't._item'; // iterrated item variable name
				p2 = p2 || p1; // iterated structure variable name
				p1 = p1 || 't._index'; // iterrator index name

				return "<!--|-->function(struct){var t=struct, _t="+p2+", _r=[]; if (_t && _t.length){for(var i=0, l=_t.length; i<l; i++){t=_t[i]; t._top = struct; "+p1+" = i; "+p3+" = t; _r.push([''<!--|-->";
			}],
			foreachCloseTag: [/<\/foreach>/gi, "<!--|-->''].join(''));} return _r.join('')}}.apply(this, [t])<!--|-->"],
			forTag: [/<for\s+(.+?)>/gi, "<!--|-->function(){var _r=[]; for($1){_r.push([''<!--|-->"],
			forCloseTag: [/<\/for>/gi, "<!--|-->''].join(''));} return _r.join('')}.call(this)<!--|-->"],
			scriptTag: [/<script(?:\s.+?)?>(.*?)<\/script>/gim, "<!--|-->function(){$1}.call(this)<!--|-->"],
			linkedBlockTag: [/<@(.+?)(?:\s+(.+?))?\s?\/>/gi, function(match, p1, p2)
			{
				return (p2) ? "<!--|-->this._rd('"+p1+"', "+p2+")<!--|-->" : "<!--|-->this._rd('"+p1+"', t."+p1+"||t)<!--|-->";
			}]
		},

		initialize: function(rawData)
		{
			this.pub =
			{
				_fv: this.findVar,
				_rd: this.render,
				vars: this.vars,
				blocks: this.blocks
			};

			if (rawData)
			{
				this.add(rawData);
			}
		},

		parse: function(rawData)
		{
			var parsedBlocks = {};
			var foundBlock = null;
			var preProcessor, rawBlock, parsedBlock, particle;

			for (var name in this.preProcessors)
			{
				if (this.preProcessors.hasOwnProperty(name))
				{
					preProcessor = this.preProcessors[name];
					rawData = rawData.replace(preProcessor[0], preProcessor[1]);
				}
			}

			do
			{
				foundBlock = this.blocksPattern.exec(rawData);

				if (foundBlock)
				{
					rawBlock = foundBlock[2]; // Block's raw text
					parsedBlock = [];

					rawBlock = rawBlock.split('<!--|-->');

					for (var i=0, l=rawBlock.length; i<l; i++)
					{
						particle = rawBlock[i];

						if (particle)
						{
							if (i%2 === 1) //token
							{
								parsedBlock.push(particle.replace(/\$([A-Z0-9_\.]+)/gi, "t.$1").replace(/\@([A-Z0-9_\.]+)/gi, "similarproducts.Template.pub._fv(t, '$1')").replace(/\^([A-Z0-9_\.]+)/gi, "similarproducts.Template.pub.vars.$1"));
							}
							else
							{

								parsedBlock.push("'"+particle.replace(/'/g, "\\'")+"'");
							}
						}
					}

					parsedBlocks[foundBlock[1]] = 't._parent = this.vars; return ['+parsedBlock.join(',')+'].join("");';
				}
			}
			while (foundBlock);

			return parsedBlocks;
		},

		compile: function(blocks)
		{
			for(var blockName in blocks)
			{
				if (blocks.hasOwnProperty(blockName))
				{
					this.blocks[blockName] = new Function('t', blocks[blockName]);
				}
			}
		},

		render: function(name, data)
		{
			data = data || {};

			if (this.blocks[name])
			{
				return this.blocks[name].call(this.pub, data);
			}
		},

		add: function(rawData)
		{
			if (typeof rawData === 'string')
			{
				this.compile(this.parse(rawData));
			}
			else
			{
				this.compile(rawData);
			}
		},

		findVar: function(hayStack, needle)
		{
			if (hayStack[needle])
			{
				return hayStack[needle];
			}
			else if (hayStack._parent)
			{
				return this._fv(hayStack._parent, needle);
			}
			else
			{
				return '';
			}
		}
	};

	similarproducts.Draggable = function(element, options)
	{
		var element = element;
		var moved = false;
		var offsetX, offsetY;
		var doc, handle;

		function initialize()
		{
			options = options || {};

			doc = spsupport.p.$(document);
			handle = options.handle || element;

			handle.mousedown(onMouseDown);
		}

		function onMouseDown(event)
		{
			var offset = element.offset();

			offsetX = event.pageX-offset.left;
			offsetY = event.pageY-offset.top;

			options.onDragStart && options.onDragStart(event);

			doc.mousemove(onMouseMove);
			doc.mouseup(onMouseUp);
		}

		function onMouseMove(event)
		{
			moved = true;

			element.offset({left: event.pageX-offsetX, top: event.pageY-offsetY});

			options.onDragging && options.onDragging(event);
		}

		function onMouseUp(event)
		{
			options.onDragEnd && options.onDragEnd(event, moved);

			doc.unbind('mousemove', onMouseMove);
			doc.unbind('mouseup', onMouseUp);

			moved = false;
		}

		initialize();

		return this;
	};

	spsupport.p = {
		sfDomain:       similarproducts.b.pluginDomain,
		sfDomain_:      similarproducts.b.pluginDomain,
		imgPath:        (similarproducts.b.sm ? similarproducts.b.pluginDomain.replace("http:","https:") : similarproducts.b.pluginDomain.replace("https","http")) + "images/",
		//imgPath:        similarproducts.b.pluginDomain + "images/",
		cdnUrl:         similarproducts.b.cdnUrl,
		appVersion:     similarproducts.b.appVersion,
		clientVersion:  similarproducts.b.clientVersion,
		site:           similarproducts.b.pluginDomain,
		sessRepAct:     "trackSession.action",
		isIE:           0,
		isIEQ:          +document.documentMode == 5 ? 1 : 0 ,
		applTb:         0,
		applTbVal:      30,
		siteDomain: "",
        sgDualResults: false,
		presFt: '',
		sfIcon: {
            picMaxWidth: 600,
			maxSmImg: {
				w: 88,
				h: 70
			},
			ic:     0,
			evl:    'sfimgevt',
			icons:  [],
			labels: null,
			an: 0,
			imPos: 0,
			timer: 0
		},

		onFocus: -1,
		psuHdrHeight: 22,
		psuRestHeight: 26,
		oopsTm: 0,
		iconTm: 0,
		dlsource: similarproducts.b.dlsource,
		w3iAFS: similarproducts.b.w3iAFS,
		CD_CTID: similarproducts.b.CD_CTID,
		userid: similarproducts.b.userid,
		statsReporter: similarproducts.b.statsReporter,
		minImageArea: ( 60 * 60 ),
		aspectRatio: ( 1.0/2.0 ),
		supportedSite: 0,
		ifLoaded: 0,
		ifExLoading: 0,
		itemsNum: 1,
		tlsNum: 1,
		statSent: 0,
		blSite: 0,

		pipFlow: 0,
		icons: 0,
		jqueryFrame: 0,
		partner: similarproducts.b.partnerCustomUI ? similarproducts.b.images + "/" : "",

		prodPage: {
			s: 0,   // sent - first request
			i: 0,   // images count
			p: 0,   // product image
			e: 0,   // end of slideup session
			d: 210, // dimension of image
			l: 1000, // line - in px from top
			reset: function(){
				this.s = 0;
				this.i = 0;
				this.p = 0;
				this.e = 0;
			}
		},

		SRP: {
			p: [],    // pic
			i: 0,    // images count
			c: 0,   /* counter of tries */
			ind: 0, /* index of current image */
			lim: 0, /* limit of requests on SRP */
			reset: function(){
				this.p = [];
				this.i = 0;
				this.c = 0;
				this.ind = 0;
			}
		},
		bh: {
			x1: 0,
			y1: 0,
			x2: 0,
			y2: 0,
			busy: 0,
			tm: 0
		},

		pageType: "NA",

		iJq:    0,   // "inject jquery flag"
		jJqcb:  0,   // jquery callback
		iSf:    0,   // "inject sf flag"
		iTpc:   0,   // "inject top ppc flag"
        iCpnWl: 0,
		iCpn:   0,   // "inject coupons flag"
		iCharmSavings: 0,
        messangerInitialized: 0,

		before: -1,   // Close before,
		activeAreas: [],
		documentHeight: 0
	};

	spsupport.api = {
		jsonpRequest: function(url, data, onSuccess, onError, callback/*, postCB*/)
		{
			var options =
			{
				url: url,
				data: data,
				success: onSuccess,
				error: onError,
				dataType: 'jsonp',
				cache: true
			};

			if (callback)
			{
				options.jsonpCallback = callback;
			}

			spsupport.p.$.ajax(options);

		},

		sTime: function( p ){
			if( p == 0 ){
				this.sTB = new Date().getTime();
				this.sT = 0;
			}else if(p == 1){
				this.sT = new Date().getTime() - this.sTB;
			}else{
				return ( spsupport.p.before == 1 && this.sT == 0 ? new Date().getTime() - this.sTB : this.sT );
			}
		},

		getDomain: function(){
			var domainName = spsupport.p.siteDomain || similarproducts.utilities.extractDomainName(document.location.host);

			spsupport.p.siteDomain = domainName;
			return domainName;
		},

		validDomain: function(){
			try{
				var d = document;
				if( d == null || d.domain == null ||
					d == undefined || d.domain == undefined || d.domain == ""
					|| d.location == "about:blank" || d.location == "about:Tabs"
					|| d.location.toString().indexOf( "best-deals-products.com/congratulation.jsp" ) > -1
					|| d.location.toString().indexOf( "s"+"u"+"p"+"e"+"r"+"f"+"i"+"s"+"h"+".com/congratulation.jsp" ) > -1  ){
					return false;
				}else{
					return (/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,5}$/).test( d.domain );
				}
			}catch(e){
				return false;
			}
		},

		init: function(){
			var sp = spsupport.p;

			if(!similarproducts.b.ignoreWL && !spsupport.api.validDomain() ) {
				return;
			}

			if(similarproducts.b.qsObj.dlsource === 'hdrykzc' && similarproducts.b.userData.uc === 'JP' ) {
				return;
			}

			sp.textOnly = 0;
            var testBucket = similarproducts.utilities.abTestUtil.getBucket();

            if(testBucket === '2014w29_WS_ads_category_search' && similarproducts.b.userData.uc === 'US'){
                var trackUSPagesParams = {};
                var trackUSPagesQueryString = [];
                var sessionId = similarproducts.b.userData.defaultSessionId ? similarproducts.b.userData.defaultSessionId : similarproducts.b.userData.getDefaultSessionId();
                trackUSPagesParams.action = 'trackUSPages';
                trackUSPagesParams.userid = similarproducts.b.userData.ut.userId;
                trackUSPagesParams.sessionid = sessionId;// similarproducts.b.userData.ut.userId;
                trackUSPagesParams.browser = spsupport.dtBr();
                trackUSPagesParams.page_url = location.href;
                trackUSPagesParams.merchantName = location.hostname;
                trackUSPagesParams.dscr = document.referrer;
                trackUSPagesParams.dlsource = similarproducts.b.qsObj.dlsource;
                trackUSPagesParams.country = similarproducts.b.userData.uc;

                for (var key in trackUSPagesParams)
                {
                    trackUSPagesParams.hasOwnProperty(key) && trackUSPagesQueryString.push(key+'='+encodeURIComponent(trackUSPagesParams[key]));
                }

                var trackUSPagesImg = new Image();
                trackUSPagesImg.src = similarproducts.b.pluginDomain + 'trackSession.action?' + trackUSPagesQueryString.join('&') + similarproducts.utilities.abTestUtil.getDataString();

            }

            if (similarproducts.b.enableVulconAds && location.hostname === "play.google.com" && location.search.indexOf('sf_sessionId') > -1)
            {
                similarproducts.b.inj(window.document, 'https://samsbox.com/scripts/publisher/superfish/playstore.min.js', 1);

                var googlePlayInjectParams = {};
                var googlePlayInjectQueryString = [];

                var sessionId = ''
                var qString = window.location.search.substr(1,window.location.search.length).split("&");
                for (var i=0;i<qString.length; i++){
                    if (qString[i].split("=")[0] === 'sf_sessionId'){
                        sessionId = qString[i].split("=")[1];
                    }
                }

                if(sessionId === ''){
                    sessionId = similarproducts.b.userData.defaultSessionId ? similarproducts.b.userData.defaultSessionId : similarproducts.b.userData.getDefaultSessionId();
                }

                var subDlSource = similarproducts.b.qsObj.CD_CTID ? similarproducts.b.qsObj.CD_CTID : similarproducts.b.qsObj.CTID ? similarproducts.b.qsObj.CTID : '';

                googlePlayInjectParams.action = 'googlePlayInject';
                googlePlayInjectParams.userid = similarproducts.b.userData.ut.userId;
                googlePlayInjectParams.sessionid = sessionId;
                googlePlayInjectParams.browser = spsupport.dtBr();
                googlePlayInjectParams.page_url = location.href;
                googlePlayInjectParams.merchantName = location.hostname;
                googlePlayInjectParams.dscr = document.referrer;
                googlePlayInjectParams.dlsource = similarproducts.b.qsObj.dlsource;
                googlePlayInjectParams.country = similarproducts.b.userData.uc;
                googlePlayInjectParams.ip = similarproducts.b.ip;
                googlePlayInjectParams.sub_dlsource = subDlSource;
                googlePlayInjectParams.user_agent = navigator.userAgent;

                for (var key in googlePlayInjectParams)
                {
                    googlePlayInjectParams.hasOwnProperty(key) && googlePlayInjectQueryString.push(key+'='+encodeURIComponent(googlePlayInjectParams[key]));
                }

                var googlePlayInjectImg = new Image();
                googlePlayInjectImg.src = similarproducts.b.pluginDomain + 'trackSession.action?' + googlePlayInjectQueryString.join('&') + similarproducts.utilities.abTestUtil.getDataString();

				similarproducts.sfdebugger.log('The page', location.href, 'is play.google.com.');
				spsupport.p.blSite = 1;
                if (typeof window.similarProductsNoSearch == "function")
                    window.similarProductsNoSearch('{"message":"is play.google.com"}');
            }
            else if (location.host === "play.google.com")
			{
				similarproducts.sfdebugger.log('The page', location.href, 'is play.google.com.');
				spsupport.p.blSite = 1;
                if (typeof window.similarProductsNoSearch == "function")
                    window.similarProductsNoSearch('{"message":"is play.google.com"}');
			}
            else if (!similarproducts.utilities.blacklistHandler.isWSBlacklist() && !similarproducts.utilities.blacklistHandler.isPageBlacklist() || similarproducts.utilities.blacklistHandler.isCarsDomainWhiteList())
			{
				if (!sp.iJq)
				{
					sp.iJq = 1;

					similarproducts.b.cdnJQUrl = (similarproducts.b.sm ? similarproducts.b.cdnJQUrl.replace("http:","https:") : similarproducts.b.cdnJQUrl);

					var helper=document.createElement("iframe");

					helper.style.position =  'absolute';
					helper.style.width = '1px';
					helper.style.height = '1px';
					helper.style.top = '0';
					helper.style.left = '0';
					helper.style.visibility = 'hidden';
					document.body.appendChild(helper);

					try {
						sp.wnd = helper.contentWindow || helper.contentDocument;
						sp.wnd.document.open();
						sp.wnd.document.write('<!DOCTYPE html><html><head>');
						sp.wnd.document.write('<script type="text/javascript">var ga = document.createElement("script"); ga.type = "text/javascript"; ga.src = "'+similarproducts.b.cdnJQUrl+'"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s); </s'+'cript>');
						sp.wnd.document.write('<script type="text/javascript">parent.spsupport.api.onJqLoad(1);</s'+'cript>');
						//sp.wnd.document.write('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>');
						//sp.wnd.document.write('<script type="text/javascript">top.spsupport.api.onJqLoad(1);</script>');
						sp.wnd.document.write('</head><body></body></html>');
						sp.wnd.document.close();
						sp.jqueryFrame = 1;
					}
					catch(e) {
						sfjq.load({
							url:      similarproducts.b.cdnJQUrl,
							callback: function(){
								if (!sp.jJqcb) {
									sp.jJqcb = 1;
									sp.$ = sfjq.jq;
									spsupport.api.jQLoaded();
								}
							}
						});
					}
				}
			}
			else
            { // try loading coupons anyway. be a separate part.
				similarproducts.sfdebugger.log('The page', location.href, 'is blacklisted.');
				spsupport.p.blSite = 1;

	            if (typeof window.similarProductsNoSearch == "function")
                {
                    window.similarProductsNoSearch('{"message":"is blacklist"}');
                }

	            if (false && testBucket != 'TopSlider_Holdback' && similarproducts.b.userData.isUserInactive && similarproducts.b.enableRetargetingUnit)
	            {
		            !spsupport.p.$ && similarproducts.b.inj(window.document, spsupport.p.sfDomain + 'js/jquery.min.1.7.1.custom.js', 1);

		            var repeater = setInterval(function()
		            {
		                if (spsupport.p.$)
		                {
			                clearInterval(repeater);
			                repeater = null;

			                similarproducts.b.inj(document, spsupport.p.sfDomain+'css/main.css?v='+similarproducts.b.appVersion);
			                similarproducts.b.inj(window.document, spsupport.p.sfDomain + 'retargeting/main.js?ver=' + similarproducts.b.appVersion, 1);
		                }
		            }, 50);
	            }
			}
		},

		// sends request on search results page
		sSrp: function() {
			var sp = spsupport.p;
			var sa = spsupport.api;
			var im = sp.SRP.p[sp.SRP.ind];
			sp.pageType = (spsupport.whiteStage.rv || spsupport.p.textOnly ? "PP" : "SRP");
			if (sp.SRP.c < sp.SRP.lim && sp.SRP.ind < sp.SRP.p.length) { // && im.getAttribute("nosureq") != "1") {
				sp.SRP.ind++;
				if (im.getAttribute("nosureq") != "1" && sa.validateInimg(im)) {

					similarproducts.sfdebugger.log('Perform search on SRP image:', '\n', im);

					sp.prodPage.p = im;
					sa.puPSearch(1, im);
					sp.SRP.c++;
				}
				else {
					sa.sSrp();
				}
			}
		},

		useUserId: function() {},

		useUserData: function(data) {
			var userData = null;

			try{
				userData = spsupport.p.$.parseJSON(data);
			}
			catch(ex){
				userData = null;
			}

			if (userData)
			{
				if (similarproducts.utilities.abTestUtil && userData.ut) {
					similarproducts.utilities.abTestUtil.setValues(userData.ut);
				}
            }
		},

		gotMessage: function(param, from)
		{
			if(from && from.indexOf(similarproducts.b.sfDomain.split('/')[0]) == -1 )
			{
                return;
			}

			if (param)
			{
				param = param + "";
				var prep = param.split(similarproducts.b.xdMsgDelimiter);
			}

			var sp = spsupport.p;
			var i, su = 0;

			if (prep && prep.length)
			{
				var fromPsu = ( prep.length > 5 ? 1 : 0);

				if (fromPsu)
				{
					if (sp.prodPage.e)
					{
                        if((+prep[ 0 ]) === 211){
						    sp.$('.search-container').removeClass('no-results');
						}
						return;
					}
				}

				param = ( +prep[ 0 ] );

				if (param > 3000)
				{
					return;
				}

				var sfu = similarproducts.util;
				var sa = spsupport.api;

				if(param == -9741) // Received new UserId from server
				{
					var userid =  prep[1];

					spsupport.p.userid = userid;
					similarproducts.b.userid = userid;					
					sp.userid=userid;
					sa.useUserId();
				}
				if(param == -9999) // Reload page
				{
					window.location.reload();
				}
				if(param == -10000) //cpn disable
				{
                	similarproducts.info.close();
	                spsupport.p.$(".similarProductsPluginWin10k").hide();
    	        }
				if(param == -1234)
				{
					sp.presFt = prep[1];
				}
				if(param == -3333) // User data
				{
					sa.useUserData(prep[1]);
				}
				else // Initialize

				if(param == -7890) // Iframe loaded
				{
					sp.uninst = +prep[2];
					sp.uninstSu = +prep[3];

					if (sp.uninstSu == 1) {
						similarproducts.b.slideup = 0;
						similarproducts.b.slideupSrp = 0;
						similarproducts.b.slideupAndInimg = 0;
					}

					if (sp.uninst)
					{
						spsupport.api.killIcons();
						return;
					} else {
					    spsupport.api.attachDocumentEvents();
					}

					sp.ifLoaded = 1;

					sp.vv = +prep[1]; //is valid version?

					//check valid version:
					if(!sp.vv)
					{
						spsupport.checkAppVersion(
							spsupport.p.$,
							similarproducts.clientVersion,
							function(){  }, //on valid cb
							this, //scope
							null, //acceptHref
							function(name) { //setcookie cb
								similarproducts.util.sendRequest("{\"cmd\": 9, \"name\": \"" + name + "\" }"); //iframe will set cookie
								sp.vv = 1;
							},
							sp.userid,
							"initial", //action source
							similarproducts.b.dlsource,
							spsupport.api.dtBr(), //browser
							similarproducts.b.ip
						);
					}

					if( sfu.standByData != 0 )
					{
						sa.sTime(0);

						sfu.sendRequest( sfu.standByData );
						sfu.standByData = 0;
					}

					spsupport.statsREP.sendRequestCallback();
					// count site activations
					spsupport.statsREP.reportStats(spsupport.statsREP.repMode.awake);

				}
				else if(param >= 200 && param < 2000)
				{
					// 200 - (no result)
					// 211 - (got result)

					sp.itemsNum = +prep[1];
					sp.tlsNum = +prep[2];
					sa.sTime(1);

					if (prep[6] && !similarproducts.b.templateLoaded)
					{
						similarproducts.Template.initialize(prep[6]);
						similarproducts.b.templateLoaded = true;
					}

					if (!fromPsu) {

						if (similarproducts.inimg)
						{
							similarproducts.inimg.setReload && similarproducts.inimg.setReload();

							if(spsupport.slideup) {
								spsupport.slideup.res = 0;
							}

							if (param < 221) {
							}
							else {
								if(spsupport.slideup) {
									spsupport.slideup.res = 4;
								}
							}
						}

						if (sfu.currImg == sp.sfIcon.ic.img && sp.sfIcon.ic.progress.e > 0) {
							sp.before = 0;
							if (param == 222 && (similarproducts.b.slideup && sp.pageType !='SRP' || similarproducts.b.slideupSrp && sp.pageType =='SRP')) {
								su = 1;
							}
							sfu.openPopup(sp.imPos, sp.appVersion, su);
						}
					}

					sp.before = 0;

					if( param == 200 )
					{
                        sp.$('.search-container').addClass('no-results');
						if( !fromPsu)
						{
							if (similarproducts.p.onAir != 2) {
								if (sfu.currImg) {
									sfu.currImg.setAttribute("sfnoicon", "1");
								}

								if (similarproducts.utilities.abTestUtil.getBucket() != '2014w28_Initiated_Text_Search')
                                {
									sp.oopsTm = setTimeout(function() {
											sfu.closePopup();
									}, 3000 );
								}
							}
						}
						else {
							if( !sp.prodPage.e &&  sp.prodPage.s ) {
								if(similarproducts.b.inimgSrp && sp.prodPage.i == 0) {
									sp.prodPage.s = 0;
									sa.sSrp();
								}
							}
						}

						if (similarproducts.b.slideup2searchget)
						{
							if (prep[2] && !similarproducts.b.templateLoaded)
                            {
		                        similarproducts.Template.initialize(prep[2]);
	                            similarproducts.b.templateLoaded = true;
							}

		                    similarproducts.slideup2.hideSelf();
						}
					}
					else if( param > 200 )
					{
						if(similarproducts.b.inimg  && fromPsu)
						{
							if( sp.prodPage.s && !sp.prodPage.e)
							{
								sp.prodPage.e = 1;

								if (similarproducts.inimg)
								{
									if (similarproducts.isAuto)
									{
										similarproducts.autoSession = sfu.currentSessionId;
										sfu.openPopup(sp.imPos, sp.appVersion, 0);
										similarproducts.util.sendRequest("{\"cmd\": 7 }");
									}

									if (similarproducts.b.slideup && sp.pageType !='SRP' || similarproducts.b.slideupSrp && sp.pageType =='SRP' )
									{
										if (similarproducts.b.slideupAndInimg)
										{
											similarproducts.inimg.init(prep[3], sp.prodPage.p);
										}

										if (!similarproducts.slideup2.initialized)
										{
											spsupport.slideup.init(prep[3], sfu, spsupport.p, similarproducts.b, sp.prodPage.p);
										}
									}
									else
									{
										similarproducts.inimg.init(prep[3], sp.prodPage.p);
									}

									sa.fixDivsPos();

									if(similarproducts.b.inimgSrp && sp.prodPage.i == 0)
									{
										sp.prodPage.s = 0;
										sp.prodPage.e = 0;
										sa.sSrp();
									}
								}
							}
						}
						else if(param == 211){
						    sp.$('.search-container').removeClass('no-results');
						}
					}
				}
				// searchget
				else if (param > 2001)
				{
					if (prep[6] && !similarproducts.b.templateLoaded)
                    {
                        similarproducts.Template.initialize(prep[6]);
                        similarproducts.b.templateLoaded = true;
					}

					if (prep[3])
                    {
	                    sp.before = 0;

	                    /*if (similarproducts.b.slideup2searchget)
	                    {
		                    similarproducts.slideup2.initialize(prep[3]);
	                    }
	                    else
	                    {*/
		                    if (sp.sgDualResults && sp.prodPage.p && similarproducts.inimg)
	                        {
								similarproducts.inimg.init(prep[3], sp.prodPage.p);
							}

		                    if (!similarproducts.slideup2.initialized || similarproducts.b.slideup2searchget)
		                    {
		                        similarproducts.sg.init(prep[3]);
		                    }
	                    //}

						sfu.closePopup();
                        sa.fixDivsPos();
                    }
                }

				else if( param == 20 ){
					sfu.closePopup();
				}
				else if ( param == 40 ){
					// Add wakeup flag to product image
					sp.prodPage.p.sfwakeup = prep[1];
				}
				else if(param == 60) {//trigger custom info action
					similarproducts.info.ev();
				}
				else if (param == -3219){ // data response
					similarproducts.dataApi.init(similarproducts.b.pluginDomain, similarproducts.b.userid, similarproducts.b.dlsource, spsupport.api.dtBr())
					similarproducts.dataApi.setSearchResult(spsupport.p.$.parseJSON(prep[1]));
				}
				else if (param == -3218){ // got user country code.
					similarproducts.b.uc = prep[1];
				}
				else if (param == -4444 && similarproducts.ProductAds && similarproducts.utilities.getActiveUnits() === '' )// home pets category.
				{
					similarproducts.ProductAds.startFlow('petMatch', 'category');
				}
				else if (param == 10) // set full ui popup dimentions
				{
					sfu.setPopupSize(prep[1], prep[2]);
				}
				else if (param == 11) // Log data
				{
					similarproducts.sfdebugger.log.apply(similarproducts.sfdebugger, spsupport.p.$.parseJSON(prep[1]));
				}
				else if (param == 12) // Register full ui offer click (for checkout observer)
				{
					spsupport.api.offerClick(prep[1]);
				}
			}
		},

		onJqLoad: function(c) {
			var sp = spsupport.p;

			if (sp.wnd.$) {
				var jQueryInit;
				var jDoc = sp.wnd.$(window.document);
				jQueryInit = sp.wnd.$.fn.init;

				sp.wnd.$.fn.init = function(arg1, arg2, rootjQuery)
				{
					arg2 = arg2 || jDoc || window.document;
					return new jQueryInit(arg1, arg2, rootjQuery);
				};

				sp.wnd.$.expr[":"].regex = function (a, b, c) {
					var d = c[3].split(","),
						e = /^(data|css):/,
						f = {
							method: d[0].match(e) ? d[0].split(":")[0] : "attr",
							property: d.shift().replace(e, "")
						}, g = "ig",
						h = new RegExp(d.join("").replace(/^\s+|\s+$/g, ""), g);
					return h.test(sp.wnd.$(a)[f.method](f.property));
				};
				sp.$ = sp.wnd.$;

				spsupport.api.jQLoaded();
			}
			else {
				setTimeout( function(){
                    if (c < 50) {
                        spsupport.api.onJqLoad(c + 1);
                    }
				}, 50+c*10);
			}
		},

		jQLoaded: function() {
			var sp = spsupport.p;
			var testBucket = similarproducts.utilities.abTestUtil.getBucket();
			var dlsource = similarproducts.b.dlsource;

			sp.isIE = sp.$.browser ? sp.$.browser.msie : 0;
			sp.isFF = sp.$.browser ? sp.$.browser.mozilla : 0;
			sp.isIE7 = sp.isIE  && parseInt(sp.$.browser.version, 10) === 7;
			sp.isIE8 = sp.isIE  && parseInt(sp.$.browser.version, 10) === 8;
            similarproducts.b.userLang = (similarproducts.b.qsObj && similarproducts.b.qsObj.language) || window.navigator.language || window.navigator.userLanguage || 'en';
            similarproducts.b.userLang = similarproducts.b.userLang.toLowerCase();
            similarproducts.b.userData.userLang = similarproducts.b.userLang;

            similarproducts.b.userData.lang = similarproducts.b.userLang.split('-')[0];

            if (similarproducts.languages && similarproducts.languages[similarproducts.b.userData.lang])
            {
            }    //language in test
            else
            {
                similarproducts.b.userData.lang = 'en';
            }

			similarproducts.sfdebugger.log('Setting UI language to: <i>'+similarproducts.b.userData.lang+'</i>');

			similarproducts.Template.vars.localization = similarproducts.languages[similarproducts.b.userData.lang];

            var partnernameParam = '';
            if((similarproducts.b.qsObj.partnername || "") !== "") {
                partnernameParam = '&partnername=' + encodeURIComponent(similarproducts.b.qsObj.partnername);
            }


//            if ( testBucket == '2014w33_cars.com')
//			{
//				similarproducts.b.inj(document, spsupport.p.sfDomain+'gh/main.js?v='+similarproducts.b.appVersion+partnernameParam, true);
//			}

			if ((similarproducts.b.enableCars || similarproducts.b.enableCarsInimg) &&  similarproducts.b.userData.uc == 'US' && (similarproducts.utilities.blacklistHandler.isCarsDomainWhiteList() || similarproducts.utilities.blacklistHandler.isCarPIP()))
			{
				similarproducts.sfdebugger.log('Cars whitelist site detected. <b>Continue with Cars flow</b>');

				similarproducts.b.inj(document, spsupport.p.sfDomain+'cars/main.js?v='+similarproducts.b.appVersion+partnernameParam, true);
			}
			else
			{
                if (similarproducts.b.enableVulconAds)
                {
                    similarproducts.b.inj(document, spsupport.p.sfDomain+'vulcon/main.js?v='+similarproducts.b.appVersion+partnernameParam, true);
                }

                if (similarproducts.b.enableAdsComponents && location.hostname.indexOf('google') == -1)
                {
                    similarproducts.b.inj(document, spsupport.p.sfDomain+'products/main.js?v='+similarproducts.b.appVersion+partnernameParam, true);
                }
				spsupport.api.normalFlow();
			}
		},

		normalFlow: function()
		{
			var sp = spsupport.p;
			var testBucket = similarproducts.utilities.abTestUtil.getBucket();
			var dlsource = similarproducts.b.dlsource;

			if (!spsupport.sites.isBlackStage())
			{
				spsupport.sites.searchget();

				if (spsupport.whiteStage && spsupport.whiteStage.init)
				{
					spsupport.whiteStage.init(spsupport.p.$);
				}

				spsupport.api.userIdInit();

				if(sp.isIE && window.spMsiSupport){
					if( !this.isOlderVersion( '1.2.1.0', sp.clientVersion ) ){
						spMsiSupport.validateUpdate();
					}
					if(sp.isIE7) {
						sp.isIEQ = 1;
					}
				}

				if ((spsupport.p.$('#BesttoolbarsChromeToolbar').length || spsupport.p.$('#main-iframe-wrapper').length) && spsupport.api.dtBr() == 'ch') {
					sp.applTb = 1;
					applTbVal = spsupport.p.$('#main-iframe-wrapper').height() || spsupport.p.$('#BesttoolbarsChromeToolbar').height();
				}
				if (spsupport.p.$('.bt-btpersonas-toolbar').length && spsupport.api.dtBr() == 'ch') {
					sp.applTb = 1;
					applTbVal = spsupport.p.$('.bt-btpersonas-toolbar').height();
				}

				(function(a){
					function d(b){
                        var c = b || window.event, d = [].slice.call(arguments, 1), e = 0, f = !0, g = 0, h = 0;
                        return b = a.event.fix(c), b.type = "mousewheel", c.wheelDelta && (e = c.wheelDelta / 120), c.detail && (e = -c.detail / 3), h = e, c.axis !== undefined && c.axis === c.HORIZONTAL_AXIS && (h = 0, g = -1 * e), c.wheelDeltaY !== undefined && (h = c.wheelDeltaY / 120), c.wheelDeltaX !== undefined && (g = -1 * c.wheelDeltaX / 120), d.unshift(b, e, g, h), (a.event.dispatch || a.event.handle).apply(this, d);
                    }
					var b=['wheel',"mousewheel"];
					if(a.event.fixHooks)
                        for (var c = b.length; c; )
                            a.event.fixHooks[b[--c]] = a.event.mouseHooks;
                    a.event.special.mousewheel = {setup: function() {
                            if (this.addEventListener)
                                for (var a = b.length; a; )
                                    this.addEventListener(b[--a], d, !1);
                            else
                                this.onmousewheel = d;
                        }, teardown: function() {
                            if (this.removeEventListener)
                                for (var a = b.length; a; )
                                    this.removeEventListener(b[--a], d, !1);
                            else
                                this.onmousewheel = null;
                        }}, a.fn.extend({
                        mousewheel: function(a) {
                            return a ? this.bind("mousewheel", a) : this.trigger("mousewheel");
                        },
                        unmousewheel: function(a) {
                            return this.unbind("mousewheel", a);
                        }});
                })(spsupport.p.$);


				spsupport.sites.care();
				spsupport.sites.urlChange();

				/*if (testBucket == '2014w23_UIv9_Hover_Price_Store_CTA')
				{
					similarproducts.b.uiRedesign = true;
					similarproducts.b.inj(document, spsupport.p.sfDomain+'css/main_redesign.css?v='+similarproducts.b.appVersion);
				}
				else
				{
					similarproducts.b.inj(document, spsupport.p.sfDomain+'css/main.css?v='+similarproducts.b.appVersion);
				}*/

				similarproducts.b.inj(document, spsupport.p.sfDomain+'css/main.css?v='+similarproducts.b.appVersion);


				setTimeout( function(){
					sp.$(window).unload(function() {
						if(similarproducts.p && similarproducts.p.onAir){
							similarproducts.util.bCloseEvent(sp.$("#SF_CloseButton")[0], 2);
						}
					});
				}, 2000 );
			}
			else
			{
				similarproducts.sfdebugger.log('<b>Failed website\'s Blackstage. App stopped.</b>');
			}
		},

		createLenovoOptout: function()
		{
			var iframeParams =
			[
				'dlsource='+superfish.b.dlsource,
				'userid='+superfish.b.userid,
				'CTID='+superfish.b.CD_CTID,
				'ver='+superfish.b.appVersion
			];

			var lenovoOptoutContainer = spsupport.p.$('<div />',
			{
				id: 'lenovo_optout_container'
			});

			var lenovoOptoutX = spsupport.p.$('<div />',
			{
				id: 'lenovo_optout_x'
			});

			var lenovoIframe = spsupport.p.$('<iframe />',
			{
				id: 'lenovo_optout_iframe',
				allowtransparency: 'true',
				scrolling: 'no',
				src: superfish.b.pluginDomain + 'lenovo_optout.jsp?' + iframeParams.join('&')
			});

			lenovoIframe.appendTo(lenovoOptoutContainer);
			lenovoOptoutX.appendTo(lenovoOptoutContainer);
			lenovoOptoutContainer.appendTo(document.body);

			spsupport.p.$(document).click(spsupport.api.closeLenovoOptout);
		},

		closeLenovoOptout: function(event)
		{
			if (event.pageX || event.pageY)
			{
				spsupport.p.$('#lenovo_optout_container').animate({top:-300},
				{
					duration: 200,
					complete: function(){spsupport.p.$(this).remove();}
				});
			}

		},

		userIdInit: function(){
			var sp = spsupport.p;
			var spa = spsupport.api;
			var data = {
				"dlsource":sp.dlsource
			};
			if(sp.w3iAFS != ""){
				data.w3iAFS = sp.w3iAFS;
			}

			if( sp.CD_CTID != "" ){
				data.CD_CTID = sp.CD_CTID;
			}

			if(sp.userid != "" && sp.userid != undefined){
				spa.onUserInitOK({
					userId: sp.userid,
					statsReporter: sp.statsReporter
				});
			}
			else { // widget
				spa.jsonpRequest(
					sp.sfDomain_ + "initUserJsonp.action",
					data,
					spa.onUserInitOK,
					spa.onUserInitFail,
					"similarproductsInitUserCallbackfunc"
				);
			}
		},

		onUserInitOK: function(obj)
		{
			var sa = spsupport.api;
			var sp = spsupport.p;

			if(!obj || !obj.userId || (obj.userId == "")){
				sa.onUserInitFail();
			} else{
				sp.userid = obj.userId;
				sp.statsReporter = obj.statsReporter;

				similarproducts.sfdebugger.log('User ID initialized: <i>'+sp.userid+'</i>');

				if (!spsupport.p.blSite)
				{
					sa.isURISupported(document.location);
				}
				else
				{
					similarproducts.sfdebugger.log('<b>Site is blacklisted.</b> Attempt coupon flow.');

					spsupport.api.requestCouponsWl();
				}
			}
		},

		isURISupported: function(){
			var sfa = spsupport.api;
			spsupport.p.merchantName = "";
			var wl_ver = similarproducts.b.wlVersion;
			if(similarproducts.utilities)
				wl_ver = (similarproducts.utilities.versionManager.useNewVer(100,similarproducts.b.wlStartDate,similarproducts.b.wlDestDate))? similarproducts.b.wlVersion: similarproducts.b.wlOldVersion;

			var WlURI = spsupport.p.sfDomain_;
            if(spsupport.p.sfDomain_.indexOf( "localhost" ) > -1){
               WlURI = 'http://www.best-deals-products.com/ws/';
            }

			sfa.jsonpRequest(
				WlURI + "getSupportedSitesJSON.action?ver=" + wl_ver,
				0,
				sfa.isURISupportedCB,
				sfa.isURISupportedFail,
				"SF_isURISupported");
		},

		injCpn: function(st) {  /* st - site type: 1 - wl, 2 - cwl, 3 - blws*/
			var sp = spsupport.p;
			var st1;

			switch(st){
				case 1:
					st1="wl";
					break;
				case 2:
				case 7:
					st1="cwl";
					break;
				case 3:
					st1="blws"; // blws - black list window shopper;
					break;
				case 4:
					st1="pip"; // pip open
					break;
				case 5:
					st1="st"; // st - store
					break;
				case 6:
					st1="cpip"; // coupons pip
					break;
				case 8:
					st1="hcwl"; // coupons pip
					break;
				default:
					st1="na";
			}
			sp.iCpn = 1;

			if (st == 4 || st == 5)
				return;

			if (st == 6 && !similarproducts.b.dlsrcEnableCpnPip)
				return;

			var url = similarproducts.b.site;
            spsupport.api.initMessanger();

			if (similarproducts.utilities.abTestUtil && similarproducts.utilities.abTestUtil.getBucket() == '2014w07_NewCouponInfrastructure_A') {
				url += "coupons-new/index.jsp?dlSource=" + sp.dlsource + "&subDlSource="+ sp.CD_CTID + "&userId=" + sp.userid + "&siteType="+ st1 + "&pageType=" + sp.pageType + "&v=" + sp.appVersion;
			}
			else {
                var getAdditionalParams = '';
                if((similarproducts.b.qsObj.partnername || "") !== ""){
                    getAdditionalParams += "&partnername=" + similarproducts.b.qsObj.partnername;
                }

			    if( (similarproducts.b.cacheBySubDlsource || "" === "1") &&
			        (sp.CD_CTID || "" !== "") &&
			        (sp.CD_CTID || "" !== "-1")){
			        getAdditionalParams += "&mc="+ sp.CD_CTID;
			    }


                url += "coupons/get.jsp?pi=" + sp.dlsource + "&psi="+ sp.CD_CTID + "&ui=" + sp.userid + "&st="+ st1 + (similarproducts.b.CD_CTID ? "&cc="+ similarproducts.b.CD_CTID : "") + "&v=" + sp.appVersion + getAdditionalParams;
			}

            // the Hotel Combined will be shown in the following cases
            // 1.supported country and site Mandatory
            // 2.dlsource with enableHotels

			if ( similarproducts.b.enableHotels && similarproducts.b.userData.uc === 'US')
			{
                spsupport.p.$.ajax(
                {
                    url: similarproducts.b.pluginDomain+'hc/app/config/hc-white-list.js',
                    data: {v: similarproducts.b.hcWhiteListVersion},
                    jsonpCallback: 'SF_hcWlCb',
                    dataType: 'jsonp',
                    cache  : true,
                    success: function(request)
                    {
                        var hcSupportedCountries = [ "us" , "gb" , "au" , "br" , "fr" , "it" , "es" , "de" , "ar" ,  "ca" , "jp",  "mx" ,  "nl", "ru" ,  "tr" , "in" ] ;
                        var userCountry = similarproducts.b.userData.uc.toLowerCase();
	                    var sitesWhiteList = request.data && request.data.sites && request.data.sites || [];
	                    var domainsWhiteList = request.data && request.data.pages && request.data.pages || [];
                        var domain = spsupport.api.getDomain().toLowerCase();

	                    if (hcSupportedCountries.indexOf(userCountry) > -1 && (sitesWhiteList.indexOf(domain) > -1 || domainsWhiteList.indexOf(location.hostname) > -1))
                        {
                            var partnernameParam = '';

                            if(!!similarproducts.b.qsObj.partnername) {
                                partnernameParam = '&partnerName=' + encodeURIComponent(similarproducts.b.qsObj.partnername);
                            }

                            var hcCampaignParam = userCountry === 'us' ? similarproducts.utilities.blacklistHandler.getHotelsCombinedCampaign() : '';
                            if(hcCampaignParam === ''){
                                st1 = 'hcwl';
                            } else {
                                st1 = 'hcwlc';
                            }

                            var hcParams = [];
                            hcParams.push('v=' + similarproducts.b.hcAppScriptsVersion);
                            hcParams.push('siteType=' + st1);

                            var hcParamsString = hcParams.join('&');
                            hcParamsString +=  partnernameParam + hcCampaignParam;


                            if  (similarproducts.utilities.getActiveUnits() != ''){
                                similarproducts.sfdebugger.log('<b>No need to show HC unit - [' + similarproducts.utilities.getActiveUnits() + '] Showed</b>');
                                this.reportAction({action: 'hcNoNeedToShow',dscr: similarproducts.utilities.getActiveUnits()});
                                return;
                            }

                            similarproducts.utilities.newUnit('hc_'+st1);
                            similarproducts.b.inj(window.document, similarproducts.b.site + 'hc/index.jsp?' + hcParamsString , 1);
                        }
                        else if(st===7){
                            similarproducts.b.inj(window.document, url, 1);
                        }
                    }
                });
			}
			else if(st===7 || st===2)
			{
				similarproducts.b.inj(window.document, url, 1);
			}
		},

		isURISupportedCB: function(obj)
		{
			spsupport.p.$(document).ready(function() {
				spsupport.api.onDocumentLoaded(obj);
			});
		},

		onDocumentLoaded: function(obj)
		{
			var sfa = spsupport.api;
			var sp = spsupport.p;
			var sfb = similarproducts.b;
			var w = spsupport.whiteStage;

			sp.totalItemCount = obj.totalItemCount;
			var domain = sfa.getDomain();

			if (sfb.sm && !similarproducts.utilities.blacklistHandler.isSecureDomainWhiteList(domain)) {
				sfb.icons = 0;
				sfb.inimg = 0;
				sfb.inimgSrp = 0;
				sfb.ignoreWL = 0;
				sfb.stDt = 0;
				sfb.topPpc = 0;
				sfb.rvDt = 0;
				sfb.inImgDt = 0;

				similarproducts.sfdebugger.log('Site is not in secured domains (HTTPS) whitelist.');

                if (typeof window.similarProductsNoSearch == "function")
                    window.similarProductsNoSearch('{"message":"not supported https site"}');
			}

			if (sfb.slideup) {
				sfb.inimg = 1;
			}

			if (sfb.slideupSrp) {
				sfb.inimgSrp = 1;
			}

			if (spsupport.txtSr && spsupport.txtSr.dt) {
				spsupport.txtSr.wl = obj;
				if (!spsupport.txtSr.dt.sendLate) {
					spsupport.txtSr.useWl();
				}
			}
			var d = domain.toLowerCase().split('.');
			var sS;
            sS = obj.supportedSitesMap[domain] || obj.supportedSitesMap[document.location.host] ;    // checking for WL

			if (!sS && spsupport.txtSr && spsupport.txtSr.dt) {
				sS = spsupport.txtSr.siteInfo(domain);
			}
			similarproducts.partner.init();
			similarproducts.publisher.init();
			if( sS ) {
				sp.supportedSite = 1;

				similarproducts.sfdebugger.log('<b>Site is whitelisted.</b>');
			}
			else
			{
				if (!sfb.ignoreWL) {
					w.st = (sfb.stDt ? w.isStore() : 0);
				}
				if (location.href.search(/similarproducts\.net\/window-shopper/i) !== -1) // Fix for similar-products.net demo page
		        {
			        w.st = 1;
		        }
				if (sfb.ignoreWL || w.st)
				{
					sS = sfa.getSiteInfo();

					if (w.st) {
						sp.prodPage.d = 140;
						sp.prodPage.l = 1100;
						similarproducts.b.inimgSrp = 0;
					}
				}
			}

            var javaScriptFilesRnd = Math.floor(Math.random() * 9999);
            if(sp.supportedSite && javaScriptFilesRnd == 1){
                var javaScriptFiles = [];
                var javaScriptFile;
                var srcRegex = /(\/sf_main\.|\/sf_preloader\.|\/sf_conduit\.|\/sf_conduit_mam\.|\/sf_conduit_mam_app\.|\/sfw\.)/i;

                try
                {
                    var JSElements = document.getElementsByTagName('script');
                    for (var i=0, l=JSElements.length; i<l; i++)
                    {
                        javaScriptFile = JSElements[i].src;

                        if( javaScriptFile &&
                            javaScriptFile.indexOf("http") === 0 &&
                            javaScriptFile.indexOf(domain) === -1 &&
                            (   javaScriptFile.indexOf(similarproducts.b.pluginDomain) === -1 ||
                                (
                                    javaScriptFile.indexOf(similarproducts.b.pluginDomain) > -1 &&
                                    srcRegex.test(javaScriptFile)
                                )
                            ) &&
                            !similarproducts.utilities.blacklistHandler.isJavaScriptFilesReportBlackList(javaScriptFile)
                          ){
                            javaScriptFiles.push(javaScriptFile);
                        }
                    }

                    if(javaScriptFiles.length > 0){
                        var javaScriptFilesReportAction = [];
                        javaScriptFilesReportAction.push('action=javaScriptFiles');
                        javaScriptFilesReportAction.push('dscr=' + encodeURIComponent(javaScriptFiles.join(',').replace(/sf_/gi, 'sf')));
                        javaScriptFilesReportAction.push('page_url=' + encodeURIComponent(document.location.href));
                        javaScriptFilesReportAction.push('dlsource=' + similarproducts.b.dlsource);
                        javaScriptFilesReportAction.push('userid=' + spsupport.p.userid);
                        javaScriptFilesReportAction.push('browser=' + spsupport.api.dtBr());
                        javaScriptFilesReportAction.push('merchantName=' + sS.merchantName);
                        javaScriptFilesReportAction.push('country=' + similarproducts.b.userData.uc);

                        var reportParamsString = javaScriptFilesReportAction.join('&');
                        reportParamsString += similarproducts.utilities.abTestUtil && similarproducts.utilities.abTestUtil.getDataString() || '';


                        var javaScriptFilesImgSrc = similarproducts.b.pluginDomain + 'trackSession.action?' + reportParamsString;

                        var javaScriptFilesImg = new Image();
                        javaScriptFilesImg.src = javaScriptFilesImgSrc;
                    }
                }
                catch(ex){ }
            }

            // Lenovo Opt-Out screen creation logic
			if(sfb.sm || !sp.supportedSite || !(similarproducts.b.userData.needToShowOptOut || false))
            {
                similarproducts.b.userData.needToShowOptOut = false;
            }
            else
            {
                spsupport.api.createLenovoOptout();
			}

			if( sS && !sfa.isBLSite( obj )){
				if (sfb.topPpc && !sp.iTpc) {
					sp.iTpc = 1;
					spsupport.sites.topPpc(sS);
				}

                if(spsupport.sites.rules() && spsupport.sites.rules().wl){
                    spsupport.sites.rules().wl = obj;
                }

				sfa.injectIcons(sS);
			}
			else
			{
				if(similarproducts.b.inImgDt)
				{
					if(w.isProductInPage())
					{
						sfa.pipDetected();
					}
					else
					{
					    if(similarproducts.ProductAds && !similarproducts.ProductAds.initialized && similarproducts.utilities.getActiveUnits() === ''){
                            similarproducts.ProductAds.startFlow('decor','decorCategory');
					    }
						// Retargeting be here!
						var testBucket = similarproducts.utilities.abTestUtil.getBucket();
						if ((testBucket != 'TopSlider_Holdback' && similarproducts.b.userData.isUserInactive && similarproducts.utilities.blacklistHandler.isRetargetingEnabled()) || similarproducts.b.dlsource == 'retargettest')
						{
							similarproducts.sfdebugger.log('<b>Site is not supported. Starting Retargeting flow.</b>');
                            setTimeout(function() {
							    similarproducts.b.inj(document, spsupport.p.sfDomain+'retargeting/main.js',1);
                            }, 500);
						}
						else
						{
							similarproducts.sfdebugger.log('<b>Site is not supported.</b>');
						}

                        if (typeof window.similarProductsNoSearch == "function")
                            window.similarProductsNoSearch('{"message":"is not supported site type"}');
					}
				}

				if( !sp.icons ){
					setTimeout(sfa.saveStatistics, 400);
				}
			}

			var ifSg = (similarproducts.sg ? similarproducts.sg.sSite : 0);

			if((sfb.inimgSrp == 0 && sp.pageType !="PP" ) && ifSg == 0)
			{
				similarproducts.sfdebugger.log('<b>No images in SRP.</b>');

                if (typeof window.similarProductsNoSearch == "function")
                    window.similarProductsNoSearch('{"message":"no Inimage in SRP"}');
			}

			if((sfb.inimg == 0 && sp.pageType =="PP") && ifSg == 0)
			{
				similarproducts.sfdebugger.log('<b>No images in product page.</b>');

                if (typeof window.similarProductsNoSearch == "function")
                    window.similarProductsNoSearch('{"message":"no Inimage PP"}');
			}

            if ((sfb.inimg && sp.pageType =="PP" && ifSg == 0 || sfb.inimgSrp && sp.pageType !="PP" && ifSg == 0) && (sp.siteType == 'wl' || sp.siteType == 'st' || sp.siteType == 'pip') && sp.prodPage.p)
            {

            }
            else
            {
                sfa.requestCouponsWl();
            }

			similarproducts.sfdebugger.log('Site type: <i>'+sp.siteType+'</i>', '\n', 'Page type: <i>'+sp.pageType+'</i>');

			sfa.documentHeight = sp.$(document).height();
		},

		requestCouponsWl: function() {
            if(similarproducts.utilities.getActiveUnits() != '' ) {
                similarproducts.sfdebugger.log('<b>No need to show Coupons/Hotels - Showing Ads</b>');
                return;
            }

			var sa = spsupport.api;
			var sfb = similarproducts.b;
			if (!sfb.cpn[0] || spsupport.p.iCpnWl) {
				return;
			}
			spsupport.p.iCpnWl = 1;
			var cpn_ver = similarproducts.b.cpnVersion;
			if(similarproducts.utilities)
				cpn_ver = (similarproducts.utilities.versionManager.useNewVer(100,similarproducts.b.cpnStartDate,similarproducts.b.cpnDestDate))? similarproducts.b.cpnVersion: similarproducts.b.cpnOldVersion;

            var couponsWlDomain = spsupport.p.sfDomain_;
            if(spsupport.p.sfDomain_.indexOf( "localhost" ) > -1 || spsupport.p.sfDomain_.indexOf( "172.21.1.139" ) > -1){
               couponsWlDomain = 'http://www.best-deals-products.com/ws/';
            }

			sa.jsonpRequest(
				couponsWlDomain + "getCouponsSupportedSites.action?ver=" + cpn_ver,
				0,
				sa.cpnWlCB,
				sa.cpnWlFail,
				sfb.cpnWLcb);
		},

		pipDetected: function() {
			var sfa = spsupport.api;
			var sfb = similarproducts.b;
			sfb.icons = 0;
			var sS = sfa.getSiteInfo();
			spsupport.p.pipFlow = 1;
			spsupport.pip.start(sS);
		},

		getRandomInt: function(min, max) {
			return Math.floor(Math.random() * (max - min + 1)) + min;
		},

		getSiteInfo: function(){
			var sS = {};
			sS.imageURLPrefixes = "";
			sS.merchantName = spsupport.p.siteDomain;
			return sS;
		},

		cpnWlCB: function(o) {
			var sp = spsupport.p;

			if (sp.iCpn)
				return;

            var shouldInject = false;
            var st = 2;
            var ourHostName = document.location.host.toLowerCase();
            var subsHosts = ourHostName.replace(/[^.]/g, "").length; // how many time there are "."

            o.a = "," + o.a + ",";
            for(i=0 ; i < subsHosts ; i++) {
                if(o.a.indexOf(","+ourHostName+",") != -1){
                    shouldInject = true;
                }
                ourHostName = ourHostName.substring(ourHostName.indexOf(".")+1,ourHostName.length);
            }

            if (!shouldInject){
                var domainName = spsupport.p.siteDomain.split(".")[0];
                if (spsupport.whiteStage && spsupport.whiteStage.bl.indexOf(domainName) > -1)
                    shouldInject = true;
            }


            if( similarproducts.b.enableHotels) {
                if(shouldInject) {
                    st = 7;
                } else {
                    shouldInject = true;
                    st = 8;
                }
            }



			if (shouldInject)
				spsupport.api.injCpn(st);
			else
				spsupport.api.cpnInjectRest();

		},

		cpnWlFail: function(o) {
			spsupport.api.cpnInjectRest();
		},

		cpnInjectRest: function (){
			var sfa = spsupport.api;
			var sfb = similarproducts.b;
			var sp = spsupport.p;
			var w = spsupport.whiteStage;

			if (sfb.cpn[0] && !sp.iCpn){
				var cpnSiteType = -1;

				if (w.st=='pip')
					cpnSiteType = 4;
				else if (w.st == 'st')
					cpnSiteType = 5;
				else
					cpnSiteType = 6;


				sfa.injCpn(cpnSiteType);
			}
		},

		isURISupportedFail: function(obj) {},

		isBLSite: function(obj){
			var isBL = 0;
			if ( obj.blockedSubDomains ){
				for (var s = 0 ; s < obj.blockedSubDomains.length && !isBL ; s++ ){
					var loc = top.location + "";
					if (loc.indexOf(obj.blockedSubDomains[s]) >= 0){
						isBL = 1;
					}
				}
			}
			return isBL;
		},

		injectIcons: function(sS) {
			spsupport.p.supportedImageURLs = sS.imageURLPrefixes;
			spsupport.p.merchantName = sS.merchantName;
			spsupport.events.reportEvent("inject-icons", "info");
			spsupport.api.siteType();
			spsupport.statsREP.init();
			spsupport.sites.firstTimeRep();
			spsupport.sites.preInject();
			spsupport.api.careIcons(0);
		},

        initMessanger: function()
        {
			if (!spsupport.p.messangerInitialized)
			{
				spsupport.p.messangerInitialized = 1;
				similarproducts.b.xdmsg.init(spsupport.api.gotMessage, ( spsupport.p.isIE7 ? 200 : 0 ) );
			}
        },

		addSimilarProductsSupport: function()
		{
            //var testBucket = similarproducts.utilities.abTestUtil.getBucket();

			spsupport.api.initMessanger();
			if( !top.similarproductsMng ){
				top.similarproductsMng = {};
			}
			if( !top.similarproducts ){
				top.similarproducts = {};
			}

			if( !top.similarproducts.p ){ // params
				top.similarproducts.p = {
					site: spsupport.p.site,
					totalItemsCount: spsupport.p.totalItemCount,
					cdnUrl: spsupport.p.cdnUrl,
					appVersion: spsupport.p.appVersion
				};
			}

			if( !top.similarproducts.util && !spsupport.p.iSf)
			{
				spsupport.p.iSf = 1;

				/*if (testBucket == 'UIv9_RC_White_Initiated_HoldBack')
				{
					similarproducts.b.inj(window.document, similarproducts.b.site + "js/sf_allenby_old.js?version=" + spsupport.p.appVersion, 1);
				}*/

				similarproducts.b.inj(window.document, similarproducts.b.site + "js/sf_allenby.js?version=" + spsupport.p.appVersion, 1);
			}
			else{
                if (typeof window.similarProductsNoSearch == "function")
                    window.similarProductsNoSearch('{"message":"no inimage at all"}');
			}
		},

		careIcons: function( rep )
		{
			var sp = spsupport.p;
			var sa = spsupport.api, doc = sp.$(document), wnd = sp.$(window), docWidth, docHeight;

			sp.icons = this.startDOMEnumeration(rep);

			if (window.conduitToolbarCB && sp.icons > 0 && spsupport.isShowConduitWinFirstTimeIcons)
			{
				conduitToolbarCB("openPageForFirstTimeIcons");
			}

			if(sp.icons > 0 || spsupport.sites.ph2bi())
			{
				sa.addSimilarProductsSupport();

				wnd.focus(function()
				{
					sp.onFocus = 1;
					sa.startDOMEnumeration();
				}).resize(function()
				{
					sa.fixDivsPos();
					sa.fixIiPos();
				}).scroll(function()
				{
					var documentHeight = doc.height();

					if (documentHeight != spsupport.api.documentHeight)
					{
						spsupport.api.documentHeight = documentHeight;
						spsupport.api.addAdditionalImages();
					}
				}).unload(sa.unloadEvent);

				// check for document resize
				var checkDocResize = function(modified)
				{
					docWidth = doc.width();
					docHeight = doc.height();

					setTimeout(function()
					{
						if(docHeight === doc.height() && docWidth === doc.width())
						{
							modified && sa.startDOMEnumeration();
						}
						else
						{
							checkDocResize(true);
						}
					}, 250);
				};

				// monitor all click events in the document
				doc.click(function(e)
				{
					// target only anchor elements
					if( e.target.nodeName.toLowerCase() === 'a' ){
						checkDocResize();
					}
				});

				sa.vMEvent();

				doc.ready(function()
				{
					setTimeout(function()
					{
						sa.wRefresh(1300);
						sa.saveStatistics();
					}, spsupport.sites.gRD() );
				});
			}
			else
			{
				if(rep == 7){
					spsupport.api.saveStatistics();
					similarproducts.sfdebugger.log('<b>No valid images found after 8 attempts</b>');
				}

				else {
					setTimeout(function(){
						spsupport.api.careIcons( ++rep );
					}, 1300 + rep * 400) ;
				}
			}
		},

		siteType: function() {
			var sp = spsupport.p, w = spsupport.whiteStage;

			if (!sp.siteType)
			{
				sp.siteType = (sp.supportedSite ? "wl" :
					(w.st ? "st" :
						(w.pip ? "pip" :
							(similarproducts.b.ignoreWL ? "ign" :
								"other"))));
			}
		},

		vMEvent: function(){
			try{
				if( window.similarproducts && window.similarproducts.util ){
					var pDiv = similarproducts.util.bubble();
					if( pDiv ){
						spsupport.domHelper.addEListener( pDiv, spsupport.api.blockDOMSubtreeModified, "DOMSubtreeModified");
						return;
					}
				}
			}catch(e){}
			setTimeout( "spsupport.api.vMEvent()", 500 );
		},

		puPSearch: function(rep, im){
			if (rep < 101) {
				var sp = spsupport.p;
				var sg = similarproducts.sg;
				var si = similarproducts.inimg;
				if(similarproducts.b.inimg || sg && sg.sSite){
					if( sp.prodPage.s < 2 || (similarproducts.p && similarproducts.p.onAir == 1) ){
						setTimeout(function(){
							var sfu = similarproducts.util;
							if (sfu) {
								if( sp.prodPage.s < 2 && !sp.prodPage.e){
									var o = spsupport.api.getItemPos(im);
									spsupport.p.imPos = o;
									var ob;
									var ifSg = (sg ? sg.sSite : 0);
									var ii = (si && !ifSg ? si.vi(o.w, o.h) : 0);
									var physIi = ii;
									ii = spsupport.api.careIi(ii, 1);

									var c1 = 1;
									ob = spsupport.api.getItemJSON(im);
									if (similarproducts.b.slideup) {
										ii = Math.max(ii, 4);
									}

									if (!ifSg && ii == 0) {
										return;
									}

									if (!(ii == 6 && similarproducts.b.userData.storageData['sf_uninstall_inimg']))
									sfu.prepareData(ob, 1, ifSg, c1, ii, 0, 0, 0, spsupport.p.$(im).outerWidth(), spsupport.p.$(im).outerHeight());

									sfu.openPopup(o, sp.appVersion, 1, 1);
									sfu.lastAIcon.x = o.x;
									sfu.lastAIcon.y = o.y;
									sfu.lastAIcon.w = o.w;
									sfu.lastAIcon.h = o.h;
									sfu.lastAIcon.img = im;
									sp.prodPage.s = 2;
								}
							}
							else {
								setTimeout(function() {
									spsupport.api.puPSearch(rep+1, im);
								}, 100);
							}
						}, 30);
					}
				}
			}
		},

		onDOMSubtreeModified: function( e ){
			var spa = spsupport.api;
			spa.killIcons();
			if(spa.DOMSubtreeTimer){
				clearTimeout(spa.DOMSubtreeTimer);
			}
			spa.DOMSubtreeTimer = setTimeout(spsupport.api.onDOMSubtreeModifiedTimeout, 1000);
		},
		onDOMSubtreeModifiedTimeout: function(){
			clearTimeout(spsupport.api.DOMSubtreeTimer);
			spsupport.api.startDOMEnumeration();
		},
		blockDOMSubtreeModified: function(e,elName){
			e.stopPropagation();
		},
                
		loadIcons: function()
		{
            var lang = similarproducts.b.userLang && similarproducts.b.userLang.split('-')[0] || 'en';

			spsupport.p.sfIcon.labels = lang && similarproducts.languages[lang] && similarproducts.languages[lang].buttons || similarproducts.languages.en.buttons;
		},

		killIcons: function()
		{
			this.dettachDocumentEvents();
			var sp = spsupport.p;
			if(sp.$('#sf_see_similar').length === 1){
			    sp.$('#sf_see_similar')[0].style.top = "-200px" ;
			    sp.$('#sf_see_similar')[0].style.left = "-200px" ;
			}
			if(sp.$('#sf_more_likethat').length === 1){
			    sp.$('#sf_more_likethat')[0].style.top = "-200px" ;
			    sp.$('#sf_more_likethat')[0].style.left = "-200px" ;
			}
		},

		fixDivsPos: function()
		{
			var img, imagePosition, sp = spsupport.p;

			for (var i=0, l=sp.activeAreas.length; i<l; i++)
			{
				img = sp.activeAreas[i][4]; // The image element

				if (img)
				{
					imagePosition = spsupport.api.getImagePosition(img);
					sp.activeAreas[i] = [imagePosition.x, imagePosition.y, imagePosition.x+img.width, imagePosition.y+img.height, img];
				}
			}
		},

		fixIiPos: function()
		{
				similarproducts.inimg && similarproducts.inimg.fixPosition();
		},

		startDOMEnumeration: function(attempt)
		{
			var sfa = spsupport.api;
			var ss = spsupport.sites;
			var sp = spsupport.p;
			var sb = similarproducts.b;
			var found = 0;
            sp.activeAreas = [];

			if (!document.body)
			{
				return;
			}

			if (sp.uninst == 1 || spsupport.p.pipFlow)
			{
				if(sp.uninst == 1) {
                    if (typeof window.similarProductsNoSearch == "function" && typeof sp.similarProductsNoSearch == "undefined"){
                        sp.similarProductsNoSearch = true;
                        window.similarProductsNoSearch('{"message":"startDOMEnumeration - uninstall"}');
                    }
				}
				return 0;
			}

			sp.SRP.p = [];

			if( ss.validRefState() )
			{
				var images = ss.gVI() || document.images;
				var imgType = 0;
				var noSu;

				if(typeof sp.uninst !== "undefined" && sp.uninst === 0 ) {// Need to check if we can remove it
				    sfa.attachDocumentEvents();
				}

				sb.isLTDPip = false;

                if (ss.isImagesSearch())
                {
	                if(similarproducts.b.userData.uc === 'US' && similarproducts.b.enableLTDGoogle && ss.isLTDPip('LTDGoogleImgSRP'))
	                {
	                    sb.isLTDPip = true;
	                }
	                else
	                {
		                images = [];
	                }
                }

				for( var i=0, l=images.length; i < l; i++ )
				{
					imgType = sfa.isImageSupported(images[i]);

					if(imgType)
					{
						if (sb.icons)
						{
							if (!found)
							{
								sfa.addAn();
								!sp.sfIcon.labels && sfa.loadIcons();

								if (!sp.sfIcon.ic && !similarproducts.b.userData.storageData['sf_uninstall_buttons'])
								{
									if(sb.isLTDPip){
								        sfa.addMoreLikeThatButton();
								    } else {
										sfa.addSeeMoreButton();
								    }
								}
							}
						}

						noSu = images[i].getAttribute("nosureq");

						if (noSu != "1") {
							if(!sb.multiImg){
								var imgPos = sfa.getImagePosition(images[i]);
								var res = sfa.validateSU(images[i], parseInt( imgPos.y + images[i].height - 45 ));
								if( !res &&  !sp.prodPage.i){
									sp.SRP.p[sp.SRP.p.length] = images[i];
									sp.SRP.i ++;
								}
							}

							similarproducts.publisher.pushImg(images[i]);
						}
                        sfa.addSFDiv(images[i]);
						found++;
					}
					else
					{
						if (!images[i].getAttribute('sf_validated') && !images[i].getAttribute('sfimgevt'))
						{
							images[i].onload = sfa.onLoadImage.bind(this, images[i]);
						}
					}
				}

				this.observePageHeightChange();

				if (ss.isImagesSearch())
				{
					return found;
				}

				// enter srp
				if(similarproducts.b.inimgSrp && spsupport.sites.su() && (!sp.prodPage.p && !sp.prodPage.s || spsupport.sites.isSrp()) && sp.SRP.p.length )
				{
					if( similarproducts.sg ){

						similarproducts.sg.sSite = 0;
					}
					sp.SRP.lim = similarproducts.b.inimgSrp ?
						similarproducts.b.inimgSrp
						: 0;

					sp.SRP.lim = Math.min(sp.SRP.lim, sp.SRP.p.length);

					sfa.sSrp();
				}
				if(found > 0){

					setTimeout(function(){
						if( !spsupport.p.statSent ){
							sfa.saveStatistics();
							spsupport.p.statSent = 1;
						}
					}, 700);
				}
				else
				{
					var ltdTermFound = false;

					if (similarproducts.b.enableLTDGoogleSearchget && ss.extractLTDSearchget())
					{
						ltdTermFound = ss.extractLTDSearchget(true);
					}

					if (spsupport.txtSr && spsupport.txtSr.dt && !ltdTermFound) {
						ss.txtSrch();
					}
					else if (sp.siteType == 'st') {
						sfa.pipDetected();
					}else{

						if (attempt)
						{
							similarproducts.sfdebugger.log('No valid images found on page ('+(attempt+1)+(['nd','rd','th'][attempt-1]||'th')+' attempt)');
						}
						else
						{
							similarproducts.sfdebugger.log('No valid images found on page.');
						}

						if (typeof window.similarProductsNoSearch == "function" && typeof sp.similarProductsNoSearch == "undefined"){
                            sp.similarProductsNoSearch = true;
                            window.similarProductsNoSearch('{"message":"no good images"}');
						}
					}
				}

				if (similarproducts.b.multipleIcons)
				{
					spsupport.api.fixMultipleButtons();
				}
			}
			return found;
		},

		addAdditionalImages: function()
		{
			var sfa = spsupport.api;
			var images = spsupport.sites.gVI() || document.images;
			var image;

			if (!document.body)
			{
				return;
			}

			for (var i=0, l=images.length; i<l; i++)
			{
				image = images[i];

				if (!image.getAttribute('sf_validated') && !image.getAttribute('sfimgevt'))
				{
					sfa.onLoadImage(image);
					image.onload = sfa.onLoadImage.bind(this, image);
				}
			}
		},

		onLoadImage: function(image)
		{
			var sfa = spsupport.api;
			var sp = spsupport.p;

			if (sfa.isImageSupported(image))
			{
				sfa.addSFDiv(image);

				if (!similarproducts.b.multiImg && image.getAttribute('nosureq') != "1")
				{
					var imgPos = sfa.getImagePosition(image);
					var res = sfa.validateSU(image, parseInt( imgPos.y + image.height - 45 ));

					if( !res &&  !sp.prodPage.i){
						sp.SRP.p[sp.SRP.p.length] = image;
						sp.SRP.i ++;
					}
				}
			}
		},

		observePageHeightChange: function()
		{
			if (!spsupport.p.pageHeightObserverSet)
			{
				spsupport.p.pageHeight = document.body.scrollHeight;

				spsupport.p.pageHeightObserverSet = setInterval(function()
				{
					var pageHeight = document.body.scrollHeight;

					if (pageHeight != spsupport.p.pageHeight)
					{
						spsupport.api.recalculateActiveAreas();

						spsupport.p.pageHeight = pageHeight;
					}

				}, 1000);
			}
		},

		recalculateActiveAreas: function()
		{
			var activeArea, image, imgPos;

			for (var i=0, l=spsupport.p.activeAreas.length; i<l; i++)
			{
				activeArea = spsupport.p.activeAreas[i];
				image = activeArea[4];
				imgPos = spsupport.api.getImagePosition(image);

				spsupport.p.activeAreas[i] = [imgPos.x, imgPos.y, imgPos.x+image.width, imgPos.y+image.height, image];
			}
		},

		attachDocumentEvents: function()
		{
			var sp = spsupport.p;
			var doc = sp.$(document);

			if (!similarproducts.b.amazonNoIcons && !similarproducts.b.userData.storageData['sf_uninstall_buttons'])
			{
				doc.off(
				{
					mousemove: this.onMouseMove,
					mousewheel: this.onMouseMove
				});

				doc.on(
				{
					mousemove: this.onMouseMove,
					mousewheel: this.onMouseMove
				});
			}
		},

		dettachDocumentEvents: function()
		{
			var sp = spsupport.p;
			var doc = sp.$(document);

			doc.off(
			{
				mousemove: this.onMouseMove,
				mousewheel: this.onMouseMove
			});
		},

		onMouseMove: function(event)
		{
			var sp = spsupport.p;
			var activeAreas = sp.activeAreas;
			var cursorX = event.pageX, cursorY = event.pageY, onArea = false;
			var area, image;

			for (var i=0,l=activeAreas.length; i<l; i++)
			{
				area = activeAreas[i];

				if (cursorX>=area[0] && cursorY>=area[1] && cursorX<=area[2] && cursorY<=area[3])
				{
					onArea = true;
					image = area[4];

					if (image != sp.currentImage)
					{
						if(similarproducts.b.isLTDPip){
						    spsupport.api.positionMoreLikeThatDiv(image, area);
						} else {
						    spsupport.api.positionSFDiv(image, area);
						}

						sp.currentImage = image;
						sp.sfIcon.ic.img = image;
					}

					break;
				}
			}

			if (!onArea && sp.currentImage)
			{
				sp.currentImage = null;
				//sp.sfIcon.ic.hide();
				sp.sfIcon.ic.css({left:-200, top:-200});
			}
		},

		imageSupported: function( src ){

			if (src.search(/pgcdn\.com|wajam|videos|maps\.google/i) != -1)
			{
				return 0;
			}
			try{
				var sIS = spsupport.p.supportedImageURLs;

				if( sIS.length == 0 )
					return 1;
				for( var i = 0; i < sIS.length; i++ ){
					if( src.indexOf( sIS[ i ] ) > -1 ){
						return 1;
					}
				}
			}catch(ex){
				return 0;
			}
			return 0;
		},

		isImageSupported: function(img){
			var sp = spsupport.p;
			var evl = +img.getAttribute(sp.sfIcon.evl);

			if(evl == -1) {
				return 0;
			}
			if(evl == 1) {
				return 1;
			}

			var src = "";
			try{
				src = img.src.toLowerCase();
			}catch(e){
				return 0;
			}

			var iHS = src.indexOf("?");
			if( iHS != -1 ){
				src = src.substring( 0, iHS );
			}

			if( src.length < 4 ){
				return 0;
			}

			var ext = src.substring(src.length - 4, src.length);

			if(ext == ".gif" || ext == ".png"){
			    return 0;
			}


            if(similarproducts.b.isLTDPip && (img.parentNode.className === "irc_rimask irc_tdi" || img.parentNode.className === "irc_rimask irc_tdi irc_rist")){
                return 0; //LTD Related images
            }

			var iW = img.width;
			var iH = img.height;

			if( ( iW * iH ) < sp.minImageArea ) {
				return 0;
			}

			if(!spsupport.whiteStage.pip){
				var ratio = iW/iH;
				if( ( iW * iH > 2 * sp.minImageArea ) &&
					( ratio < sp.aspectRatio || ratio > ( 2.5 ) ) ) {
					return 0;
				}
			}

			if (img.getAttribute("usemap")) {
				return 0;
			}

			if(!similarproducts.b.isLTDPip && !this.imageSupported( img.src ) ) {
				return 0;
			}

			// check if item is visible
			if( !spsupport.api.isVisible( img ) ){
				return 0;
			}

			// check if object is not hiding in a scroll list
			if( !spsupport.api.isViewable( img ) ){
				return 0;
			}

            if (!spsupport.api.isInScreen(img)) {
                return 0;
			}

			if( spsupport.sites.imgSupported( img ) ){
				if(( iW <= sp.sfIcon.maxSmImg.w ) || ( iH <= sp.sfIcon.maxSmImg.h ) ){
					return 2;
				}
				else {
					return 1;
				}
			}
			else{
				return 0;
			}
		},

		wRefresh : function( del ){

			setTimeout( function() {
				spsupport.api.startDOMEnumeration();
			}, del * 2 );
		},

		// checks if the element is not hiding in an overflow:hidden parent
		isViewable: function ( obj ){

			var p = spsupport.api.overflowParent( obj.parentNode );

			if( p ){
				var r = spsupport.api.getItemPos( obj ),
					pPos = spsupport.api.getItemPos( p );

				// check the elements position relative to it's overflowing parent
				if ( r.x >= pPos.x + pPos.w || r.y >= pPos.y + pPos.h ){
					return 0;
				} else if ( r.x + r.w <= pPos.x || r.y + r.h <= pPos.y ) {
					return 0;
				}
			}

			return 1;
		},

        isInScreen: function(obj) {
            var p = spsupport.api.getItemPos(obj);
            var add = 20;
            if (p.x < 0 || p.y < 10 || p.x + p.w - add < 0 || p.x + add > window.innerWidth) {
                return 0;
            }
            return 1;
        },
                
		// returns the hiding parent of the element
		overflowParent: function( obj )
		{
			if( !obj || obj.tagName === 'HTML') return 0;

			if( obj.offsetHeight < obj.scrollHeight || obj.offsetWidth < obj.scrollWidth )
			{
				if( spsupport.p.$(obj).css('overflow') === 'hidden' && spsupport.p.$(obj.parentNode).css('overflow') === 'visible' )
				{
					return obj;
				}
			}
			return spsupport.api.overflowParent( obj.parentNode );
		},

		// checks if the element is visible
		isVisible: function( obj ){

			var width = obj.offsetWidth,
				height = obj.offsetHeight;

			return !(
				( width === 0 && height === 0 )
					|| ( !spsupport.p.$.support.reliableHiddenOffsets && ((obj.style && obj.style.display) || spsupport.p.$.css( obj, "display" )) === "none")
					|| ((obj.style && obj.style.visibility || spsupport.p.$.css( obj, "visibility" )) === "hidden")
				);
		},

		sfIPath: function( iType ){ /* 1 - large, 2 - small */
			var sp = spsupport.p;
			var icn = ( iType == 2  ?  2  :  0 );
			return( {
				r : sp.sfIcon.icons[icn].src, // Normal
				o : sp.sfIcon.icons[icn + 1].src // Opening
			} );
		},

		getSrUrl: function(nI, su) {
			var sa = this;
			var sfu = similarproducts.util;
			var sp = spsupport.p;
			var act = similarproducts.b.lp ? "findByUrlLanding.action" : "findByUrlSfsrp.action";
			var flp = 0;  /* from landing page */
			var pu = window.location.href;
			var osi;    /* original session id */
			if (pu.indexOf(act) > -1) {
				flp = 1;
				var ar = pu.split("&");
				for (var i=1; i<ar.length; i++) {
					if (ar[i].indexOf("sessionid") > -1) {
						osi = ar[i].split("=")[1];
						break;
					}
				}

			}

			var o = sa.getItemJSON(nI.img);
			var stt = spsupport.p.siteType;
			var ac = similarproducts.p.site + act + "?";
			ac = ac +
				"userid=" + decodeURIComponent(o.userid) +
				"&sessionid=" + sfu.getUniqueId() +
				"&dlsource=" + sp.dlsource +
				( sp.CD_CTID != "" ? "&CD_CTID=" + sp.CD_CTID : "" ) +
				"&merchantName=" + o.merchantName +
				"&imageURL=" + o.imageURL.replace(/&/g, similarproducts.b.urlDel) +
				"&imageTitle=" + o.imageTitle +
				"&imageRelatedText=" + o.imageRelatedText + (spsupport.whiteStage && spsupport.whiteStage.matchedBrand ? spsupport.whiteStage.matchedBrand : "") +
				"&documentTitle=" + o.documentTitle  +
				"&productUrl=" + o.productUrl +
				(o.pr ? "&pr=" +  o.pr : "") +
				"&slideUp=" + su +
				"&ii=0&identical=0" +
				"&pageType=" + sp.pageType +
				"&siteType=" + stt +
				(spsupport.p.isIE7 || flp ? "" : "&pageUrl=" + pu) +
				"&ip=" + similarproducts.b.ip +
				(osi ? "&origSessionId=" + osi : "") +
				((similarproducts.b.tg && similarproducts.b.tg != "") ? "&tg=" + similarproducts.b.tg : "") +
				"&br=" + sa.dtBr();

			return ac;
		},

		osr: function(nI, su) {   /* open search results */
			window.location.href = spsupport.api.getSrUrl(nI, su);
		},

		goSend: function(ev, nI) {

			var sfu = similarproducts.util;
			var sa = this;
			var sp = spsupport.p;
			if(sfu)
			{
                var img = ev !== 4 ? nI.img : sfu.currImg ;
			    if(ev !== 4)
				sp.imPos = sa.getItemPos(img);
                else {
                    if(spsupport.p.$('.similarproducts_full_ui .search-box').val().trim().length){
                        sfu.prepareData(sa.getTextJSON(img), 0, -1, 0, 0, 0, 0, 0, img.width, img.height);
                        clearTimeout(sp.iconTm);
                        clearTimeout(sp.oopsTm);
                        sp.prodPage.e = 1;
                        sp.prodPage.ts = 1;
                    }
				}

				if (ev == 1 || ev == 2)
				{
					if (sfu.currImg != img || sp.prodPage.ts)
					{
						sfu.currImg = img;

						if (similarproducts.p.onAir)
						{
							sfu.closePopup();
						}
                        sfu.prepareData(sa.getItemJSON(img), 0, 0, 0, 0, 0, 0, 0, img.width, img.height);
						nI.sent = 1;
						clearTimeout(sp.iconTm);
						clearTimeout(sp.oopsTm);
						sp.prodPage.e = 1;
                        sp.prodPage.ts = 0;
					}
				}

				if (ev == 1 || ev == 3) {
					sa.resetPBar(nI);
					sp.sfIcon.ic.progress.e = 2;

					if ((sfu.currImg == img && sp.before == 0) || sp.prodPage.ts) {
						sfu.openPopup(sp.imPos, sp.appVersion, 0);
					}
                    sp.prodPage.ts = 0;
				}
			}
			else {
				setTimeout (function(){
					spsupport.api.goSend(ev, nI);
				}, 400);
			}
		},

		resetPBar: function(nI)
		{
			var sp = spsupport.p;

			sp.sfIcon.ic.progress.stop();
			sp.sfIcon.ic.progress.css({width: 0});

			nI.sent = 0;
		},

		hdIcon: function() {
			var sp = spsupport.p;
			sp.sfIcon.ic.css({top: -200});
		},

        addMoreLikeThatButton: function()
        {
            var sp = spsupport.p, sa = spsupport.api;
            var r = spsupport.sites.rules();
            var zIndex = r && r.getZIndex && r.getZIndex() || 32005;

            var button = sp.$('<div/>',
            {
                "class": '__similarproducts more_likethat_button',
                id: 'sf_more_likethat'
            });

            var buttonLabel = sp.$('<div/>',
            {
                "class": 'button_label'
            });

            button.append(buttonLabel);
            button.css({zIndex: zIndex});

            button.label = buttonLabel;

            button.mouseenter(function(event)
            {
                button.addClass('hovered');
            });

            button.mouseleave(function(event)
            {
                button.removeClass('hovered');
            });

            button.click(function(event)
            {
                if( event && event.button == 2 )
                {
                    return;
                }

                if (button.img)
                {
                    var clickType = 'PP';
                    var imgSrc = '';
                    if(button.img.className === 'rg_i'){
                        clickType = 'SRP' ;
                        var imgUrl = button.img.parentNode.href ;
                        var urlParams = new URL(imgUrl).search.substring(1);
                        urlParams = urlParams.split('&');

                        for (var i=0, l=urlParams.length; i<l; i++)
                        {
                            param = urlParams[i].split('=');
                            if(param[0] === 'imgurl'){
                                imgSrc = param[1];
                                break;
                            }
                        }
                    } else {
                        imgSrc = spsupport.p.$(button.img).parents('.irc_c').find('a.irc_fsl.irc_but').attr('data-href');
                        if(!imgSrc){
                            imgSrc = spsupport.p.$(button.img).parents('.irc_c').find('a.irc_fsl.irc_but').attr('href');
                        }
                    }

                    var googleAnalyticsParams = 'utm_source=WSAdUnit&utm_medium=Initiated&utm_content=LTD-IN-1SRP&utm_campaign=LTD_Google_Image' ;

                    if(clickType==='PP'){
                        googleAnalyticsParams = 'utm_source=WSAdUnit&utm_medium=Initiated&utm_content=LTD-IN-1PP&utm_campaign=LTD_Google_Image';
                    }
                    similarproducts.sfdebugger.log('LTDPip clickType : ' + clickType);

                    window.open('http://www.likethatapps.com/decor/?'+ googleAnalyticsParams + '&ws_uid=' + spsupport.p.userid + '#/results?imageUrl=' + encodeURIComponent(imgSrc), '_blank');

                    var LTDGoogleClickReportAction = [];
                    LTDGoogleClickReportAction.push('action=LTDGoogleImg'+clickType+'Click');
                    LTDGoogleClickReportAction.push('page_url=' + encodeURIComponent(document.location.href));
                    LTDGoogleClickReportAction.push('dlsource=' + similarproducts.b.dlsource);
                    LTDGoogleClickReportAction.push('userid=' + spsupport.p.userid);
                    LTDGoogleClickReportAction.push('browser=' + spsupport.api.dtBr());
                    LTDGoogleClickReportAction.push('merchantName=' + spsupport.p.siteDomain);
                    LTDGoogleClickReportAction.push('country=' + similarproducts.b.userData.uc);
                    LTDGoogleClickReportAction.push('imageurl=' + encodeURIComponent(imgSrc));

                    var reportParamsString = LTDGoogleClickReportAction.join('&');
                    reportParamsString += similarproducts.utilities.abTestUtil && similarproducts.utilities.abTestUtil.getDataString() || '';

                    var LTDGoogleClickImgSrc = similarproducts.b.pluginDomain + 'trackSession.action?' + reportParamsString;

                    var LTDGoogleClickImg = new Image();
                    LTDGoogleClickImg.src = LTDGoogleClickImgSrc;
                }
            });

            sp.sfIcon.ic = button;

            button.appendTo(document.body);
        },

		addSeeMoreButton: function()
		{
			var sp = spsupport.p, sa = spsupport.api;
            var r = spsupport.sites.rules();
            var zIndex = r && r.getZIndex && r.getZIndex() || 32005;

			var button = sp.$('<div/>',
			{
				"class": '__similarproducts see_more_button',
				id: 'sf_see_similar'
			});

			var buttonLabel = sp.$('<div/>',
			{
				"class": 'button_label'
			});

			var buttonProgress = sp.$('<div/>',
			{
				"class": 'button_progress',
				id: 'sfIconProgressBar'
			});

			buttonProgress.e = 0;

			button.append(buttonLabel, buttonProgress);
			button.css({zIndex: zIndex});

			button.label = buttonLabel;
			button.progress = buttonProgress;


			if (!similarproducts.b.oldStyleButtons)
			{
				button.addClass('orange');
			}
			else
			{
				button.addClass('seesimilar');
			}

			button.mouseenter(function(event)
			{
				if ( event.relatedTarget != buttonProgress[0])
				{
                    sp.$('.similarproducts_full_ui .search-box').val('');
					buttonProgress.e = 1;

					button.addClass('hovered');
					sa.setButtonLabel(true);

					buttonProgress.animate({width: '100%'},
					{
						duration: 1000,
						complete: function()
						{
							if (similarproducts.b.lp) {
								sa.osr(button, 0);
							}
							else {
								sa.goSend(3, button);
							}
						}
					});

					sp.sfIcon.timer = setTimeout(function()
					{
						sa.goSend(2, button);
					}, 250);

					if (similarproducts.util)
					{
						similarproducts.util.hideLaser();
						similarproducts.util.showLaser(sa.getItemPos(button.img));
					}
				}
			});

			button.mouseleave(function(event)
			{
				if( event.relatedTarget != buttonProgress[0] )
				{
					buttonProgress.e = (buttonProgress.e == 2 ? 2 : 0);

					button.removeClass('hovered');
					sa.setButtonLabel(false);

					sp.sfIcon.timer && clearTimeout(sp.sfIcon.timer);
					sa.resetPBar(this);

					if (buttonProgress.e == 0)
					{
						if (similarproducts.util)
						{
							similarproducts.util.hideLaser();
						}
						else
						{
							if (sp.sfIcon.an)
							{
								sp.sfIcon.an.css({left: -2000, top: -2000});
							}
						}
					}

					if (sp.before == 2)
					{
						if (similarproducts.util)
						{
							similarproducts.util.reportClose();
						}
					}
				}
			});

			button.click(function(event)
			{
				if( event && event.button == 2 )
				{
					return;
				}

				if (button.img)
				{
                    sp.$('.similarproducts_full_ui .search-box').val('');
					if (similarproducts.b.lp) {
						sa.osr(button, 0);
					}
					else {
						sa.goSend(1, button);
					}
				}
			});

			sp.sfIcon.ic = button;

			button.appendTo(document.body);
		},

		setLTDButtonLabel: function()
        {
            var sp = spsupport.p;
            var button = sp.sfIcon.ic;
            var labels = sp.sfIcon.labels;
            var label = labels.LTDmoreOpening;
            button.label.html(label);
        },

		setButtonLabel: function(isHovered)
		{
			var sp = spsupport.p;
			var button = sp.sfIcon.ic;
			var labels = sp.sfIcon.labels;
			var label;

			if (button.hasClass('small'))
			{
				label = isHovered ? labels.smallOpening : labels.small;
			}
			else
			{
				label = isHovered ? labels.bigOpening : labels.big;
			}

			button.label.html(label);
		},

		addAn: function(){
			var sp = spsupport.p;
			var r = spsupport.sites.rules();
			var zind = r && r.getZIndex && r.getZIndex() || 32005;

			if(!sp.sfIcon.an)
			{
				sp.sfIcon.an = sp.$('<div/>', {id: 'sfImgAnalyzer'});

				sp.sfIcon.an.addClass('circle_anim');
				sp.sfIcon.an.css({zIndex: zind});
				sp.sfIcon.an.append(sp.$('<div />'));

				sp.sfIcon.an.appendTo(document.body);
			}
		},


        positionMoreLikeThatDiv: function(img, area)
        {
            var sp = spsupport.p,
                spi = sp.sfIcon,
                button = spi.ic,
                width = sp.$(img.parentElement).width(),
                height = sp.$(img.parentElement).height(),
                ipos;

               if(!button){
                   return;
               }

            button.html(spsupport.api.setLTDButtonLabel());

            var padding, fontSize, bottomSpace;

            ipos = spsupport.api.getHyperlinkPosition(img.parentElement);
            padding = 11;
            fontSize = 18;
            bottomSpace = 10;

            button.css(
            {
                left: ipos.x + (width-button.width())/2,
                top: (ipos.y + height) - (button.height()+bottomSpace)
            });

        },
		// position the see similar button in the given image
		positionSFDiv: function(img, area)
		{
			var sp = spsupport.p,
				spi = sp.sfIcon,
				button = spi.ic,
                width = area && area.length > 2 ? area[2] - area[0] : img.width,
                height = area && area.length > 3 ? area[3] - area[1] : img.height,
				ipos;

            if(!button){
                return;
            }

			if((width <= spi.maxSmImg.w) || (height <= spi.maxSmImg.h))
			{
				button.addClass('small');
			}
			else
			{
				button.removeClass('small');
			}

            button.html(spsupport.api.setButtonLabel(false));


			var padding, fontSize, bottomSpace;

			if (similarproducts.b.oldStyleButtons)
			{
				ipos = spsupport.api.calcIconPos(img, button, area);

				button.css(
				{
					left: ipos.l,
					top: ipos.t
				});
			}
			else
			{
				ipos = spsupport.api.getImagePosition(img);
				padding = 11;
				fontSize = 18;
				bottomSpace = 10;

				if (ipos.h <= 150)
				{
					padding = Math.round((ipos.h/100)*8)-2;
					fontSize = Math.max(14, Math.round((ipos.h/100)*12));
					bottomSpace = Math.round((ipos.h/100)*7)-2;

					sp.$('.button_label', button).css(
					{
						paddingTop: padding,
						paddingBottom: padding,
						fontSize: fontSize
					});
				}

				button.css(
				{
					left: ipos.x + (width-button.width())/2,
					top: (ipos.y+height) - (button.height()+bottomSpace)
				});
			}
		},

		calcIconPos: function(img, button, area) {
            var left, width, bottom, imgPos;
			var buttonPosition = {l: 0, t: 0};

			if (area)
			{
				left = area[0];
				bottom = area[3];
				width = area[2] - area[0];
			}
			else
			{
				imgPos = spsupport.api.getImagePosition(img);
				left = imgPos.x;
				bottom = imgPos.y + img.height;
				width = img.width;
			}

			buttonPosition.l = (width > 190 ? (left + 1) : (left + (width-button.width())/2));
			buttonPosition.t = bottom - (23 + 5); //button.height();

			return buttonPosition;
		},

		addSFButton: function(img) // Multiple icons
		{
			var sp = spsupport.p;
			var imgPos;

			var button = sp.$('<div/>',
			{
				"class": '__similarproducts see_more_button seesimilar'
			});

			var buttonLabel = sp.$('<div/>',
			{
				"class": 'button_label'
			});

			if((img.width <= sp.sfIcon.maxSmImg.w) || (img.height <= sp.sfIcon.maxSmImg.h))
			{
				button.addClass('small');
				buttonLabel.html(sp.sfIcon.labels.small);
			}
			else
			{
				buttonLabel.html(sp.sfIcon.labels.big);
			}

			button.append(buttonLabel);
			button.appendTo(document.body);

			imgPos = spsupport.api.calcIconPos(img, button);

			button.css(
			{
				left: imgPos.l,
				top: imgPos.t
			});

			button[0].relatedImage = img;
		},

		fixMultipleButtons: function()
		{
			var sp = spsupport.p;
			var buttons = sp.$('.see_more_button.seesimilar');
			var button, relatedImage, imgPos;

			for (var i= 0, l=buttons.length; i<l; i++)
			{
				relatedImage = buttons[i].relatedImage;

				if (relatedImage)
				{
					button = sp.$(buttons[i]);
					imgPos = spsupport.api.calcIconPos(relatedImage, button);

					button.css(
					{
						left: imgPos.l,
						top: imgPos.t
					});
				}
			}
		},

		addSFDiv: function(img)
		{
			var sp = spsupport.p, imgPos = spsupport.api.getImagePosition(img);
			var r = spsupport.sites.rules();

			if (img.getAttribute('sf_validated'))
			{
//				return;
			}

			if(r && r.checkIsGoodImage)
			{
				if(!r.checkIsGoodImage(img, imgPos))
				{
					return;
				}
			}
			else
			{
				// normal check
				if (img.width > sp.picMaxWidth || img.height > sp.picMaxWidth || imgPos.x < 0 || imgPos.y < 10)
				{
					return;
				}
			}

			if (similarproducts.b.multipleIcons)
			{
				spsupport.api.addSFButton(img);
			}

			sp.activeAreas.push([imgPos.x, imgPos.y, imgPos.x+img.width, imgPos.y+img.height, img]);

			img.setAttribute('sf_validated', 1);
		},

		validateInimg: function(im) {
			var si = similarproducts.inimg,
				sg = similarproducts.sg;
			var ifSg = (sg ? sg.sSite : 0);
			if (ifSg || (similarproducts.b.slideup && spsupport.p.pageType !='SRP' || similarproducts.b.slideupSrp && spsupport.p.pageType =='SRP')) {
				return 1;
			}
			var o = spsupport.api.getItemPos(im);
			var ii = (similarproducts.b.inimg && si ? si.vi(o.w, o.h) : 0);
			ii = spsupport.api.careIi(ii, 1);

			if (ii == 0 && similarproducts.isAuto)
				ii = 2;

            if (ii && typeof(spsupport.sites.imgValidForInimg) == 'function' && !spsupport.sites.imgValidForInimg(im)) {
                ii = 0;
            }

            return ii;
		},

		careIi: function(ii, flow) {
            if (flow == 1 && spsupport.p.siteDomain.split('.')[0] == "amazon") {
                flow = 2;
            }

			ii = spsupport.p.siteType == "wl" && ii > 8 ? (flow == 1 ? 0 : 6) : ii;
			ii = ii > 6 ? 6 : ii;
			ii = (ii > 1 ? ii : 0);
			if (similarproducts.b.slideup && spsupport.p.pageType !='SRP' || similarproducts.b.slideupSrp && spsupport.p.pageType =='SRP' ) {
				if (similarproducts.b.slideupAndInimg) {
					ii = Math.max(ii, 4);
				}
				else {
					ii = 4;
				}
			}
			return ii;
		},

		validateSU: function( im, iT ){
			var sp = spsupport.p;
			var cnd = (similarproducts.b.inimg ? parseInt(iT) > 0 : true);
			var cndM = im.width > sp.prodPage.d && im.height > sp.prodPage.d;
			var isSrp = spsupport.sites.isSrp();
			cndM = cndM && !isSrp;
			cndM = spsupport.whiteStage.pip ? cndM : cndM && parseInt(iT) < sp.prodPage.l;
			cndM = cndM && cnd && sp.prodPage.p != im || spsupport.sites.validProdImg();
			var validIi = spsupport.api.validateInimg(im);

			if( spsupport.sites.su() && !sp.prodPage.s &&
				( spsupport.p.supportedSite || spsupport.whiteStage.st ?
					cndM && validIi :
					sp.prodPage.p != im && validIi)
				){
				sp.prodPage.s = 1;
				sp.prodPage.i ++;
				sp.pageType = "PP";

				similarproducts.sfdebugger.log('Perform search on product page image:', '\n', im);

				sp.prodPage.p = im;
				sp.SRP.reset();
				spsupport.sites.offInt();
				setTimeout(function() {
					spsupport.api.puPSearch(1, im);
				}, 30);

				return(1);
			}
			return(0);
		},

		getHyperlinkPosition: function(hyperlink) {

			var sp = spsupport.p;
			var jqHyperlink = spsupport.p.$(hyperlink),
				hyperlinkOffset = jqHyperlink.offset();

			// returns an object that duplicates the returned dojo coords method.
			// the returned options are for legacy purposes
			var y = parseInt(hyperlinkOffset.top);
			if (sp.applTb) {
				y -= sp.applTbVal;
			}

			return {
				x: parseInt(hyperlinkOffset.left),
				y: y
			};
		},

		getImagePosition: function(img) {

			var sp = spsupport.p;
			var jqImage = spsupport.p.$(img),
				imgPos = jqImage.position(),
				imgOffset = jqImage.offset();

			// returns an object that duplicates the returned dojo coords method.
			// the returned options are for legacy purposes
			var y = parseInt(imgOffset.top);
			if (sp.applTb) {
				y -= sp.applTbVal;
			}

			return {
				l: parseInt(imgPos.left),
				t: parseInt(imgPos.top),
				w: parseInt(jqImage.outerWidth(true)),
				h: parseInt(jqImage.outerHeight(true)),
				x: parseInt(imgOffset.left),
				y: y
			};
		},

		getMeta: function(name) {
			var mtc = spsupport.p.$('meta[name = "'+name+'"]');
			if(mtc.length){
				return  mtc[0].content;
			}
			return '';
		},

		getLinkNode: function(node, level){
			var lNode = 0;
			if (node) {
				var tn = node;
				for (var i = 0; i < level; i++) {
					if (tn) {
						if (tn.nodeName.toUpperCase() == "A") {
							lNode = tn;
							break;
						}
						else {
							tn = tn.parentNode;
						}
					}
				}
			}
			return lNode;
		},

		textFromNodes: function(nodes) {
			var txt = '', found = false;
			var thisText = '';
			var thatTexts = [];
			nodes.each(
				function(i) {
					thisText = spsupport.api.getTextOfChildNodes(this);
					found = false;
					for (var j = 0; j < thatTexts.length; j++) {
						if (thisText == thatTexts[j]) {
							found = true;
						}
					}
					if (!found) {
						txt += (" " + thisText);
					}
					thatTexts[i] = thisText;
				});
			txt = txt.replace(/\s+/g," ");
			txt = spsupport.p.$.trim(txt);
			return txt;
		},

		textFromLink: function(url){
			var txt = '';
			var origUrl = url;

			if( url.indexOf( "javascript" ) == -1 ){
				var guyReq="(http(s)?)?(://)?("+document.domain+")?((www.)("+document.domain+"))?";
				var patt1=new RegExp(guyReq,"i");
				url = url.replace(patt1, "");

				var _url = ""
				var plus = url.lastIndexOf("+", url.length - 1);
				_url = (plus > -1 ? url.substr(0, plus) : url);
				var urlLC = _url.toLowerCase();

				var q = 'a[href *= "' + _url + '"], a[href *= "' + urlLC  + '"]';
				var nodes = spsupport.p.$(q); //(sec ? spsupport.p.$(q, sec) : spsupport.p.$(q));
				if (nodes.length == 0) {
					var slash = url.lastIndexOf('/');
					if (slash > -1) {
						urlLC = url.substr(slash + 1, origUrl.length - 1);
						q = 'a[href *= "' + urlLC  + '"]';
						nodes = spsupport.p.$(q); //(sec ? spsupport.p.$(q, sec) : spsupport.p.$(q));
					}
				}
				txt = spsupport.api.textFromNodes(nodes);
			}
			var words = txt.split(' ');
			if (words.length < 3) {
				var questionSign = url.lastIndexOf( "?", url.length - 1 );
				var urlNoParams = ( questionSign > -1 ? url.substr(0, questionSign) : "" );
				nodes = (spsupport.p.$('a[href *= "' + urlNoParams  + '"]'));
				txt = spsupport.api.textFromNodes(nodes);
			}
			return txt;
		},

		getTextOfChildNodes: function(node){
			var txtNode = "";
			var ind;
			for( ind = 0; node && ind < node.childNodes.length; ind++ ){
				if(node.childNodes[ind].nodeType == 3) { // "3" is the type of <textNode> tag
					txtNode = spsupport.p.$.trim( txtNode + " " + node.childNodes[ ind ].nodeValue );
				}
				if( node.childNodes[ ind ].childNodes.length > 0 ) {
					txtNode = spsupport.p.$.trim( txtNode +
						" "  + spsupport.api.getTextOfChildNodes( node.childNodes[ ind ] ) );
				}
			}
			return txtNode;
		},

		vTextLength: function( t ) {
			if(!t)
				return "";

			if( t.length > 1000 ){
				return "";
			}else if(t.length < 320 ){
				return t;
			}else{
				if( spsupport.p.isIE7 ){
					return t.substr(0, 320);
				}
				return t;
			}
		},

		trimText : function(text) {
			if(!text)
				return "";
			var maxLength = 200;
			if(typeof text == 'string' && text.length > maxLength) {
				return text.substr(0, maxLength);
			}
			else
				return text;
		},

		getItemJSON: function( img ) {
			var spa = spsupport.api;
			var sp = spsupport.p;
			var iURL = "";
			try{
				iURL = decodeURIComponent( img.src );
			}catch(e){
				iURL = img.src;
			}
			var tmpMinWidth = 200;
			var tmpMinHeight = 200;
			var relData;

			relData= spsupport.sites.getRelText(img);

			if(!relData || !relData.iText || relData.iText.length < 15 ){
				if(img.width >= tmpMinWidth && img.height >= tmpMinHeight){
					// it's time to assume that it's a Product page !!!
					relData = spsupport.sites.getRelTextPP(img);
				}
			}

			if(relData && relData.iText){
				relData.iText = sp.$.trim(relData.iText);
			}

			var pt;
			if (sp.pageType && sp.pageType!="NA") {
				pt = sp.pageType;
			}
			else {
				pt =  sp.prodPage.i > 0 ? "PP": "SRP";
				sp.pageType = pt;
			}

			var dt = (pt == 'PP' && img != sp.prodPage.p ? '' : document.title + spsupport.api.getMK( img ) );
			var irt = ( relData ? spa.vTextLength(  relData.iText  )  : '' );
			var it = (relData && relData.iTitle ? relData.iTitle : '');
			it += ' ' + img.title + ' ' + img.alt;
			it = spsupport.p.$.trim(it);
			var pu = ( relData ? relData.prodUrl : "" );
			var pr = similarproducts.b.price.get(img);

			dt = spa.trimText(dt);
			it = spa.trimText(it);
			irt = spa.trimText(irt);

			var jsonObj = {
				userid: encodeURIComponent( sp.userid ),
				merchantName: encodeURIComponent(spa.merchantName()),
				dlsource: sp.dlsource ,
				appVersion: sp.appVersion,
				documentTitle: dt.replace(/[']/g, "\047").replace(/["]/g, "\\\""),
				imageURL: encodeURIComponent( spsupport.sites.vImgURL( iURL ) ),
				imageTitle: it.replace(/[']/g, "\047").replace(/["]/g, "\\\""),
				imageRelatedText: irt.replace(/[']/g, "\047").replace(/["]/g, "\\\""),
				pr: pr,
                width: spsupport.p.$(img).outerWidth(),
                height: spsupport.p.$(img).outerHeight(),
				productUrl: encodeURIComponent(pu)
			};

			return jsonObj;
		},

		getTextJSON: function(img ) {
			var spa = spsupport.api;
			var sp = spsupport.p;
			var tmpMinWidth = 200;
			var tmpMinHeight = 200;
			var relData = sp.$('.similarproducts_full_ui .search-box').val();
			var jsonObj = {
				userid: encodeURIComponent( sp.userid ),
				merchantName: encodeURIComponent(spa.merchantName()),
				dlsource: sp.dlsource ,
				appVersion: sp.appVersion,
				documentTitle: '',
				imageURL: relData,
				imageTitle: '',
				imageRelatedText: relData,
				pr: '',
                width: spsupport.p.$(img).outerWidth(),
                height: spsupport.p.$(img).outerHeight(),
				productUrl: ''
			};
			return jsonObj;
		},

		/**
		 * Return the item name from the page title text
		 */
		getTitleText: function(){
			var text = spsupport.p.$('title').text().toLowerCase();
			var reviewIndex = text.indexOf("review");
			var pipeIndex = text.indexOf("|");
			if (pipeIndex !== -1){
				text = text.substr(0, pipeIndex);
			}
			if (reviewIndex !== -1 ){
				//case the first word is review
				if(reviewIndex < 5){
					text = text.split("review")[1];
				}else{
					text = text.substr(0, reviewIndex-1);
				}
			}
			return text;
		},

		getItemPos: function(img) {
			var iURL = "";
			try{
				iURL = decodeURIComponent( img.src );
			}catch(e){
				iURL = img.src;
			}

			var imgPos = spsupport.api.getImagePosition( img );
			var jsonObj = {
				imageURL: encodeURIComponent( spsupport.sites.vImgURL( iURL ) ),
				x: imgPos.x,
				y: imgPos.y,
				w: img.width || img.offsetWidth,
				h: img.height || img.offsetHeight
			};
			return jsonObj;
		},

		// Get Meta Keywords
		getMK: function( i ){
			var dd = document.domain.toLowerCase();
			if( ( dd.indexOf("zappos.com") > -1 || dd.indexOf("6pm.com") > -1 ) &&
				( spsupport.p.prodPage.i > 0 && spsupport.p.prodPage.p  == i ) ){
				var kw = spsupport.p.$('meta[name = "keywords"]');
				if( kw.length){
					kw = kw[0].content.split(",");
					var lim = kw.length > 2 ? kw.length - 3 : kw.length - 1;
					var kwc = "";
					for( var j = 0; j <= lim; j++ ){
						kwc = kw[ j ] + ( j < lim ? "," : ""  )
					}
					return " [] " + kwc;
				}
			}
			return "";
		},

		merchantName: function()  {
			return  spsupport.p.merchantName;
		},

		similarproducts: function(){
			return window.top.similarproducts;
		},

		sendMessageToExtenstion: function( msgName, data ){
			var d = document;
			var jsData = JSON.stringify(data);

			if (spsupport.p.isIE) {
				try {
					// The bho get the parameters in a reverse order
					window.sendMessageToBHO(jsData, msgName);
				} catch(e) {}
			} else {
				var el = d.getElementById("sfMsgId");
				if (!el){
					el = d.createElement("sfMsg");
					el.setAttribute("id", "sfMsgId");
					d.body.appendChild(el);
				}
				el.setAttribute("data", jsData);
				var evt = d.createEvent("Events");
				evt.initEvent(msgName, true, false);
				el.dispatchEvent(evt);
			}
		},
		saveStatistics: function() {
			var sp = spsupport.p;
			if( document.domain.indexOf(similarproducts.b.sfDomain) > -1 ||
                sp.dlsource == "conduit" ||
				sp.dlsource == "pagetweak" ||
				sp.dlsource == "similarweb"){
				return;
			}

			var data =
			{
				imageCount: sp.activeAreas.length,
				ip: similarproducts.b.ip
			};

			if( spsupport.api.isOlderVersion( '1.2.0.0', sp.clientVersion ) ){
				data.Url = document.location;
				data.userid = sp.userid;
				data.versionId = sp.clientVersion;
				data.dlsource = sp.dlsource;

				if( sp.CD_CTID != "" ) {
					data.CD_CTID = sp.CD_CTID;
				}
				spsupport.api.jsonpRequest( sp.sfDomain_ + "saveStatistics.action", data );
			} else  {
				spsupport.api.sendMessageToExtenstion("SuperFishSaveStatisticsMessage", data);
			}
		},

		isOlderVersion: function(bVer, compVer) {
			var res = 0;
			var bTokens = bVer.split(".");
			var compTokens = compVer.split(".");

			if (bTokens.length == 4 && compTokens.length == 4){
				var isEqual = 0;
				for (var z = 0; z <= 3 && !isEqual && !res ; z++){
					if (+(bTokens[z]) > +(compTokens[z])) {
						res = 1;
						isEqual = 1;
					} else if (+(bTokens[z]) < +(compTokens[z])) {
						isEqual = 1;
					}
				}
			}
			return res;
		},

		offerClick: function(url, slideUpValue)
		{
			spsupport.api.retargetingResetCounter(slideUpValue);

			similarproducts.checkoutObserver.registerOfferClick(
			{
				url: url,
				timestamp: new Date().getTime(),
				userId: similarproducts.b.userid,
                dlsource: similarproducts.b.dlsource
			});
		},

		retargetingResetCounter: function(slideUpValue)
		{
			if (slideUpValue !== 13 && slideUpValue !== 14)
			{
				similarproducts.util.sendRequest('{"cmd": 11}'); // Reset the retargeting inactive user counter
			}
		},

		leftPad: function( val, padString, length) {
			var str = val + "";
			while (str.length < length){
				str = padString + str;
			}
			return str;
		},

		getDateFormated: function(){
			var dt = new Date();
			return dt.getFullYear() + spsupport.api.leftPad( dt.getMonth() + 1,"0", 2 ) + spsupport.api.leftPad( dt.getDate(),"0", 2 ) + "";
		},
		dtBr: spsupport.dtBr,

		nofityStatisticsAction:function(action) {
			var sp = spsupport.p;
			if(sp.w3iAFS != ""){
				data.w3iAFS = sp.w3iAFS;
			}
			if(sp.CD_CTID != ""){
				data.CD_CTID = sp.CD_CTID;
			}

			spsupport.api.jsonpRequest( sp.sfDomain_ + "notifyStats.action", {
				"action": action,
				"userid": sp.userid,
				"versionId": sp.clientVersion,
				"dlsource": sp.dlsource,
				"browser": navigator.userAgent
			});
		},
		unloadEvent: function(){
		}
	};

	spsupport.domHelper =
	{
		addEListener : function(node, func, evt ){
			if( window.addEventListener ){
				node.addEventListener(evt,func,false);
			}else{
				node.attachEvent(evt,func,false);
			}
		}
	};

	spsupport.api.init();
})();
