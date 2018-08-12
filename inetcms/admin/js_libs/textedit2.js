window.addEvent('domready', function() {
  tinyMCE.init({
    // General options
    mode : "textareas",
    theme : "advanced",
    language : "ru",
    plugins : "pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
    file_browser_callback : "tinyBrowser",
  
    // Theme options
    theme_advanced_buttons1 : "code,save,newdocument,preview,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,search,replace,|,cite,del,ins,|,nonbreaking,restoredraft,|,removeformat,cleanup",
    theme_advanced_buttons2 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,media,table",
    theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,|,fullscreen,visualaid,help",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    theme_advanced_buttons2_add : 'filemanager',
  
    // Example content CSS (should be your site CSS)
    // content_css : js_config['css_path'] + "/main.css",
  
    relative_urls : false,
    remove_script_host : true,
  
    // Style formats
    style_formats : [
      {title : 'Bold text', inline : 'b'},
      {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
      {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
      {title : 'Example 1', inline : 'span', classes : 'example1'},
      {title : 'Example 2', inline : 'span', classes : 'example2'},
      {title : 'Table styles'},
      {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
    ]
  });
});

function textedit2_ajax_save(field_id) {
  ed = tinyMCE.get(field_id + '_id');
  $(field_id + '_id').set('value', ed.getContent());
  ed.setProgressState(1);
}

function textedit2_ajax_after_save(field_id, clear) {
  ed = tinyMCE.get(field_id + '_id');
  ed.setProgressState(0);
  ed.undoManager.clear();
  ed.nodeChanged();
  ed.isNotDirty = true;
  if (typeof clear != "undefined" && clear) {
    ed.setContent('');
  }
}