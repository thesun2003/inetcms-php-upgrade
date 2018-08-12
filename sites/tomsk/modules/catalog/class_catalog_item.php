<?php

using::add_class('textedit2');
using::add_class('textfield');
using::add_class('gallery');

class CatalogItem extends Entity
{
  function __construct($info=false){
    parent::__construct(getTablePrefix() . 'catalog_items');

    $this->form->addField('id');
    $this->form->addField('site_id', 0);

    $this->form->addField('articul');
    $this->form->addField('xurl');
    $this->form->setRequired('name');
    
    $this->form->set('gallery1_id', '-1');
    
    $this->form->set('description', '');
    $this->form->set('cost', '0.00');
    $this->form->set('content', '');

    $this->form->addField('title');
    $this->form->addField('descr');
    $this->form->addField('keyw');

    $this->form->set('catalog_id', '0');
    $this->form->set('date_price_updated', strftime(MYSQL_TIME));
    $this->form->setStored('date_auto_updated', false);

    $this->form->addField('position');

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  function getUpDown() {
    global $DB;
    $up   = $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'catalog_items WHERE catalog_id = ' . $this->get('catalog_id') . ' AND position < ' . $this->get('position') . ' ORDER BY position DESC limit 1');
    $down = $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'catalog_items WHERE catalog_id = ' . $this->get('catalog_id') . ' AND position > ' . $this->get('position') . ' ORDER BY position ASC limit 1');
    return array('up' => $up, 'down' => $down);
  }

  public static function move($id, $new_catalog_id) {
    $item = new self();
    $item = $item->find(array('id' => $id))->next();
    if($item) {
      $item->set('catalog_id', $new_catalog_id);
      $item->save();
    }
  }

  function save() {
    if (!$this->form->get('xurl')) {
      $this->form->set('xurl', translit($this->form->get('name')));
    }
    if (!$this->form->get('title')) {
      $this->form->set('title', $this->form->get('name'));
    }
    if (!$this->get('id')) {
      parent::save();
      $this->form->set('position', $this->form->get('id'));
    }
    parent::save();
    if ($this->form->get('gallery1_id') == '-1') {
      $gallery1 = new Gallery(array('col_num' => 2, 'limit' => 2, 'width' => '375'));
      $gallery1->save();
      $this->form->set('gallery1_id', $gallery1->get('id'));
    }
    parent::save();
  }

  public function isValid($fields = false){
    global $LNG;
    parent::isValid();
    if(!is_numeric($this->form->get('cost'))) {
      $this->form->addError('cost', $LNG['catalog_item_cost_must_be_numeric']);
    }

    if(!preg_match("/^[a-z0-9_]*$/", $this->form->get('xurl'))) {
      $this->form->addError('xurl', $LNG['invalid_xurl']);
    }

    return (bool)!$this->form->getErrors();
  }

  static function get_items_count($catalog_ids = array()) {
    global $DB;
    if($catalog_ids) {
      return $DB->getOne('SELECT count(id) FROM ' . getTablePrefix() . 'catalog_items WHERE catalog_id IN (' . implode(',', $catalog_ids) . ')');
    }
    return 0;
  }

  function getMetadata() {
    $catalog = new Catalog();
    $catalog = $catalog->find(array('id' => $this->get('catalog_id')))->next();
    if (!$catalog) {
      $catalog = new Catalog();
    }

    return array('title' => $catalog->get('name') .' '. $this->get('title'),
                 'keywords' => $this->get('keyw'),
                 'description' => $this->get('descr'));
  }

  // Catalog render
  function get_admin_item($level = 0) {
    $menu = $this;

    $values = array(
      'menu_id' => $menu->get('id'),
      'left_padding' => $level*16,
      'level' => $level,
      'field' => 'catalog_item',
      'name' => $menu->get('name'),
      'menu_href' => ModalForm::getLinkX('catalog_item', 'change', $menu->get('id')),
    );

    $values['menu_link'] = '<a id="catalog_item_' . $menu->get('id') . '_name" href="' . ModalForm::getLinkX('catalog_item', 'change', $menu->get('id')) . '"  title="Изменить имя раздела &quot;' . $menu->get('name') . '&quot;" onmouseover="openMenuActions(\'' . $menu->get('id') . '\')" onmouseout="closeMenuActions(\'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a>';
   
    $actions_block = '';
    $actions_block .= admin_button::get('edit', '/admin/admin.php?type=module_edit&module_name=catalog&id=' . $menu->get('id'), ' для товара &quot;' . $menu->get('name') . '&quot;');
    $actions_block .= admin_button::get('del', "javascript:ondel('" . Module::getModuleURL('catalog') . "/?action_suffix=catalog_item&action=delete&id=" . $menu->get('id') . "');", '');
    
    $arrows = $menu->getUpDown();
    if (!empty($arrows['down'])) {
      $actions_block .= admin_button::get('arrow_down', Module::getModuleURL('catalog') . "/?action_suffix=catalog_item&action=changepos&fid=" . $menu->get('id') . "&tid=" . $arrows['down'], '');
    }
    if (!empty($arrows['up'])) {
      $actions_block .= admin_button::get('arrow_up', Module::getModuleURL('catalog') . "/?action_suffix=catalog_item&action=changepos&fid=" . $menu->get('id') . "&tid=" . $arrows['up'], '');
    }

    $values['actions_block'] = $actions_block;
    $content = SimplePage::process_template_file(
      Module::getModulePath('catalog'),
      'menu/catalog_item',
      $values
    );
    return $content;
  }
  
  static function get_search_image($item_id) {
    $item = new CatalogItem();
    $item = $item->find(array('id' => $item_id))->next();
    $url = '';
    if($item) {
      $image = new Images();
      $image = $image->find(array('gallery_id' => $item->get('gallery1_id')))->next();
      if($image) {
        $url = $image->IMAGES_URL . $image->get('filename');
      }
    }
    return $url;
  }

  function getUserPage() {
    $gallery1 = new Gallery();
    $gallery1 = $gallery1->find(array('id' => $this->get('gallery1_id')))->next();
    
    $gallery = '';
    if(count($gallery1->images) == 2) {
      $image1 = array_shift($gallery1->images);
      $image2 = array_shift($gallery1->images);
      $gallery = '<a href="'.$image2->get_url().'" title="" rel="lightbox"><img alt="'.$this->get('name', false).'" title="'.$this->get('name', false).'" height="'.$image1->get_height(150).'" src="'.$image1->get_url().'" border="0"/></a>';
    } elseif(count($gallery1->images) == 1) {
      $image1 = array_shift($gallery1->images);
      $gallery = '<a href="'.$image1->get_url().'" title="" rel="lightbox"><img alt="'.$this->get('name', false).'" title="'.$this->get('name', false).'" height="'.$image1->get_height(150).'" src="'.$image1->get_url().'" border="0"/></a>';
    }

    $values = array(
      'gallery1' => $gallery,
      'content' => $this->get('content', false),
      'cost' => number_format($this->get('cost'), 2, '.', ' '),
      'name' => $this->get('name', false),
    );
    $content = SimpleTemplate::process_file(
      Module::getModulePath('catalog') . '/templates/template.html',
      $values
    );
    return $content;
  }

/*
  function getCatalogList($action_id = '') {
    $result = array();
    $items = new self();
    if (!empty($action_id) && is_numeric($action_id)) {
      $search = $items->find(array('action_id' => $action_id));
      while ($items = $search->next()) {
        $result[$items->get('id')] = $items;
      }
    }
    return $result;
  }
*/

  function getList($parity = 0) {
    $gallery1 = new Gallery();
    $gallery1 = $gallery1->find(array('id' => $this->get('gallery1_id')))->next();
    
    $catalog = new Catalog();
    $catalog = $catalog->find(array('id' => $this->get('catalog_id'), 'position'))->next();

    $values = array(
      'class_name' => $parity ? 'row1' : 'row2',
      'gallery1' => $gallery1->getListImage(),
      'item_name' => $this->get('name', false),
      'item_description' => str_replace("<br />\n<br />", '<br>', $this->get('description', false)),
      'item_cost' => number_format($this->get('cost'), 2, '.', ' '),
      //'item_link' => '/?cat_id=823&catalog_id=' . $this->get('catalog_id') .'&item_id='. $this->get('id'), //hardcoded for time
      
      
      'item_link' => '/?catalog_id=' . $this->get('catalog_id') .'&item_id='. $this->get('id'), //hardcoded for time
      
      
      
      'parity_clear' => $parity ? '<br style="clear:both">' : ''
    );
    
    if (CATALOG_USE_XURL) {
      $values['item_link'] = '/catalog/' . $catalog->get('xurl') .'/'. $this->get('xurl') . '.html'; //hardcoded for time
    }
    

    $content = SimpleTemplate::process_file(
      Module::getModulePath('catalog') . '/templates/list_template_td.html',
      $values
    );
    return $content;
  }

  function editForm() {
    $form = "";
    $form .= "<h1 style=\"font-size:20px\" align='center'>" . $this->get('name') . "</h1>";
    $form .= '<form id="catalog_item_form" action="'.Module::getModuleURL('catalog').'/" method="post">';

    $gallery1 = new Gallery();
    $search = $gallery1->find(array('id' => $this->get('gallery1_id')));
    $gallery1 = $search->next();

    $textEdit = new TextEdit2('content', $this->get('content'));
    $description = new TextField('Краткое описание', 'description', $this->get('description'));
    $date_added = new TextField('Цена', 'cost', $this->get('cost'));
    $articul = new TextField('Артикул (уникальный номер)', 'articul', $this->get('articul'));
    $xurl = new TextField('Адрес страницы', 'xurl', $this->get('xurl'));
    $title = new TextField('Заголовок страницы', 'title', $this->get('title'));
    $descr = new TextField('Описание страницы', 'descr', $this->get('descr'));
    $keyw = new TextField('Ключевые слова страницы', 'keyw', $this->get('keyw'));

    $form .= $articul->getAdminForm();
    $form .= $gallery1->getAdminForm();
    $form .= $date_added->getAdminForm();
    $form .= $description->getAdminForm();
    $form .= $textEdit->getAdminForm();

    $form .= '<h2>Настройки страницы</h2><div id="page_settings">';
    $form .= $xurl->getAdminForm();
    $form .= $title->getAdminForm();
    $form .= $descr->getAdminForm();
    $form .= $keyw->getAdminForm();
    $form .= '</div>';

    $form .= '<input type="hidden" name="id" value="'.$this->get('id').'" />';
    $form .= '<input type="hidden" name="action" value="edit" />';
    $form .= '<input type="hidden" name="action_suffix" value="catalog_item" />';
    $form .= '<input type="button" name="update_form" value="Изменить" onclick="textedit2_ajax_save(\'content\');ajax_catalog_item_submit(\'catalog_item_form\');textedit2_ajax_after_save(\'content\')" />';

    return $form;
  }

  static function getNameById($id) {
    $item = new self();
    $item = $item->find(array('id' => $id))->next();
    if($item) {
      return $item->get('name');
    } else {
      return false;
    }
  }
  
  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    switch($action) {
      case 'add':
        $result['action_value'] = Module::getModulePath('catalog') . '/';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('catalog'),
          'modalformx/catalog_item_add',
          array('parent_id' => $id)
        );
      break;
      case 'change':
        $item = new self();
        $item = $item->find(array('id' => $id))->next();

        $result['action_value'] = Module::getModuleURL('catalog') . '/';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('catalog'),
          'modalformx/catalog_item_change',
          array(
            'id' => $id,
            'name' => $item->get('name'),
            'articul' => $item->get('articul'),
          )
        );
      break;
    }
    return $result;
  }
}