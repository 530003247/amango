function zy_rc4_init(key){
	var s = [], j = 0, x, res = '';
	for (var i = 0; i < 256; i++) {
		s[i] = i;
	}
	for (i = 0; i < 256; i++) {
		j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
		x = s[i];
		s[i] = s[j];
		s[j] = x;
	}
	return s;
}
function zy_rc4(str,s) {
	var i = 0;
	var j = 0;
	var res="";
	var k=[];
 	k=k.concat(s);
	for (var y = 0; y < str.length; y++) {
		i = (i + 1) % 256;
		j = (j + k[i]) % 256;
		x = k[i];
		k[i] = k[j];
		k[j] = x; 
		res += String.fromCharCode(str.charCodeAt(y) ^ k[(k[i] + k[j]) % 256]);
	}
	return res;
}
function zy_rc4ex(str,key)
{
	var s=zy_rc4_init(key);
	return zy_rc4(str,s);
}
