<?php
using::add_class('module');
using::add_class('modules');
using::add_class('menu');
using::add_class('catalog_item', Module::getModulePath('catalog'));

class Catalog extends Module {
  var $module_id_field = 'catalog_id';
  var $module_name = 'catalog';
  static $name = 'Каталог товаров';

  function __construct($info=false){
    parent::__construct();
    $this->Entity(getTablePrefix() . 'catalog');
    $this->form->addField('id');
    $this->form->addField('site_id');

    $this->form->addField('xurl');
    $this->form->setRequired('name');
    $this->form->set('gallery1_id', '-1');
    $this->form->set('description', '');

    $this->form->set('visible', 0);

    $this->form->addField('title');
    $this->form->addField('descr');
    $this->form->addField('keyw');

    $this->form->set('parent_id', 0);
    $this->form->addField('position');

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  static function get_site_parent_id($site_id) {
    global $DB;
    return $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'catalog WHERE site_id = "' . $site_id. '"');
  }

  public function isValid($fields = false){
    global $LNG, $DB;
    parent::isValid();

    if($this->find(array('name' => $this->form->get('name')), false, false, 'parent_id = ' . $DB->quote($this->form->get('parent_id')) .' AND id != ' . $DB->quote($this->form->get('id')))->next()) {
      $this->form->addError('name', $LNG['catalog_name_must_be_unique']);
    }

    if(!preg_match("/^[a-z0-9_]*$/", $this->form->get('xurl'))) {
      $this->form->addError('xurl', $LNG['invalid_xurl']);
    }
    if($this->find(array('xurl' => $this->form->get('xurl')), false, false, 'id != ' . $DB->quote($this->form->get('id')))->next()) {
      $this->form->addError('name', $LNG['xurl_must_be_unique']);
    }

    return (bool)!$this->form->getErrors();
  }

  function getMetadata() {
    return array('title' => $this->get('title'),
                 'keywords' => $this->get('keyw'),
                 'description' => $this->get('descr'));
  }

  function save() {
    if ($this->form->get('gallery1_id') == '-1') {
      $gallery1 = new Gallery(array('col_num' => 1, 'limit' => 1, 'width' => '133'));
      $gallery1->save();
      $this->form->set('gallery1_id', $gallery1->get('id'));
    }
    if (!$this->form->get('xurl')) {
      $this->form->set('xurl', translit($this->form->get('name')));
    }
    if (!$this->get('id')) {
      parent::save();
      $this->form->set('position', $this->form->get('id'));
    }
    parent::save();
  }

  public function init() {
    global $JS_config_array;
    $JS_config_array['catalog_path'] = MODULES_URL . '/catalog/';
    $JS_config_array['catalog_item_path'] = MODULES_URL . '/catalog/';
    
    if (!isset($_SESSION['div_catalog'])) {
      $_SESSION['div_catalog'] = array();
    }
  }

  function getUpDown() {
    global $DB;
    $up   = $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'catalog WHERE parent_id = ' . $this->get('parent_id') . ' AND position < ' . $this->get('position') . ' ORDER BY position DESC limit 1');
    $down = $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'catalog WHERE parent_id = ' . $this->get('parent_id') . ' AND position > ' . $this->get('position') . ' ORDER BY position ASC limit 1');
    return array('up' => $up, 'down' => $down);
  }
  
  function has_items() {
    $item = new CatalogItem();
    $search = $item->find(array('catalog_id' => $this->get('id')));
    return $search->hasNext();
  }

  function has_children() {
    $search = $this->find(array('parent_id' => $this->get('id')));
    return $search->hasNext();
  }
  
  static function static_has_children($id) {
    if(!$id) {
      return true;
    }
    $item = new Catalog();
    $item = $item->find(array('id' => $id))->next();
    if($item) {
      return $item->has_children();
    }
    debug_log('catalog not found: id = ' . $id);
    return false;
  }

  public static function is_opened($id) {
    return isset($_SESSION['div_catalog'][$id]);
  }

  function get_children_list_by_parent_id($parent_id_field, $parent_id, $level = 0, $is_open = false) {
    $parent_result = parent::get_children_list_by_parent_id($parent_id_field, $parent_id, $level);
    $result = array();
    if(!self::static_has_children($parent_id)) {
      $catalog_item = new CatalogItem();
      $search = $catalog_item->find(array('catalog_id' => $parent_id), 'position');
      while($catalog_item = $search->next()) {
        $result[] = array(
          'object' => $catalog_item,
          'level' => $level
        );
      }
    }
    foreach($parent_result as $item) {
      $result[] = $item;
      if(($is_open && self::is_opened($item['object']->get('id'))) || !$is_open) {
        $result[] = '-' . $item['object']->get('id');
      }
    }
    return $result;
  }

  static function admin_render($parent_id = 0) {
    $result = '';
    $begin_delimeter = '<div style="display:[display]" id="catalog_[item_id]">';
    $end_delimeter = '</div>';
    $parent_item = new self();
    $min_level = 0;
    if($parent_id) {
      $parent_item = $parent_item->find(array('id' => $parent_id))->next();
      $min_level = $parent_item->get_item_level('parent_id');
    }
    $parent_list = $parent_item->get_children('parent_id', true);

    $num_div_open = 0;
    foreach($parent_list as $k => $item) {
      $object = $item['object'];
      $level = $item['level'];
      $type = strtolower(get_class($item['object']));
      $next_level = -1;
      if ($k < count($parent_list)-1) {
        $next_level = $parent_list[$k+1]['level'];
      }

      if($num_div_open > $level) {
        while($num_div_open != $level) {
          $result .= $end_delimeter;
          $num_div_open--;
        }
      }
      
      if ($type == 'catalog') {
        $result .= $object->get_admin_item($level + $min_level);
        if($level < $next_level) {
          $display = 'block';
        } else {
          $display = 'none';
        }
        $tpl = new SimpleTemplate($begin_delimeter);
        $result .= $tpl->process_template(array(
          'display' => $display,
          'item_id' => $object->get('id')
        ));
        $num_div_open++;
      } elseif ($type == 'catalogitem') {
        $result .= $object->get_admin_item($level + $min_level);
      }
    }

    while($num_div_open != 0) {
      $result .= $end_delimeter;
      $num_div_open--;
    }

    return $result;
  }
  
  function get_url() {
    if (CATALOG_USE_XURL) {
      return '/catalog/' . $this->get('xurl') . '/';
    } else {
      return parent::get_url();
    }
  }
  
  function process_admin_page() {
    $result = '<hr />';

    $root_object = new self();
    $root_object->setInfo(array(
      'id' => '0',
      'parent_id' => '-1',
      'name' => self::$name,
    ));
    $result .= $root_object->get_admin_item();
    return $result;
  }
  
  static public function process_user_page() {
    $content = $metadata = '';
    $catalog = new self();
    $catalog_item_xurl = $catalog_xurl = '';

    $url = get_url();
    if ($url['path']) {
      if ($url['path'][1]) {
        $catalog_xurl = $url['path'][1];
      }
      if ($url['path'][2]) {
        $catalog_item_xurl = $url['path'][2];
      }
    }

    if (!empty($catalog_xurl)) {
      $catalog = $catalog->find(array('xurl' => $catalog_xurl))->next();
    } elseif (!empty($_GET['catalog_id']) && is_numeric($_GET['catalog_id'])) {
      $catalog = $catalog->find(array('id' => $_GET['catalog_id']))->next();
    }

    if($catalog) {
      $content .= $catalog->get_page_title();
      if (!empty($catalog_item_xurl) && substr($catalog_item_xurl, -5) == '.html') {
        $catalog_item = new CatalogItem();
        $catalog_item = $catalog_item->find(array('xurl' => str_replace('.html', '', $catalog_item_xurl)))->next();
        if ($catalog_item) {
          $_GET['item_id'] = $catalog_item->get('id');
          $metadata = $catalog_item->getMetadata();
        }
      }

      if(!empty($_GET['item_id']) && is_numeric($_GET['item_id'])) {
        $content .= $catalog->get_item($_GET['item_id']);
      } else {
        $content .= $catalog->get_catalog_content();
        $metadata = $catalog->getMetadata();
      }
    }
    
    if ($content) {
      if (CATALOG_USE_XURL && !$url['path']) {
        $rewrite_url = '/';
        if (!empty($_GET['item_id'])) {
          $catalog_item = new CatalogItem();
          $catalog_item = $catalog_item->find(array('id' => $_GET['item_id']))->next();
          $rewrite_url = '/catalog/' . $catalog->get('xurl') . '/' . $catalog_item->get('xurl') . '.html';
        } else {
          if ($catalog->get('id')) {
            $rewrite_url = '/catalog/' . $catalog->get('xurl') . '/';
          } else {
            $rewrite_url = '/catalog/';
          }
        }
        Rewrite_301::static_run($rewrite_url);
      }
      return array('content' => $content, 'metadata' => $metadata);
    } else {
      return false;
    }
  }
  
  /*
   * For 404 error page
   *
   */
  static public function get_main_catalog_block() {
    $catalog = new self();
    return $catalog->get_catalog_content();
  }
  
  // Catalog render
  function get_admin_item($level = 0) {
    $menu = $this;
    $is_root_item = false;
    if(!$menu->get('id')) {
      $is_root_item = true;
    }

    if (self::is_opened($menu->get('id'))) {
      $img_node = 'img/openednode.gif';
    } else {
      $img_node = 'img/closednode.gif';
    }

    $values = array(
      'menu_id' => $menu->get('id'),
      'name' => $menu->get('name'),
      'class' => '',
      'left_padding' => $level*16,
      'level' => $level,
      'field' => 'catalog',
      'menu_href' => '#',
      'catalog_items_droppables' => '',
    );
  
    if(!$is_root_item) {
      $values['menu_href'] = ModalForm::getLinkX('catalog', 'change', $menu->get('id'));
    }

    if ($menu->has_children() || $menu->has_items()) {
      $toggle_menu_block = '<a onclick="toggleMenu(' . $menu->get('id') .', \'catalog\')"><img id="catalog_item_image_' . $menu->get('id') . '" src="' . $img_node . '" width="16" height="22" align="middle" alt="" border="0"></a>';
    } else {
      $toggle_menu_block = '<img id="catalog_item_image_' . $menu->get('id') . '" src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">';
      $values['class'] = 'empty_folder';
    }
    $values['toggle_menu_block'] = $toggle_menu_block;
    $actions_block = '';

    if(!$is_root_item) {
      if ($menu->get('visible') == '0') {
        //$actions_block .= admin_button::get('show', ADMIN_INC_FILE . "/show_hide.php?id=" . $menu->get('id'), '');
      } else {
        //$actions_block .= admin_button::get('hide', ADMIN_INC_FILE . "/show_hide.php?id=" . $menu->get('id'), '');
      }
      //$actions_block .= admin_button::get('seo', '/admin/admin.php?type=seo_editpage&id=' . $menu->get('id'), ' для страницы &quot;' . $menu->get('name') . '&quot;');
    }

    if($is_root_item || !$menu->has_items()) {
      $actions_block .= admin_button::get('new_menu', ModalForm::getLinkX('catalog', 'add', $menu->get('id')), ' новую подкатегорию');
    }

    if(!$is_root_item) {
      if(!$menu->has_children()) {
        $actions_block .= admin_button::get('new_page', ModalForm::getLinkX('catalog_item', 'add', $menu->get('id')), ' новый товар');
        $values['catalog_items_droppables'] = 'catalog_items_droppables';
      }
      $actions_block .= admin_button::get('edit', '/admin/admin.php?type=module_edit&module_name=catalog&context=catalog&id=' . $menu->get('id'), ' категорию &quot;' . $menu->get('name') . '&quot;');
      $actions_block .= admin_button::get('del', "javascript:ondel('" . MODULES_URL . "/catalog/?action_suffix=catalog&action=delete&id=" . $menu->get('id') . "');", '');
      $arrows = $menu->getUpDown();

      if (!empty($arrows['down'])) {
        $actions_block .= admin_button::get('arrow_down', MODULES_URL . "/catalog/?action_suffix=catalog&action=changepos&fid=" . $menu->get('id') . "&tid=" . $arrows['down'], '');
      }
      if (!empty($arrows['up'])) {
        $actions_block .= admin_button::get('arrow_up', MODULES_URL . "/catalog/?action_suffix=catalog&action=changepos&fid=" . $menu->get('id') . "&tid=" . $arrows['up'], '');
      }
    }
    
    $values['actions_block'] = $actions_block;
    $content = SimplePage::process_template_file(
      MODULES . '/catalog',
      'menu/catalog',
      $values
    );
    
    if($is_root_item) {
      if(self::is_opened(0)) {
        $content .= '<div style="display:block;padding-left:16px" id="catalog_0">';
        $content .= self::admin_render();
        $content .= '</div>';
      } else {
        $content .= '<div style="display:none;padding-left:16px" id="catalog_0"></div>';
      }
      $content .= '<script type="text/javascript">update_grag_and_drop2();</script>';
    }
    
    return $content;
  }
  
  function get_item($item_id = 0, $is_admin = false) {
    $content = '';
    $catalog_item = new CatalogItem();
    if(!empty($_GET['context']) && $_GET['context'] == 'catalog') {
      $catalog_item = new self();
    }
    $catalog_item = $catalog_item->find(array('id' => $item_id))->next();
    if($catalog_item) {
      if(!$is_admin) {
        $content = $catalog_item->getUserPage();
      } else {
        $content = $catalog_item->editForm();
      }
    }
    return $content;
  }

  function get_catalog_menu($use_parent = false, $catalog_id = 0) {
    $cat = array();
    $catalog = new self();
    if(!$this->get('id')) {
      $this->set('id', $catalog_id);
    }
    $search = $catalog->find(array(), 'position', false , 'parent_id = ' . $this->get($use_parent ? 'parent_id' : 'id'));
    while ($catalog = $search->next()) {
      $cat[$catalog->get('id')] = $catalog;
    }
    return $cat;
  }

  private function get_catalog_contentlist($selected = false) {
    $content = '';
    $menu = $this->get_catalog_menu($selected ? true : false);
    $parity = 0;
    foreach ($menu as $item) {
      $gallery1 = new Gallery();
      $gallery1 = $gallery1->find(array('id' => $item->get('gallery1_id')))->next();
      
      $image_url = '/images/empty.gif';
      if($gallery1->images) {
        $image = array_shift($gallery1->images);
        $image_url = $image->IMAGES_URL . $image->get('filename');
      }

      $id = $item->get('id');
      $class = 'catalog_list';
      if($selected == $id) {
        $class .= ' selected';
      }
      $values = array(
        'gallery1' => $image_url,
        'class' => $class,
        'item_url' => $item->get_url(),
        'item_name' => $item->get('name'),
        'item_description' => str_replace("<br />\n<br />", '<br>', $item->get('description', false)),
        'parity_clear' => $parity ? '<br style="clear:both">' : ''
      );
      $content .= SimplePage::process_template_file(
        MODULES . '/catalog',
        'catalog_list',
        $values
      );
      $parity = $parity == 0 ? 1 : 0;
    }
    return '<div>' . $content . '</div>';
  }

  private function get_catalog_content() {
    $content = $this->get_catalog_contentlist();

    if(empty($_GET['showall']) && $this->has_children()) {
      if($this->form->get('id')) {
        $content .= '<br style="clear:both"/><br /><a class="catalog_list" href="'.All::urlReplace(array('showall' => '1'), false, true).'">Показать все товары текущего раздела</a>';
      }
    } else {
      if(!$this->has_children()) {
        //$content .= $this->get_catalog_contentlist($this->get('id'));
      }
      $content .= '<br style="clear:both"/><br />' . $this->get_action_items();
    }
    return $content;
  }

  function get_action_items() {
    global $DB;
    $items_on_page = 20;
    $pn = 1;
    $limit = All::get_limit($items_on_page, $pn, false);
    
    $catalog_item = new CatalogItem();
    $catalog_items = $this->get_children();
    //var_dump_pre($catalog_items);

    $catalog_ids = array($this->get('id'));
    foreach ($catalog_items as $item) {
      if(get_class($item['object']) == get_class($this)) {
        $catalog_ids[] = $item['object']->get('id');
      }
    }
    $total_items = CatalogItem::get_items_count($catalog_ids);
  
    $content_td = '';
    $search = $catalog_item->find(array(), 'position', $limit, 'catalog_id IN (' . implode(',', $catalog_ids) . ')');
    
    $parity = 0;    
    while ($catalog_item = $search->next()) {
      $content_td .= $catalog_item->getList($parity++ % 2);
    }

    $content = SimpleTemplate::process_file($this->path . '/templates/list_template.html', array(
      'items_list' => $content_td,
      'pages' => All::get_pages($pn, $items_on_page, $total_items, empty($_GET['showall']))
    ));
    return $content;
  }

  static function get_catalog_html($catalog_id = 0, $is_top = true) {
    //$content = '<hr />';
    $content = '';
    $catalog = new self();
    $top_menu = $catalog->get_catalog_menu(false, $catalog_id);
    foreach ($top_menu as $id => $top_item) {
      $content .= '<a class="catalog_menu' . ($is_top ? '_top' : '') . '" href="' . $top_item->get_url() . '">' . $top_item->get('name') . '</a><br>';
      if ($is_top) {
        $content .= self::get_catalog_html($id, false);
      }
    }
    return $content;
  }

  function get_page_title($title = '') {
    $menu = new Menu();
    $menu = $menu->find(array('id' => Modules::getMenuIdByModuleName($this->module_name)))->next();
    
    if (CATALOG_USE_XURL) {
      $menu_href = '<a href="/catalog/">' . self::$name . '</a>';
    } else {    
      $menu_href = '<a href="/?'.$this->module_id_field.'=0">' . self::$name . '</a>';
    }
    
    $history = array($menu_href);
    $title .= $this->get_history($history);
    return parent::get_page_title($title);
  }


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

 
  public function install() {
    parent::install();
    $modules = new Modules();
    $module_name = get_class($this);
    if(!Modules::isModuleInstalled($module_name)) {
      $modules->setInfo(
        array(
          'name' => self::$name,
          'module_name' => $module_name,
          'module_id' => $this->module_id_field
        )
      );
      $modules->save();
    }
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

  function editForm() {
    $form = "";
    $form .= "<h1 style=\"font-size:20px\" align='center'>" . $this->get('name') . "</h1>";
    $form .= '<form id="catalog_form" action="'.MODULES_URL.'/catalog/" method="post">';

    $gallery1 = new Gallery();
    $search = $gallery1->find(array('id' => $this->get('gallery1_id')));
    $gallery1 = $search->next();

    $description = new TextField('Краткое описание', 'description', $this->get('description'));

    $xurl = new TextField('Адрес страницы', 'xurl', $this->get('xurl'));
    $title = new TextField('Заголовок страницы', 'title', $this->get('title'));
    $descr = new TextField('Описание страницы', 'descr', $this->get('descr'));
    $keyw = new TextField('Ключевые слова страницы', 'keyw', $this->get('keyw'));

    $form .= $gallery1->getAdminForm();
    $form .= $description->getAdminForm();

    $form .= '<h2>Настройки страницы</h2><div id="page_settings">';
    $form .= $xurl->getAdminForm();
    $form .= $title->getAdminForm();
    $form .= $descr->getAdminForm();
    $form .= $keyw->getAdminForm();
    $form .= '</div>';

    $form .= '<input type="hidden" name="id" value="'.$this->get('id').'" />';
    $form .= '<input type="hidden" name="action" value="edit" />';
    $form .= '<input type="hidden" name="action_suffix" value="catalog" />';
    $form .= '<input type="button" name="update_form" value="Изменить" onclick="ajax_catalog_item_submit(\'catalog_form\')" />';

    return $form;
  }
  
  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    switch($action) {
      case 'add':
        $result['action_value'] = MODULES_URL . '/catalog/';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/catalog',
          'modalformx/catalog_add',
          array('parent_id' => $id)
        );
      break;
      case 'change':
        $result['action_value'] = MODULES_URL . '/catalog/';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/catalog',
          'modalformx/catalog_change',
          array(
            'id' => $id,
            'name' => self::getNameById($id)
          )
        );
      break;
    }
    return $result;
  }
}
