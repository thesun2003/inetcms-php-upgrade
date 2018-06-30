<?php
using::add_class('entity');
using::add_class('jpeg');
//using::add_class('image_upload');

class Images extends Entity {
  var $width;
  function Images($info=false){
    $this->Entity(getTablePrefix() . 'images');

    $this->form->addField('id');
    $this->form->set('filename', '');
    $this->form->set('gallery_id', -1);
    $this->form->set('descr', '');
    $this->form->set('position', 0);

    $this->IMAGES_DIR = ROOT . '/photos/';
    $this->IMAGES_URL = '/photos/';

    if (!empty($info)) {
      $this->setInfo($info);
    }
    $this->width = 480;
  }

  function get_url() {
    return $this->IMAGES_URL . $this->get('filename');
  }

  function get_path() {
    return $this->IMAGES_DIR . $this->get('filename');
  }
  
  function get_dimension($type = 'width', $size = 100) {
    $image_sizes = @getimagesize($this->get_path());
    $dim = $type == 'width' ? $image_sizes[0] : $image_sizes[1];
    return min($dim, $size);
  }
  
  function get_height($size = 100) {
    return $this->get_dimension('height', $size);
  }

  function get_width($size = 100) {
    return $this->get_dimension('width', $size);
  }

  function getAdminForm2($gallery_id) {
    $form   = array();
    $form[] = '<table border="0" width="200">';
    $form[] = '<tr><td colspan="2" align="center">';
    
    $form[] = ImageUpload::render($this->get('id'), $this->IMAGES_URL . $this->get('filename'));
    
    $form[] = '</td></tr>';
    $form[] = '<tr>';
    $form[] = '<td align="center"><a onclick="return confirm(\'Вы точно хотите удалить?\')" href="javascript:dropImage(' . $this->get('id') . ', ' . $gallery_id . ');"><img src="/admin/img/b_drop.png" border="0" alt="Удалить" title="Удалить"></a></td>';
    $form[] = '<td align="center"><a onclick="showDescrForm(' . $this->get('id') . ')"><img src="/admin/img/b_edit.png" border="0" alt="Редактировать подпись к картинке" title="Редактировать подпись к картинке"></a></td>';
    $form[] = '</tr>';
    $form[] = '<tr>';
    $form[] = '<td align="center" colspan="2">';
    $form[] = '<div class="image_description" id="descr_' . $this->get('id') . '">' . $this->get('descr', false) . '</div>';
    $form[] = '<div style="display:none" id="change_descr_' . $this->get('id') . '"><textarea id="change_descr_text_' . $this->get('id') . '"style="width:200px;height:100px">' . $this->get('descr') . '</textarea><br /><input type="button" value="Изменить" onclick="updateDescr(' . $this->get('id') . ');"></div>';
    $form[] = '</td>';
    $form[] = '</tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }

  function getAdminForm($gallery_id) {
    $form   = array();
    $form[] = '<table border="0" width="200">';
    $form[] = '<tr><td colspan="2" align="center"><img src="' . $this->IMAGES_URL . $this->get('filename') . '" /></td></tr>';
    $form[] = '<tr>';
    $form[] = '<td align="center"><a onclick="return confirm(\'Вы точно хотите удалить?\')" href="javascript:dropImage(' . $this->get('id') . ', ' . $gallery_id . ');"><img src="/admin/img/b_drop.png" border="0" alt="Удалить" title="Удалить"></a></td>';
    $form[] = '<td align="center"><a onclick="showDescrForm(' . $this->get('id') . ')"><img src="/admin/img/b_edit.png" border="0" alt="Редактировать подпись к картинке" title="Редактировать подпись к картинке"></a></td>';
    $form[] = '</tr>';
    $form[] = '<tr>';
    $form[] = '<td align="center" colspan="2">';
    $form[] = '<div class="image_description" id="descr_' . $this->get('id') . '">' . $this->get('descr', false) . '</div>';
    $form[] = '<div style="display:none" id="change_descr_' . $this->get('id') . '"><textarea id="change_descr_text_' . $this->get('id') . '"style="width:200px;height:100px">' . $this->get('descr') . '</textarea><br /><input type="button" value="Изменить" onclick="updateDescr(' . $this->get('id') . ');"></div>';
    $form[] = '</td>';
    $form[] = '</tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }

  function getUserPage() {
    $form   = array();
    $form[] = '<table border="0" width="200">';
    $form[] = '<tr><td colspan="2" align="center"><img src="' . $this->IMAGES_URL . $this->get('filename') . '" /></td></tr>';
    $form[] = '<tr>';
    $form[] = '<td align="center" colspan="2">';
    $form[] = '<div class="image_description">' . $this->get('descr', false) . '</div>';
    $form[] = '</td>';
    $form[] = '</tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }

  function getImage($width = false, $height = false) {
    $form   = array();
    $form[] = '<img border="0" src="' . $this->IMAGES_URL . $this->get('filename') . '" '.($width ? ' width="'.$width.'" ' : '') . ($height ? ' height="'.$height.'" ' : '').' />';
    return implode("\n", $form);
  }


  function upload() {
     $attr = array();
     if (!empty($_FILES)) {
         foreach ($_FILES as $key => $image) {
             $ff = name2time($image['name']);
             if ($ff == "") $ff='';
             if (file_exists($this->IMAGES_DIR . $ff)&&($ff !=''))
             {
              $q = time('U');
              $dop = ($q-(round($q/100000)-1)*100000);
              $ff=$dop.$ff;
             }
             if ($ff !='') {
                 $image = new JPEG($image['tmp_name'], array($this->width, $this->width));
                 $image->save($this->IMAGES_DIR . $ff);
             }
             $attr[$key] = $ff;
         }
     }
     return $attr;
  }

  function add() {
      $attr = $this->upload();
      if (!empty($attr['image'])) {
          $_POST['filename'] = $attr['image'];
          $this->setInfo($_POST);
          $this->save();
          $this->set('position', $this->get('id'));
          $this->save();
          return true;
      } else {
          return false;
      }
  }

  function delete() {
     @unlink($this->IMAGES_DIR . $this->get('filename'));
     $this->del();
  }

}
