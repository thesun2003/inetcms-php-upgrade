//var pasteFrame;
var pasteWindow;
var pasteDocument;
function initCodeCleanup () {
	
	if (action=='paste' && mode != 'upload' && mode != 'uploadFinished') {
		var frame
		if (WPro.isIE) {
			pasteWindow = window.frames['pasteFrame']
			pasteDocument = pasteWindow.document
		} else {
			pasteWindow = document.getElementById('pasteFrame').contentWindow
			pasteDocument = pasteWindow.document
		}
		pasteDocument.designMode = "on";
		pasteWindow.focus();
	}
	if (mode=='uploadFinished') {
		skip();	
	}
}
function skip () {
	var str = document.dialogForm.elements['html'].value;
		
	if (action=='paste') {
		dialog.editor.insertAtSelection(str);
	} else {
		dialog.editor.setValue(str);	
	}
	
	dialog.close();
	return false;
}
function selectAll() {
	var form = document.dialogForm;
	// get settings
	var inputs = form.getElementsByTagName('INPUT');
	var n = inputs.length;
	for (var i=0;i<n;i++) {
		inputs[i].checked=true;
	}	
}
function unselectAll() {
	var form = document.dialogForm;
	// get settings
	var inputs = form.getElementsByTagName('INPUT');
	var n = inputs.length;
	for (var i=0;i<n;i++) {
		inputs[i].checked=false;
	}
}
function _removePMargins(str) {
	var b = str.replace(/(<[^>]* style=")([^"]*)("[^>]*>)/gi, "$1");	
	var a = str.replace(/(<[^>]* style=")([^"]*)("[^>]*>)/gi, "$3");	
	var str=str.replace(/(<[^>]* style=")([^"]*)("[^>]*>)/gi, "$2");	
	
	// encode strings
	str = str.replace(/url\([\s\S]*?\)/gi, function(x){return '[WP'+escape(x)+'WP]';});
	str = str.replace(/"[\s\S]*?"/g, function(x){return '[WP'+escape(x)+'WP]';});
	str = str.replace(/'[\s\S]*?'/g, function(x){return '[WP'+escape(x)+'WP]';});
	
	var arr = {};
	var styles = str.match(/([A-Za-z\-]*:[^;]*)/gi);
	if (styles) {
		var n = styles.length;
		for (var i=0; i<n; i++) {
			s = styles[i].split(':');
			if (s[0] && s[1]) {
				if (/^\s*(mso-|margin|padding)/.test(s[0])) continue;
				arr[s[0]] = s[1];
			}
		}
		var str = '';
		for (var key in arr) {
			var val = arr[key];
			if (!val) continue;
			str += key + ':'+val+'; ';
		}
		if (/; $/.test(str)) {
			str = str.substring(0, str.length - 2);
		}
	}
	// unencode strings
	str = str.replace(/\[WP[\s\S]*?WP\]/g, function(x){return unescape(x).replace(/\[WP/g, '').replace(/WP\]/g, '');});
	
	return b+str+a;
}
function cleanCode(win) {
	//var str = '';
	var form = document.dialogForm;
	// get settings
	var inputs = form.getElementsByTagName('INPUT');
	var n = inputs.length;
	for (var i=0;i<n;i++) {
		if (inputs[i].getAttribute('type')=='checkbox') {
			//alert('var '+inputs[i].id+' = '+(inputs[i].checked?'true':'false'));
			eval ('var '+inputs[i].id+' = '+(inputs[i].checked?'true':'false'));	
		}
	}
	//combineFont
	//removeAttributelessFont
	
	//combineSpan
	//removeAttributelessSpan	
	if (combineFont || removeAttributelessFont && !removeFont) {
		var fonts = win.document.getElementsByTagName('FONT');
		var n = fonts.length;
		var s = 0;
		for (var i=0;i<n;i++) {
			// remove fonts within a font that have duplicate attributes
			if (combineFont) {
				var f = fonts[i];
				var cn = f.childNodes;
				var node = f;
				var k = cn.length
				for (var j=0;j<k;j++) {
					if (cn[j].tagName) {
						if (cn[j].tagName == 'FONT') {
							if (fonts[i].getAttribute('face') == cn[j].getAttribute('face')) {
								cn[j].removeAttribute('face');	
							}
							if (fonts[i].getAttribute('size') == cn[j].getAttribute('size')) {
								cn[j].removeAttribute('size');	
							}
							if (fonts[i].getAttribute('color') == cn[j].getAttribute('color')) {
								cn[j].removeAttribute('color');	
							}
							if (fonts[i].className == cn[j].className) {
								cn[j].className = '';	
							}
							if (fonts[i].style.cssText == cn[j].style.cssText) {
								cn[j].style.cssText = '';	
							}
						}
					}
				}
				
			}	
			// remove fonts with no attributes
			if (removeAttributelessFont) {
				if (i>=0) {
					if (fonts[i]) {
						if (!fonts[i].className && !fonts[i].style.cssText && !fonts[i].getAttribute('face') && !fonts[i].getAttribute('size') && !fonts[i].getAttribute('color')) {
							var f = fonts[i];
							var cn = f.childNodes;
							var node = f;
							var k = cn.length
							for (var j=0;j<k;j++) {
								f.parentNode.insertBefore(cn[j].cloneNode(true), f);
							}
							f.parentNode.removeChild(f);
							i--
							n = fonts.length;
						}
					}
				}
			}
			// combine directly nested fonts
			if (combineFont) {
				if (i>=0) {
					if (fonts[i]) {
						// check for a matching only child
						var cn = fonts[i].childNodes;
						if (cn.length == 1) {
							if (fonts[i].firstChild.tagName) {
								if (fonts[i].firstChild.tagName == 'FONT') {
									var fc = fonts[i].firstChild
									WPro.addAttributes(fonts[i], fc.attributes, fc)
									var cn = fc.childNodes;
									for (var m=0; m<cn.length; m++) {
										fonts[i].appendChild(cn[m].cloneNode(true));
									}
									fonts[i].removeChild(fc);
									i--
									n = fonts.length;
								}
							}
						}
					}	
				}
			}
		}
	}
	
	if (combineSpan || removeAttributelessSpan && !removeSpan) {
		var spans = win.document.getElementsByTagName('SPAN');
		var n = spans.length;
		var s = 0;
		for (var i=0;i<n;i++) {
			if (combineSpan) {
				var f = spans[i];
				var cn = f.childNodes;
				var node = f;
				var k = cn.length
				for (var j=0;j<k;j++) {
					if (cn[j].tagName) {
						if (cn[j].tagName == 'SPAN') {
							if (spans[i].className == cn[j].className) {
								cn[j].className = '';	
							}
							if (spans[i].style.cssText == cn[j].style.cssText) {
								cn[j].style.cssText = '';	
							}
						}
					}
				}
				
			}
			if (removeAttributelessSpan) {
				if (i>=0) {
					if (spans[i]) {
						if (!spans[i].className && !spans[i].style.cssText) {
							var f = spans[i];
							var cn = f.childNodes;
							var node = f;
							var k = cn.length
							for (var j=0;j<k;j++) {
								f.parentNode.insertBefore(cn[j].cloneNode(true), f);
							}
							f.parentNode.removeChild(f);
							i--
							n = spans.length;
						} 
					}
				}
			}
			if (combineSpan) {
				if (i>=0) {
					if (spans[i]) {
						// check for a matching only child
						var cn = spans[i].childNodes;
						if (cn.length == 1) {
							if (spans[i].firstChild.tagName) {
								if (spans[i].firstChild.tagName == 'SPAN') {
									var fc = spans[i].firstChild
									WPro.addAttributes(spans[i], fc.attributes, fc)
									var cn = fc.childNodes;
									for (var m=0; m<cn.length; m++) {
										spans[i].appendChild(cn[m].cloneNode(true));
									}
									spans[i].removeChild(fc);
									i--
									n = spans.length;
								}
							}
						}
					}
				}
			}
		}
	}
	
	var str = win.document.body.innerHTML;
	
	str = WPro.escapeServerTags(str);
	
	if (proprietary) {
		while (str.match(/(<[^>]+) _[a-z:\-_]=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi)) {
			str=str.replace(/(<[^>]+) _[a-z:\-_]=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi, '$1');
		}
	}
	
	if (quotes) {
		
		var t = {}
		t[130] = 8218;
		t[131] = 402;
		t[132] = 8222;
		t[133] = 8230;
		t[134] = 8224;
		t[135] = 8225;
		t[136] = 710;
		t[137] = 8240;
		t[138] = 352;
		t[139] = 8249;
		t[140] = 338;
		t[145] = 8216;
		t[146] = 8217;
		t[147] = 8220;
		t[148] = 8221;
		t[149] = 8226;
		t[150] = 8211;
		t[151] = 8212;
		t[152] = 732;
		t[153] = 8482;
		t[154] = 353;
		t[155] = 8250;
		t[156] = 339;
		t[159] = 376;
		
		var arr = [];
		var n = str.length;
		for (var j=0; j<n; j++) {
			var charCode = str.charCodeAt(j);
			if (t[charCode]) {
				arr.push(charCode);
			}
		}
		var n = arr.length;
		for (var j=0; j<n; j++) {
			str = str.replace(String.fromCharCode(arr[j]), (t[arr[j]]?"&#"+t[arr[j]]+";":"&#"+arr[j]+";"))
		}
		
		
	}
	
	
	if(convertP) {
		str=str.replace(/(<p(| [^>]*)>([\s\S]*?)<\/p>)/gi, '<div$1>$2</div>');
	}
	if(convertDiv) {
		str=str.replace(/(<div(| [^>]*)>([\s\S]*?)<\/div>)/gi, '<p$1>$2</p>');
	}
	if (removeStyles) {
		str=str.replace(/(<[^>]+) style=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi, '$1');
	}
	if (removeClasses) {
		str=str.replace(/(<[^>]+) class=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi, '$1');
	}
	if (removeFont) {
		//while (str.match(/<font[^>]*>([\s\S]*?)<\/font>/gi)) {
			//str = str.replace(/<font[^>]*>([\s\S]*?)<\/font>/gi, '$1');
			str = str.replace(/<font(| [^>]*)>/gi, '');
			str = str.replace(/<\/font>/gi, '');
		//}
	}
	if (removeSpan) {
		//while (str.match(/<span[^>]*>([\s\S]*?)<\/span>/gi)) {
			str = str.replace(/<span(| [^>]*)>/gi, '');
			str = str.replace(/<\/span>/gi, '');
		//}
	}
	
	if (removeXML) {
		str = str.replace(/<\?xml(|:[^>]*| [^>]*)>/gi, '');
		while (str.match(/<[^>]+ [a-z]+:[a-z]+=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi)) {
			str=str.replace(/(<[^>]+) [a-z]+:[a-z]+=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi, '$1')
		}
		//str = str.replace(/<[a-z]+:[a-z]+[^>]*>([\s\S]*?)<\/[a-z]*:[a-z]*[^>]*>/gi, '$1');
		str = str.replace(/<[a-z]+:[a-z]+[^>]*>/gi, '');
		str = str.replace(/<\/[a-z]+:[a-z]+[^>]*>/gi, '');
	}
	
	if (removeConditional) {
		while (str.match(/<![\-]*\[if [^\]]*\][\-]*>([\s\S]*?)<![\-]*\[endif\][\-]*>/gi)) {
			str = str.replace(/<![\-]*\[if [^\]]*\][\-]*>([\s\S]*?)<![\-]*\[endif\][\-]*>/gi, '');
		}
	}
	if (removeComments) {
		str = str.replace(/<!--([\s\S]*?)-->/gi, '');
	}
	if (removeDel) {
		while (str.match(/<del(| [^>]*)>([\s\S]*?)<\/del>/gi)) {
			str = str.replace(/<del[^>]*>([\s\S]*?)<\/del>/gi, '');
		}
	}
	if (removeIns) {
		while (str.match(/<ins(| [^>]*)>([\s\S]*?)<\/ins>/gi)) {
			str = str.replace(/<ins(| [^>]*)>([\s\S]*?)<\/ins>/gi, '$2');
		}
	}
	if (removeLang) {
		str = str.replace(/(<[a-z]+[^>]*) lang=("[^>"]*"|'[^>']*'|[a-z0-9\-_][^> ]*)/gi, '$1');
	}
	// string manipulations
	if (removeScripts) {
		str = str.replace(/<script(| [^>]*)>([\s\S]*?)<\/script>/gi, '');
		// attributes
		while (str.match(/<[^>]+ on[a-zA-Z]+=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi)) {
			str=str.replace(/(<[^>]+) on[a-zA-Z]+=("[^"]*"|'[^']*'|[a-z0-9\-_][^> ]*)/gi, '$1')
		}

	}
	if (removeObjects) {
		str = str.replace(/<object(| [^>]*)>([\s\S]*?)<\/object>/gi, '');
		str = str.replace(/<embed(| [^>]*)>([\s\S]*?)<\/embed>/gi, '');
		str = str.replace(/<applet(| [^>]*)>([\s\S]*?)<\/applet>/gi, '');	
	}
	if (removeImages) {
		str = str.replace(/<img(| [^>]*)>/gi, '');
	}
	if (removeLinks) {
		str = str.replace(/<a [^>]*href=[^>]*>([\s\S]*?)<\/a>/gi, '$1')
	}
	
	if(removeEmptyP) {	
		// remove empty paragraph tags
		str=str.replace(/(<p(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/p>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<p [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h1(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h1>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h1 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h2(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h2>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h2 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h3(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h3>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h3 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h4(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h4>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h4 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h5(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h5>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h5 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h6(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h6>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h6 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
		
		// remove empty paragraph tags
		str=str.replace(/(<h7(| [^>]*)>(|<(strong|b|em|i)[^>]*>)( |&nbsp;|)(|<\/(strong|b|em|i)>)<\/h7>)/gi, '');
		// remove margins from paragraph tags
		str=str.replace(/<h7 [^>]*style="[^"]*"[^>]*>/gi, _removePMargins);
	}
	if(removeEmptyContainers) {
		// empty paragraphs
		str=str.replace(/(<p(| [^>]*)><\/p>)/gi, '');
		str=str.replace(/(<h[0-9](| [^>]*)><\/h[0-9]>)/gi, '');
		str=str.replace(/(<div(| [^>]*)><\/div>)/gi, '');
		//str=str.replace(/(<span(| [^>]*)><\/span>)/gi, '');
		//str=str.replace(/(<font(| [^>]*)><\/font>)/gi, '');
		//str=str.replace(/(<strong(| [^>]*)><\/strong>)/gi, '');
		//str=str.replace(/(<em(| [^>]*)><\/em>)/gi, '');
		//str=str.replace(/(<i(| [^>]*)><\/i>)/gi, '');
		//str=str.replace(/(<u(| [^>]*)><\/u>)/gi, '');
	}
	// tables should not be within paragraphs
	str = str.replace(/<p(| [^>]*)>\s*<table/gi, '<table');
	str = str.replace(/<\/table>\s*<\/p>/gi, '</table>');
	
	if (dialog.isIE) {
		str = str.replace(/<b(| [^>]*)>/gi, '<strong$1>');
		str = str.replace(/<\/b>/gi, '</strong>');
		str = str.replace(/<i(| [^>]*)>/gi, '<em$1>');
		str = str.replace(/<\/i>/gi, '</em>');
	} else {
		str = str.replace(/<strong(| [^>]*)>/gi, '<b$1>');
		str = str.replace(/<\/strong>/gi, '</b>');
		str = str.replace(/<em(| [^>]*)>/gi, '<i$1>');
		str = str.replace(/<\/em>/gi, '</i>');
	}
	
	str = WPro.unescapeServerTags(str);
	
	return str;
}

function containsComputerLinks(str) {
	if (str.match(/<[a-z]+[^>]*[a-z]+=("|'|)file:\/\//gi)) {
		return true;
	} else {
		return false;	
	}
}

function formAction () {
	var win
	var form = document.dialogForm;
	if (mode!= 'upload' && mode != 'uploadFinished') {
		
		dialog.showLoadMessage();
		setTimeout("formAction2();", 100);
		return false;
		
	} else {
		var str = form.elements['html'].value;
		var n = files.length;
		for (var i=0; i<n; i++) {
			var v = form.elements['files_'+i].value.replace(/ /g, '%20');
			str = eval('str.replace(/(<[a-z]+[^>]*[a-z]+=)("|\'|)file:\\/+'+WPro.quoteMeta(files[i])+'("|\'| |>)/gi, "$1$2'+v+'$3");');
		}
		
		if (action=='paste') {
			dialog.editor.insertAtSelection(str);
		} else {
			dialog.editor.setValue(str);	
		}
		
		dialog.close();
		return true;
	}
}
function formAction2 () {
	var form = document.dialogForm;
	if (action=='paste') {
		win = pasteWindow;
	} else {
		win = dialog.editor.editWindow;	
	}
	var str = cleanCode(win);
	if (containsComputerLinks(str)) {
		dialog.focus();
		form.method='post';
		form.elements['html'].value = str;
		form.submit();
		return true;
	} else {
	
		if (action=='paste') {
			dialog.editor.insertAtSelection(str);
		} else {
			dialog.editor.setValue(str);	
		}
		
		dialog.close();
		return false;
	}
}