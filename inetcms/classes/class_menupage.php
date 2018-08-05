<?php
using::add_class('menu');
using::add_class('page');

class MenuPage {
  var $menu, $page;

  function MenuPage($info=false){
    $this->menu = new Menu();
    $this->page = new Page();

    if (!empty($info)) {
      if (!empty($info['id']) && is_numeric($info['id'])) {
        $search = $this->menu->find(array('id' => $info['id']));
        if ($search->hasNext()) {
          $this->menu = $search->next();
        }
      }
      $this->menu->setInfo($info);
    }

    if ($this->menu->get('id')) {
      $search = $this->page->find(array('menu_id' => $this->menu->get('id')));
      if ($search->hasNext()) {
        $this->page = $search->next();
      }
    } else {
      $this->menu->set('type', 'page');
    }

    $page_info = $info;
    unset($page_info['id']);
    $this->page->setInfo($page_info);
  }

  function save() {
    $this->menu->save();
    if (!$this->page->get('id')) {
      $this->page->set('menu_id', $this->menu->get('id'));
    }
    $this->page->save();
  }

  function isValid() {
    return ($this->menu->isValid() && $this->page->isValid());
  }
  
  static function getContentById($id) {
    $page = new self(array('id' => $id));
    if ($page->page->get('id')) {
      return $page->page->getHTML();
    } else {
      return false;
    }
  }

  public static function getTextField($params = array()) {
      $content = SimplePage::process_template_file(
          Module::getModulePath('core'),
          'modalformx/text_field',
          $params
      );

      return $content;
  }

  public static function getModalFormValues($action, $id, $type) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    $menupage = new self(array('id' => $id));
    $formTitleSuffix = $type == 'menu' ? 'категорию' : 'страницу';

    $textFields = array();
    foreach ($menupage->menu->form->getAdminEditableFields() as $fieldName) {
        $params = array(
            'field_title' => 'название' . (strpos($fieldName, 'eng') ? ' (eng)' : ''),
            'field_name' => $fieldName,
            'field_value' => $action == 'change' ? $menupage->menu->get($fieldName) : '',
        );
        $textFields[] = self::getTextField($params);
    }
    switch($action) {
      case 'add':
        $result['action_value'] = ADMIN_INC_FILE . '/save_menu.php';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('core'),
          'modalformx/menupage_add',
          array(
              'parent_id' => $id,
              'type' => $type,
              'text_fields' => implode($textFields),
              'form_title' => 'Добавить новую ' . $formTitleSuffix,
          )
        );
      break;
      case 'change':
        $result['action_value'] = ADMIN_INC_FILE . '/save_menu.php';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('core'),
          'modalformx/menupage_change',
          array(
            'id' => $id,
            'name' => $menupage->menu->get('name'),
            'type' => $type,
            'text_fields' => implode($textFields),
            'form_title' => 'Изменить ' . $formTitleSuffix,
          )
        );
      break;
    }
    return $result;
  }  
}
