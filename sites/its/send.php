<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('captcha');
using::add_class('mail');

if(!Captcha::check_captcha()) {
  reload('/?cat_id=5');
}

$fields = array(
  "name" => 'Ф.И.О.',
  "company" => 'Компания',
  "status" => 'Должность',
  "city" => 'Город',
  "phone" => 'Телефон',
  "fax" => 'Факс',
  "email" => 'E-mail',
  "message" => 'Текст сообщения'
);

$mess_body = array();
foreach($fields as $key => $value) {
  if(!empty($_POST[$key])) {
    $mess_body[] = '<b>'.$value.':</b> ' . $_POST[$key];
  }
}

$to = 'ssa555@mail.ru';
//$to = 'asterix@softservice.org';
$subject = 'Вопрос с сайта компании "ИТ-Сервис"';
$mess_body = implode("\n<br />", $mess_body);
$fromName ='сайт ИТ-Сервис';
$fromMail = 'no-reply@its-nsk.ru';

$mail = new TMail($to, $subject, $mess_body, $fromName, $fromMail);
$mail->send();

reload('/?cat_id=12');