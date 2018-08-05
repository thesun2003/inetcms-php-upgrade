<?php

using::add_class('entity');

class Page extends Entity {
  function Page($info=false){
    $this->Entity(getTablePrefix() . 'page');
    $this->form->addField('id');
    $this->form->addField('content', true);
    $this->form->set('menu_id', 0);

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  function save() {
    parent::save();
  }

  static function clearHTML($page) {
    $replacement = array(
      '-moz-use-text-color' => '',
      '-moz-background-clip: -moz-initial;' => '',
      'background: white none repeat scroll 0%;' => '',
      'background: white none repeat scroll 0% 50%;' => ''
    );
    $page = preg_replace("/<!--(.*?)-->/is", "", $page);
    $page = str_replace(array_keys($replacement), $replacement, $page);
    return $page;
  }

  function getHTML() {
    return self::clearHTML($this->form->get('content', false));
  }

}
