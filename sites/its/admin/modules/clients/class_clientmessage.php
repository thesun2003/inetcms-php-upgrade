<?php
using::add_class('simplepage');

class ClientMessage extends Entity {
  function ClientMessage($info=false){
    $this->Entity(getTablePrefix() . 'messages');
    $this->form->addField('id');
    $this->form->set('type', 'ask');
    $this->form->setRequired('client_id');
    $this->form->setRequired('message');
    $this->form->set('date_added', strftime(MYSQL_TIME));

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  static function get_count($client_id) {
    global $DB;
    return $DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'messages WHERE client_id = ' . $client_id);
  }

  static function get_date($date) {
    return strftime('%d-%m-%Y %T', strtotime($date));
  }

  function get_message_html($is_admin = false) {
    $client = new Clients();
    $client = $client->find(array('id' => $this->get('client_id')))->next();
    if($is_admin) {
      $name = $this->get('type') == 'ask' ? $client->get('title') : 'Вы';
    } else {
      $name = $this->get('type') == 'ask' ? 'Вы' : 'ИТ Сервис';
    }

    $content = SimplePage::process_template_file(
      MODULES . '/clients',
      'list_template',
      array(
        'name' => $name,
        'item_date' => self::get_date($this->get('date_added')),
        'content' => $this->get('message', false),
        'item_type' => $this->get('type')
      )
    );
    return $content;
  }

  /*
  public function isValid($fields = false){
    global $LNG, $DB;
    parent::isValid();

    if($this->find(array('login' => $this->form->get('login')), false, false, 'id != ' . $DB->quote($this->form->get('id')))->next()) {
      $this->form->addError('login', $LNG['err_client_login']);
    }
    return (bool)!$this->form->getErrors();
  }*/
}