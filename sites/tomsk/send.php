<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('captcha');
using::add_class('mail');

if(!Captcha::check_captcha()) {
  reload('/?cat_id=2');
}

$fields = array(
  "name" => '�.�.�.',
  "company" => '��������',
  "status" => '���������',
  "city" => '�����',
  "phone" => '�������',
  "fax" => '����',
  "email" => 'E-mail',
  "message" => '����� ���������'
);

$mess_body = array();
foreach($fields as $key => $value) {
  if(!empty($_POST[$key])) {
    $mess_body[] = '<b>'.$value.':</b> ' . $_POST[$key];
  }
}

$to = 'TomskTorgStroy@inbox.ru';
//$to = 'asterix@softservice.org';
$subject = '��������� � ����� �������� "���������"';
$mess_body = implode("\n<br />", $mess_body);
$fromName ='���� ���������';
$fromMail = 'no-reply@tomsktorgstroy.ru';

$mail = new TMail($to, $subject, $mess_body, $fromName, $fromMail);
$mail->send();

reload('/?cat_id=4');