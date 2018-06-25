var modal, modal_form, modal_content, modal_submit;

function initModalForm(name) {
  modal = document.getElementById(name);
  modal_form = document.getElementById(name + '_form');
  modal_submit = document.getElementById(name + '_submit');
  modal_content = document.getElementById(name + '_content');
  modal.style.top  = ((document.body.clientHeight - 150) / 2) + 'px';
  modal.style.left = ((document.body.clientWidth  - 350)  / 2) + 'px';
  modal.style.position = 'fixed';
}

function showModalForm(form_id) {
  var form_content = document.getElementById(form_id);
  if (document.getElementById(form_id + '_action') != null) {
    modal_form.action = document.getElementById(form_id + '_action').value;
  }
  if (document.getElementById(form_id + '_submit') != null) {
    modal_submit.value = document.getElementById(form_id + '_submit').value;
  }
  modal_content.innerHTML = form_content.innerHTML;
  modal.style.display = 'block';
}

function hideModalForm() {
  modal_content.innerHTML = '';
  modal.style.display = 'none';
}

function showModalFormX(action, submit_value) {
  if (action != null) {
    modal_form.action = action;
  }
  if (submit_value != null) {
    modal_submit.value = submit_value;
  }
  modal.style.display = 'block';
}

function showEditModalForm(form_id, prefix, ids) {
  showModalForm(form_id);
  getValuesModalForm(form_id, prefix, ids);
}

function updateModalFormX(result) {
  if (result != null) {
    modal_content.innerHTML = result.content;
    showModalFormX(result.action, result.submit_value);
  }
}

function showEditModalFormX(form_id, prefix, id) {
  eval(form_id + "_getForm('" + prefix + "', " + id + ");");
}

function getValuesModalForm(form_id, prefix, ids) {
  var form_field_name = form_id + '_values_';
  var edit_field_name = prefix + '_';
  for (i in ids) {
    if (document.getElementById(form_field_name + ids[i]) != null) {
      document.getElementById(form_field_name + ids[i]).value = document.getElementById(edit_field_name + ids[i]).innerHTML;
    }
  }  
}

// --------------------------------------------------------------------------------
// new AJAX methods
// --------------------------------------------------------------------------------
var modal_x = null;

function modalFormX_init(name) {
  if(name && modal_x == null) {
    modal_x = $(name);
  }
  var form_size = modal_x.getSize();
  modal_x.setStyle('top', ((document.body.clientHeight - form_size.y) / 2) + 'px');
  modal_x.setStyle('left', ((document.body.clientWidth  - form_size.x)  / 2) + 'px');
  modal_x.setStyle('position', 'fixed');
}

function modalFormX_hide() {
  modal_x.set('html', '');
  modal_x.setStyle('display', 'none');
}

function modalFormX_show(type, action, id) {
  new Request.JSON({
    url: get_path(type),
    method: 'get',
    data: 'mode=JSON&context=modalformx&type=' + type + '&action=' + action + '&id=' + id,
    onSuccess: function(response) {
      modal_x.set('html', response.content);
      modal_x.setStyle('display', 'block');
      modalFormX_init();
      if(typeof response.js_code != "undefined") {
        eval(response.js_code);
      }
    }
  }).send();
}

function ajax_modalform_submit(form_prefix_id, form_suffix_id) {
  var form = $(form_prefix_id + form_suffix_id);
  var progress = $(form_prefix_id + '_ajax_loading');
  if(form) {
    form.set('send',
      {
        onSuccess: function(html_response) {
          var response = JSON.decode(html_response);
          if(response.status == 'ok') {
            //showMessage('Данные успешно изменены', 'ok');
            reload(js_config['admin_url']);
          } else {
            progress.setStyle('display', 'none');
            showMessage(response.error, 'error');
          }
        }}
      );
    form.send();
    progress.setStyle('display', 'block');
  }
}

/*
      window.addEvent('domready', function() {
        request_page_id($('tid').get('value'));
        update_offer_view_link($('offer_id').get('value'));
      });
*/