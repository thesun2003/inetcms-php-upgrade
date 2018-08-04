<?php
using::add_class('menu');
using::add_class('page');

class MenuPage {
  var $menu, $page, $content;
  function MenuPage($info=false){
    $this->menu = new Menu();
    $this->page = new Page();
    $this->content = "";

    if (!empty($info)) {
      if (!empty($info['id']) && is_numeric($info['id'])) {
        $search = $this->menu->find(array('id' => $info['id']));
        if ($search->hasNext()) {
          $this->menu = $search->next();
        }
      }
      $this->menu->setInfo($info);
    }
    
    if (!empty($info['content'])) {
      $this->content = $info['content'];
    }

    if ($this->menu->get('id')) {
      $search = $this->page->find(array('menu_id' => $this->menu->get('id')));
      if ($search->hasNext()) {
        $this->page = $search->next();
      }
    } else {
      $this->menu->set('type', 'page');
    }

    if (!empty($this->content)) {
      $this->page->set('content', $this->content);
    }

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

  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    switch($action) {
      case 'add':
        $result['action_value'] = ADMIN_INC_FILE . '/save_menu.php';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('core'),
          'modalformx/page_add',
          array('parent_id' => $id)
        );
      break;
      case 'change':
        $result['action_value'] = ADMIN_INC_FILE . '/save_menu.php';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('core'),
          'modalformx/page_change',
          array(
            'id' => $id,
            'name' => Menu::getNameById($id)
          )
        );
      break;
    }
    return $result;
  }  
}
