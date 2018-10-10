
function wproPlugin_templateFilter(){}
wproPlugin_templateFilter.prototype.init=function(EDITOR){
EDITOR.addHTMLFilter('source',wproPlugin_templateFilter_ocFilter);
EDITOR.addHTMLFilter('design',wproPlugin_templateFilter_tdFilter);
EDITOR.addHTMLFilter('rawSource',wproPlugin_templateFilter_tdFilter);
EDITOR.addHTMLFilter('preview',wproPlugin_templateFilter_tdFilter);
EDITOR.addHTMLFilter('source',wproPlugin_templateFilter_tsFilter);
}
function wproPlugin_templateFilter_tdFilter (editor, html) {
for (var x in editor._templateFilterTags) {
var regex1 = new RegExp(WPro.quoteMeta(x), 'gi');
html = html.replace(regex1, editor._templateFilterTags[x]);
}
return html
}
function wproPlugin_templateFilter_tsFilter (editor, html) {
for (var x in editor._templateFilterTags) {
var regex1 = new RegExp(WPro.quoteMeta(editor._templateFilterTags[x]), 'gi');
html = html.replace(regex1, x);
}
return html
}
function wproPlugin_templateFilter_ocFilter(editor,html) {
for (var i=0;i<editor._templateFilterOpen.length;i++) {
var open = editor._templateFilterOpen[i];
var close = editor._templateFilterClose[i];
var encodedOpen = escape(open);
var encodedClose = escape(close);
var regex1 = new RegExp(WPro.quoteMeta(open)+'([\\s\\S]*?)'+WPro.quoteMeta(close), 'gi');
var regex2 = new RegExp(WPro.quoteMeta(encodedOpen)+'([\\s\\S]*?)'+WPro.quoteMeta(encodedClose), 'gi');
html = html.replace(regex1, function(x){return wproPlugin_templateFilter_ocFilter2(unescape(x));});
html = html.replace(regex2, function(x){return wproPlugin_templateFilter_ocFilter2(unescape(x));});
}
return html;
};
function wproPlugin_templateFilter_ocFilter2(str) {
return str.replace('&nbsp;', String.fromCharCode(160)).replace(/\&\#[0-9]+\;/g, function (x) {
var r;
var n=x.replace(/[^0-9]/g, '');
if (r=String.fromCharCode(parseInt(n))) {
return r;
}else{
return x;
}
}).replace(/&amp;/g, '&');
}