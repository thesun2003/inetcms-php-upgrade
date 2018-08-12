<?php
using::add_class('images');
using::add_class('ajax');

class Gallery extends Entity
{
  var $images;

  function __construct($info=false) {
    parent::__construct(getTablePrefix() . 'gallery');

    $this->form->addField('id');
    $this->form->addField('col_num');
    $this->form->addField('width');
    $this->form->addField('limit');

    if (!empty($info)) {
      $this->setInfo($info);
    }

    $this->images = array();

    $images = new Images();
    $search = $images->find(array('gallery_id' => $this->get('id')), 'position ASC, id ASC');
    while ($images = $search->next()) {
      $this->images[$images->get('id')] = $images;
    }
    if (count($this->images) > 2) {
      $this->images = array_slice($this->images, -2);
    }
  }

  function isLimited() {
    if ($this->get('limit') > 0 && count($this->images) >= $this->get('limit')) {
      return true;
    } else {
      return false;      
    }
  }

  static function getWidthById($gallery_id) {
    global $DB;
    return $DB->getOne("SELECT width FROM " . getTablePrefix() . "gallery WHERE id = " . $DB->quote($gallery_id));
  }

  static function getAdminAddForm($gallery_id) {
    $form = array();
    $form[] = '<form action="" method="post" enctype="multipart/form-data">';
    $form[] = '<input type="file" name="image" value="">';
    $form[] = '<input type="submit" value="Добавить" onclick="showUpload(' . $gallery_id . ')">';
    $form[] = '</form>';
    $form[] = '<script type="text/javascript">';
    $form[] = 'reloadGallery(' . $gallery_id . ');';
    $form[] = '</script>';
    return implode("\n", $form);
  }

  function getAdminGallery() {
    $form = array();
    $num = 0;
    $form[] = '<table border="0">';
    foreach ($this->images as $image) {
      if ($num % $this->get('col_num') == 0) {
        $form[] = '</tr>';
      }
      $form[] = '<td align="center" valign="top">' . $image->getAdminForm($this->get('id')) . '</td>';
      if ($num % $this->get('col_num') == $this->get('col_num')-1) {
        $form[] = '<tr>';
      }
      $num++;
    }

    if ($num % $this->get('col_num') == 0) {
      $form[] = '</tr>';
    }
    $form[] = '<td align="center">';
    $form[] = '<div id="gallery_upload_' . $this->get('id') . '" style="display:none"><img src="/admin/img/upload.gif" alt="Картинка загружается. Подождите пожалуйста" title="Картинка загружается. Подождите пожалуйста"></div>';
    $form[] = '</td>';
    if ($num % $this->get('col_num') == $this->get('col_num')-1) {
      $form[] = '<tr>';
    }

    $form[] = '</tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }

  function getAdminForm($url = '') {
    $form  = '';
    $url = Module::getModuleURL('core') . '/image_upload/';
    if(!empty($url)) {
      $form .= '<div class="gallery_edit_block">';
      $form .= '<div id="gallery_' . $this->get('id') . '"><!-- Gallery #' . $this->get('id') . ' -->' . Ajax_Loader::run() . '</div><br />';
      $form .= '<div id="upload_form_' . $this->get('id') . '"><iframe src="' . $url . 'add.php?gallery_id=' . $this->get('id') . '" width="400" height="55" frameborder="0" scrolling="no">Ваш браузер не поддерживает плавающие фреймы!</iframe></div></div>';
    }
    return $form;
  }

  function getAdminForm2() {
    $form  = '';
    $form .= '<div class="gallery_edit_block">';
    $form .= $this->getAdminGallery2();
    $form .= '</div>';
    return $form;
  }

  function getAdminGallery2() {
    $form = array();
    $num = 0;
    $form[] = '<table border="1">';
    foreach ($this->images as $image) {
      if ($num % $this->get('col_num') == 0) {
        $form[] = '</tr>';
      }
      $form[] = '<td align="center" valign="top">' . $image->getAdminForm2($this->get('id')) . '</td>';
      if ($num % $this->get('col_num') == $this->get('col_num')-1) {
        $form[] = '<tr>';
      }
      $num++;
    }

    if ($num % $this->get('col_num') == 0) {
      $form[] = '</tr>';
    }
    $form[] = '<td align="center">';
    $form[] = '<div id="gallery_upload_' . $this->get('id') . '" style="display:none"><img src="/admin/img/upload.gif" alt="Картинка загружается. Подождите пожалуйста" title="Картинка загружается. Подождите пожалуйста"></div>';
    $form[] = '</td>';
    if ($num % $this->get('col_num') == $this->get('col_num')-1) {
      $form[] = '<tr>';
    }

    $form[] = '</tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }

  function getUserPage() {
    $form = array();
    $num = 0;
    $form[] = '<table border="0">';
    foreach ($this->images as $image) {
      if ($num % $this->get('col_num') == 0) {
        $form[] = '</tr>';
      }
      $form[] = '<td align="center" valign="top">' . $image->getUserPage() . '</td>';
      if ($num % $this->get('col_num') == $this->get('col_num')-1) {
        $form[] = '<tr>';
      }
      $num++;
    }

    if ($num % $this->get('col_num') == 0) {
      $form[] = '</tr>';
    }
    $form[] = '</tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }

  function getListImage() {
    $result = '';
    if($this->images) {
      $image = array_shift($this->images);
      $result = $image->getImage(false, GALLERY_LIST_IMAGE_WIDTH);
    }
    return $result;
  }

}
