function flashVersion() { 
      // Отдельно определяем Internet Explorer 
      var ua = navigator.userAgent.toLowerCase(); 
      var isIE = (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1 && ua.indexOf("webtv") == -1); 
      // Стартовые переменные 
      var version = 0; 
      var lastVersion = 10; // c запасом 
      var i; 
      if (isIE) { // browser == IE 
            try { 
                  for (i = 3; i <= lastVersion; i++) { 
                        if (eval('new ActiveXObject("ShockwaveFlash.ShockwaveFlash.'+i+'")')) { 
                              version = i; 
                        } 
                  } 
            } catch(e) {} 
      } else { // browser != IE 
            for (i = 0; i < navigator.plugins.length; i++) { 
                  if (navigator.plugins[i].name.indexOf('Flash') > -1) { 
                        version = (parseInt(navigator.plugins[i].description.charAt(16)) > version) ? parseInt(navigator.plugins[i].description.charAt(16)) : version;
                  } 
            } 
      } 
      return version; 
}

function showflashWH(flashName, W, H) {
    var bnFlash = flashName;
    var bnW = W;
    var bnH = H;
    var flashV = "6";
    document.getElementById(flashName).innerHTML = showflashJS(bnFlash, bnW, bnH, flashV);
}

function showflash(flashName) {
    var bnFlash = flashName;
    var bnW = 200;
    var bnH = 200;
    var flashV = "6";
    document.getElementById(flashName).innerHTML = showflashJS(bnFlash, bnW, bnH, flashV);
}

function showPleaseWait(type) {
    if (type == 'show') {
        document.getElementById("please_wait").innerHTML = "Пожалуйста подождите<br>загрузка может занять<br>несколько секунд";    
    } else {
        document.getElementById("please_wait").innerHTML = "";
        return false;
    }
    setTimeout('showPleaseWait("hide")', 15000);
}

function showflashJS(bnFlash, bnW, bnH, flashV) {
    var ie = (navigator.userAgent && (navigator.userAgent.indexOf("MSIE") >= 0) && (navigator.appVersion.indexOf("Win") != -1)) ? 1 : 0;
    var plugin = (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) ? navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin : 0;
    var ret = "";

    if(plugin){
        var version = flashVersion();
        var pluginVersion = parseInt(plugin.description.substring(plugin.description.indexOf(".") - 2));
        version = pluginVersion > version ? pluginVersion : version;
        var isPlay = (version >= flashV);
    }
    else if(ie){
        isPlay = true;
        ret += '<sc'+'ript type="text/vbscript"> \n';
        ret += 'on error resume next \n';
        ret += 'isPlay = IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.'+flashV+'")) \n';
        ret += '</sc'+'ript> \n';
    }

    if(isPlay){
        ret += '<object type="application/x-shockwave-flash" data="'+bnFlash;
        ret +='" width="'+bnW+'" height="'+bnH+'">';
        ret +='<param name="movie" value="'+bnFlash;
        ret +='" />';
        ret +='<param name="quality" value="high" />';
        ret +='<param name="wmode" value="transparent" />';
        ret +='</object>';
    }
    return ret;
}