<?php
#  � ������ ����� ������ ������ �� �����, ������ ���� � ��� ���� ��������� � ��������� �� ���������
#  � ���������� ��������, �� �������������� ������ 100 ��� ������� � ����������.
error_reporting(0);
$config['url_name'] = "��������� �� �������";
$config['url_path'] = "/";

#  ���� � �����, ���������� ������ �����������, � ����� ����� ���������� �� ���������.
$config['recip_f'] = "recip.txt";
$config['recip_n'] = 1;

#  �� ��������� � ������ �������� ������ �� IP ������ � �������� ������ ���������.
#  ���� ��� ������, �� ������� �������� �� "0".
$config['features'] = 0;

#  ����� ���������. ���� �� ����� ������ �� ������, ����� ����������� ���������
#  ����������� �� ������� �� ������, ����� �������� �������� �� "0". ����� �����
#  �� ������ ����� :).
$config['copyshow'] = 0;
# �������
  function formtohtml ($str) {
    if (get_magic_quotes_gpc()) {
    $str = stripslashes($str);
    }
  $str = trim($str);
  $str = htmlspecialchars ($str, ENT_QUOTES);
  $str = str_replace("|", "/", $str);
  $str = str_replace("\r", "", $str);
  $str = str_replace("\n", "<br>", $str);
  $str = eregi_replace("(<br>*){2,}","<br><br>",$str);
  $str = eregi_replace("[ ]{2,}", " ", $str);
  return $str;
  }
  function htmltoform ($str) {
  $str = str_replace("&amp;", "&", $str);
  $str = str_replace("&quot;", "\"", $str);
  $str = str_replace("&#039;", "'", $str);
  $str = str_replace("&lt;", "<", $str);
  $str = str_replace("&gt;", ">", $str);
  $str = str_replace("<br>", "\r\n", $str);
  return $str;
  }
# ��������
  if (isset($_POST['go'])) {
  $var['fields'] = array("name"=>"���", "email"=>"e-mail", "subject"=>"���������", "message"=>"����� ���������");
  $notice['error'] = array();
    foreach ($var['fields'] as $key => $value) {
    if (empty($_POST[$key]) || (($_POST[$key] = formtohtml($_POST[$key])) == "0")) $notice['error'][] = $value;
    }
    if (empty($notice['error'])) {
      if (preg_match("/^([a-z,0-9,_,\-,\.])+\@([a-z,0-9,_,\-])+(\.([a-z,0-9])+)+$/",$_POST['email'])) {
      $t['c'] = file($config['recip_f']);
      $t['n'] = sizeof($t['c']);
        for ($i=0;$i<$t['n'];$i++) {
        $t['c'][$i] = explode("|",trim($t['c'][$i]));
        }
        if (!empty($_POST['recip']) and !empty($t['c'][$_POST['recip']-1])) {
        $var['recip_n'] = $t['c'][$_POST['recip']-1][1];
        $var['recip_e'] = $t['c'][$_POST['recip']-1][0];
          if (function_exists("imap_binary")) {
            function mail_convert($str) {
            $str = trim(imap_binary(addcslashes($str, "\"!@\\!@(!@)")));
            return $str;
            }
          $var['subj'] = "=?Windows-1251?B?".trim(imap_binary(htmltoform($_POST['subject'])))."?=";
          $var['header']  = "From: =?Windows-1251?B?".mail_convert(htmltoform($_POST['name']))."?= <".$_POST['email'].">\r\n";
          $var['header'] .= "MIME-Version: 1.0\r\n";
          $var['header'] .= "Content-Transfer-Encoding: 8bit\r\n";
          $var['header'] .= "Content-Type: text/plain; charset=\"Windows-1251\"\r\n";
          $var['header'] .= "X-Mailer: PHP v.".phpversion();
          $var['message']  = htmltoform($_POST['message']);
          if (!empty($config['features'])) $var['message'] .= "\r\n\r\n---------------------------------------\r\nIP ����� �����������: ".$_SERVER['REMOTE_ADDR'];
          # �������� ������� ���������
            if (mail("=?Windows-1251?B?".mail_convert(htmltoform($var['recip_n']))."?= <".$var['recip_e'].">",$var['subj'],$var['message'],$var['header'])) {
            $notice['ok'] = "��������� ������� ����������";
            }
            else {
            $notice['error'] = "��������� ������ - ���� ������� &quot;mail&quot;";
            }
          }
          else {
          $notice['error'] = "������ ��������� - ���������� ������� &quot;imap_binary&quot;";
          }
        }
        else {
        $notice['error'] = "������ ������� ��������� - �������� ����������";
        }
      }
      else {
      $notice['error'] = "����������, ��������� ������������ e-mail";
      }
    }
    else {
    $notice['error'] = "����������, ������� ".implode(", ",$notice['error']);
    }
  }
echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\r\n";
  if (empty($notice['ok'])) {
  echo "<form name=\"SM\" method=\"POST\" action=\"\">\r\n";
  }
  else {
  // ��� ������������� ������ "METHOD POST NOT ALLOWED" �������� "POST" �� "GET"
  echo "<form name=\"GT\" method=\"POST\" action=\"\">\r\n";
  }
# ��������� � ���������� / ������
  if (!empty($notice)) {
  echo "<tr><td align=\"center\">";
    if (!empty($notice['ok'])) {
    echo "<p><b><font color=\"#008000\">".$notice['ok']."!</font></b></p>";
    }
    elseif (!empty($notice['error'])) {
    echo "<p><b><font color=\"#800000\">".$notice['error']."!</font></b></p>";
    }
  echo "</td></tr>\r\n";
  }
# ����� �����
  if (empty($notice['ok'])) {
  echo "<tr><td align=\"center\">";
  require("form.inc");
  echo "</td></tr>\r\n";
  }
echo "<tr><td align=\"center\"><table border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td>";
# ������
  if (empty($notice['ok'])) {
  echo "<input type=\"submit\" name='go' value=\"���������\"></td>\r\n";
  echo "<td><input type=\"reset\" value=\"��������\">\r\n";
  }
  else {
  echo "<input type=\"submit\" value=\"".$config['url_name']."\">\r\n";
  }
echo "</td></tr></table></td></tr>\r\n";
  if (!empty($config['copyshow'])) {
  echo "<tr><td align=\"center\"><font style=\"font-size: 10px;\"><a style=\"text-decoration: none\" href=\"http://www.jpcars.com/scripts/phpsendmail.zip\">PHPSendMail</a> v.2.1.2 � 2000-".date("Y")." <a style=\"text-decoration: none\" href=\"http://www.jpcars.com\" target=\"_blank\">JPCars.com</a></font></td></tr>\r\n";
  }
echo "</form></table>\r\n";
# �����

?>
