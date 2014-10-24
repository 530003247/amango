/**
 * 
 */ 
var zy_dl_opid=1000;
var zy_dl_session=new Object;
var zy_dl_taskcount=0;
var zy_muticount=3;
var lstor=(window.localStorage?window.localStorage:(new Object));

function zy_initcache(cb)
{
	uexFileMgr.cbGetFileRealPath = function(opCode, dataType, path) {
		zy_dl_session.rp = path;
		if (cb)
			cb();
	}
	uexFileMgr.getFileRealPath("wgt://");
}

function zy_imgcache(sel,key,url,cb,err,dest,ext)
{
      uexDownloaderMgr.onStatus = function(opCode, fileSize, percent, status) {
	  	switch (status) {
			case 0 :
				break;
			case 1 :
				var s = zy_dl_session[opCode];
				uexDownloaderMgr.closeDownloader(opCode);
				uexDownloaderMgr.clearTask(s.rul);
				lstor[s.key] = s.dest.replace("wgt://", zy_dl_session.rp);
				setTimeout(function(){
					if (s.cb)
						s.cb(s.sel, lstor[s.key]);
					else
						$$(s.sel).style.backgroundImage = "url(" + lstor[s.key] + ")";
					zy_cleartask(opCode);
				},100);
				break;
			case 2 :
				uexDownloaderMgr.closeDownloader(opCode);
				if (zy_dl_session[opCode].err)
					zy_dl_session[opCode].err();
				zy_cleartask(opCode);
				return;
		}
	};
	uexDownloaderMgr.cbCreateDownloader = function(opCode, dataType, data) {
		if (data == 0) {
			var s = zy_dl_session[opCode];
			if (!s.dest) {
				var d = new Date();
				s.dest = "wgt://data/icache/" + d.valueOf() + opCode + "."
						+ (s.ext ? s.ext : "jpg");
			}
			else
				s.dest = "wgt://data/icache/"+s.dest;
			uexDownloaderMgr.download(opCode, s.url, s.dest, '0');
		} else {
			uexDownloaderMgr.closeDownloader(opCode);
			if (zy_dl_session[opCode].err)
				zy_dl_session[opCode].err();
			zy_cleartask(opId);
		}
	};
	
	uexFileMgr.cbIsFileExistByPath=function(opId,dataType,data)
			{
				if(int(data))
				{
					var s = zy_dl_session[opId];
					setTimeout(function(){
						if (s.cb)
							s.cb(s.sel,s.rp);
						else
							$$(s.sel).style.backgroundImage = "url(" + s.rp + ")";
						zy_cleartask(opId);
						},100);
				}
				else{
					uexDownloaderMgr.createDownloader(opId);
				}
			}
	{
		zy_dl_opid++;
		zy_dl_session[zy_dl_opid] = new Object;
		zy_dl_session[zy_dl_opid].sel = sel;
		zy_dl_session[zy_dl_opid].key = key;
		zy_dl_session[zy_dl_opid].cb = cb;
		zy_dl_session[zy_dl_opid].err = err;
		zy_dl_session[zy_dl_opid].url = url;
		zy_dl_session[zy_dl_opid].dest = dest;
		zy_dl_session[zy_dl_opid].ext = ext;
		zy_dl_session[zy_dl_opid].state=0;
		zy_runcache();
	}
	if(lstor[key])
		return lstor[key];
	else
		return "";
}
function zy_cleartask(id)
{
	delete zy_dl_session[id];
	zy_dl_taskcount--;
	zy_runcache();
}
function zy_runcache(){
	if (zy_dl_taskcount < zy_muticount) {
		for (var i in zy_dl_session) {
			var s=zy_dl_session[i];
			if (s.state == 0) {
				s.state = 1;
				zy_dl_taskcount++;
				s.rp = lstor[s.key];
				if (s.rp) 
					uexFileMgr.isFileExistByPath("" + i, s.rp);
				else 
					uexDownloaderMgr.createDownloader(i);
				return;
			}
		}
	}
}
function zy_clearcache()
{
	lstor.clear();
	uexFileMgr.deleteFileByPath("wgt://data/icache");
}