   function getSelector(s)
   {
   		if (s.nodeType && s.nodeType == 1) {
			return s;
		} else if (typeof s == 'string') {
			return (document.getElementById(s) || document.querySelector(s));
		}
		return null;
   }
   function zy_anim_listen(s,c)
   {
   		var sel=getSelector(s);
		if(sel.animCB!=c)
		{
			if(sel.animCB)
			{
				sel.removeEventListener('webkitTransitionEnd', sel.animCB, true);
			}
			sel.animCB=c;
			if(c)
			{
				sel.addEventListener("webkitTransitionEnd", c, true);
			}	
		}   	
   }
   function zy_anim_push(s,a)
   {
   		var sel=getSelector(s);
		if(sel)
		{
			if(sel.className.indexOf(a)<0)
				sel.className+=" "+a;
		}
   }
   function zy_anim_pop(s,a)
   {
   		var sel=getSelector(s);
		if(sel)
		{
			if (a) 
				sel.className = sel.className.replace(a, "");
			else {
				var n = sel.className.lastIndexOf(" ");
				if(sel.className.substr(n).indexOf(" a-")>=0)
					sel.className=sel.className.substr(0,n);
			}
		}
   }