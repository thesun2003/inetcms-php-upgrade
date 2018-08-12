function getDOMObjectPosition(obj) {
  // get absolute coordinates for dom element
  var info = {
    left: 0, 
    top: 0, 
    width: obj.width ? obj.width : obj.offsetWidth, 
    height: obj.height ? obj.height : obj.offsetHeight
  };

  while (obj) {
    info.left += obj.offsetLeft;
    info.top += obj.offsetTop;
    obj = obj.offsetParent;
  }
  return info;
}

function wopen(url,name,w,h,r,s,st) {
   var w=window.open(url,name,"width="+w+",height="+h+",resizable="+r+",toolbar=0,location=0,status="+st+",menubar=0,directories=0,scrollbars="+s);
}

function ondel(url) {
 if (confirm('Вы точно хотите удалить?')) location.href=url;
}

function openerHref(url) {
    window.opener.location = url;
    window.close();
}

function heightEdit100pers() {
  var iframe = document.getElementById('content___Frame');
  iframe.height = (document.body.clientHeight - 60) + "px";
}

function heightEditNew100pers() {
  var editor_area = document.getElementById('editor_area');
  editor_area.style.height = (document.body.clientHeight - 60) + "px";
}

function reload(path) {
  document.location.href = path;
}

function hideNotification(id) {
  if (typeof id == "undefined") {
    id = 'notification_item';
  }
  document.getElementById(id).style.display = "none";
}

function pushNotification(num, id) {
  if (typeof id == "undefined") {
    id = 'notification_item';
  }
  if (num < 5) {
    num++;
    document.getElementById(id).style.top = "-" + (num * 10) + "px";
    setTimeout('pushNotification(' + num + ', \'' + id + '\')', 200);
  } else {
    setTimeout('hideNotification(\'' + id + '\')', 200);
  }
}

function showNotification(id) {
  if (typeof id == "undefined") {
    id = 'notification_item';
  }
  document.getElementById(id).style.display = "block";
  setTimeout('pushNotification(0, \''+id+'\')', 5000);
}

function showMessage(message, type) {
  if (!type) {
    type = 'ok';
  }
  document.getElementById('js_notification_item_text').setAttribute("class", "notification_pattern notify_"+type);
  document.getElementById('js_notification_item_text').innerHTML = message;
  document.getElementById('js_notification_item').style.top = '0px';
  showNotification('js_notification_item');
}

function showDescrForm(image_id) {
  document.getElementById('descr_' + image_id).style.display = 'none';
  document.getElementById('change_descr_' + image_id).style.display = 'block';
}

function updateDescr(image_id) {
  var resultDIV = $("descr_" + image_id);
  var descr = $('change_descr_text_' + image_id).get('value');
  new Request.JSON({
    url: js_config['image_upload_path'] + 'updatedescr.php',
    method: 'get',
    data: 'id='+image_id+'&descr='+descr,
    onSuccess: function(response) {
      resultDIV.set('html', descr);
      $('change_descr_' + image_id).setStyle('display', 'none');
      $('descr_' + image_id).setStyle('display', 'block');
    }
  }).send();
}

function checkUploadForm(gallery_id, is_limited) {
  if (is_limited) {
    parent.document.getElementById('upload_form_' + gallery_id).style.display = 'none';
  } else {
    parent.document.getElementById('upload_form_' + gallery_id).style.display = 'block';
  }
}

function reloadGallery(gallery_id) {
  var resultDIV = parent.$('gallery_' + gallery_id);
  new Request.JSON({
    url: js_config['image_upload_path'] + 'get.php',
    method: 'get',
    data: 'id='+gallery_id,
    onSuccess: function(response) {
      resultDIV.set('html', response.result);
      checkUploadForm(gallery_id, response.is_limited);
    }
  }).send();
}

function dropImage(image_id, gallery_id) {
  new Request.JSON({
    url: js_config['image_upload_path'] + 'dropimage.php',
    method: 'get',
    data: 'id='+image_id,
    onSuccess: function(response) {
      reloadGallery(gallery_id);
    }
  }).send();
}


function showUpload(gallery_id) {
  parent.document.getElementById('gallery_upload_' + gallery_id).style.display='block';
}

function openSubmenu(id) {
  document.getElementById('submenu_' + id).style.display = 'block';
}

function closeSubmenu(id) {
  document.getElementById('submenu_' + id).style.display = 'none';  
}

function openMenuActions(id) {
  var obj = $('values_editmenu_' + id + '_name');
  if(obj) {
    var link_obj = getDOMObjectPosition(obj);
    $('div_menu_actions_' + id).style.top = (link_obj.top-3) + 'px';
    $('div_menu_actions_' + id).style.marginLeft = (link_obj.width-1) + 'px';
    $('div_menu_actions_' + id).style.display = 'block';
  }
}

function closeMenuActions(id) {
  document.getElementById('div_menu_actions_' + id).style.display = 'none';
}

function openActionActions(menu_id, id) {
  link_obj = getDOMObjectPosition(document.getElementById('action_item_' + menu_id + '_' + id));
  document.getElementById('div_item_actions_' + menu_id + '_' + id).style.top = (link_obj.top-5) + 'px';
  document.getElementById('div_item_actions_' + menu_id + '_' + id).style.marginLeft = (link_obj.width-1) + 'px';
  document.getElementById('div_item_actions_' + menu_id + '_' + id).style.display = 'block';
}

function closeActionActions(menu_id, id) {
  document.getElementById('div_item_actions_' + menu_id + '_' + id).style.display = 'none';
}


function openActions(field, id) {
  var field_name_id = field + '_' + id + '_name';
  var field_actions_id = field + '_' + id + '_actions';
  link_obj = getDOMObjectPosition($(field_name_id));
  $(field_actions_id).setStyle('top', (link_obj.top-3) + 'px');
  $(field_actions_id).setStyle('marginLeft', (link_obj.width-1) + 'px');
  $(field_actions_id).setStyle('display', 'block');
}

function closeActions(field, id) {
  var field_actions_id = field + '_' + id + '_actions';
  $(field_actions_id).setStyle('display' , 'none');
}

function updateSession(field, value, action) {
  new Request.JSON({
    url: main_url + admin_inc + 'updatemenu.php',
    method: 'get',
    data: 'field=' + field + '&value=' + value + '&action=' + action
  }).send();
}

function get_path(suffix) {
  var core_array = ['menu', 'page', 'admins', 'search'];
  if(core_array.contains(suffix)) {
    return js_config['core_path'];
  } else {
    return js_config[suffix + '_path'];
  }
}

function pleaseWait(id) {
  $(id).set('html', '<img src="'+core_path+'images/ajax-loader.gif" title="Загружается..." alt="Загружается..." />')
}

function pleaseWaitImage(id) {
  $(id).set('src', core_path+'images/circle-loader.gif');
  $(id).set('height', '16');
}

function toggleMenu(id, elem_prefix) {
  if(!elem_prefix) {
    elem_prefix = 'menu';
  }
  var elem_id = elem_prefix + '_' + id;
  var elem_image_id = elem_prefix + '_item_image_' + id;
  //console.log(elem_id);
  var elem = $(elem_id);

  if (elem.style.display == 'block') {
    elem.style.display = 'none';
    $(elem_image_id).set('src' , 'img/closednode.gif');
    updateSession('div_' + elem_prefix, id, 0);
  } else {
    if($(elem_id).get('html')) {
      updateSession('div_' + elem_prefix, id, 1);
      $(elem_id).setStyle('display' , 'block');
      $(elem_image_id).set('src', 'img/openednode.gif');
      $(elem_image_id).set('height', '22');
    } else {
      openChildrenBlock(id, elem_prefix, elem_id, elem_image_id);
    }
  }
  return false;
}

function openChildrenBlock(id, elem_prefix, elem_id, elem_image_id) {
  pleaseWaitImage(elem_image_id);  
  new Request.JSON({
    url: get_path(elem_prefix),
    method: 'get',
    data: 'mode=JSON&context='+ elem_prefix +'&id=' + id,
    onSuccess: function(response) {
      //console.log(typeof response);
      $(elem_id).set('html', response.content);
      $(elem_id).setStyle('display' , 'block');
      $(elem_image_id).set('src', 'img/openednode.gif');
      $(elem_image_id).set('height', '22');
      update_grag_and_drop2();
    }
  }).send();
}

function ajax_form_submit(form_id, message) {
  if (typeof message == "undefined") {
    message = 'Данные успешно изменены';
  }
  var form = $(form_id);
  if(form) {
    form.set('send',
      {
        onSuccess: function(response) {
          showMessage(message, 'ok');
        }}
      );
    form.send();
  }
}

function ajax_catalog_item_submit(form_id, message) {
  if (typeof message == "undefined") {
    message = 'Данные успешно изменены';
  }
  var form = $(form_id);
  if(form) {
    form.set('send',
      {
        onSuccess: function(html_response) {
          var response = JSON.decode(html_response);
          if(response.status == 'ok') {
            showMessage(message, 'ok');
          } else {
            showMessage(response.error, 'error');
          }
        }}
      );
    form.send();
  }
}

function catalog_items_move(item_id, catalog_id) {
  var elem_prefix = 'catalog';
  new Request.JSON({
    url: get_path(elem_prefix),
    method: 'get',
    data: 'mode=JSON&context=catalog_item&type=move&catalog_id='+catalog_id+'&id=' + item_id,
    onSuccess: function(response) {
      catalog_reload(catalog_id);
    }
  }).send();
}

function catalog_reload(id) {
  var elem_prefix = 'catalog';
  var elem_id = elem_prefix + '_' + id;
  var elem_image_id = elem_prefix + '_item_image_' + id;
  openChildrenBlock(id, elem_prefix, elem_id, elem_image_id);
}

function update_grag_and_drop2() {
$$('.catalog_items_draggables').addEvent('mousedown', function(event){
    event.stop();

    // `this` refers to the element with the .item class
    var shirt = this.getParent('table');

    var clone = shirt.clone().setStyles(shirt.getCoordinates()).setStyles({
      opacity: 0.7,
      position: 'absolute'
    }).inject(document.body);

    var styles = shirt.getStyles('display');
    shirt.setStyles({display: 'none'});

    var drag = new Drag.Move(clone, {

      droppables: $$('.catalog_items_droppables'),

      onDrop: function(dragging, cart){
        dragging.destroy();

        if (cart != null) {
          var item_id = shirt.get('item_id');
          if(confirm('Вы точно хотите переместить товар?'+ $('catalog_item_'+item_id+'_name').get('text'))) {
            var catalog_id = cart.get('catalog_id');
            catalog_items_move(item_id, catalog_id);
            shirt.destroy();
          } else {
            shirt.setStyles(styles);
          }
          cart.highlight('#7389AE', '#FFF');
        } else {
          shirt.setStyles(styles);
        }
      },
      onEnter: function(dragging, cart){
        cart.tween('background-color', '#98B5C1');
      },
      onLeave: function(dragging, cart){
        cart.tween('background-color', '#FFF');
      },
      onCancel: function(dragging){
        dragging.destroy();
        shirt.setStyles(styles);
      }
    });
    drag.start(event);
  }).removeClass('catalog_items_draggables');
};

function send_message_form_submit(form_id) {
  var form = $(form_id);
  if(form) {
    form.set('send',
      {
        onSuccess: function(response) {
          var success_text = new Element('div', {
            'html': '<br />Спасибо за ваше сообщение!'
          });
          success_text.inject($(form_id));
        }}
      );
    form.send();
  }
}