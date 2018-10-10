<?php
#  Это тестовый файл для проверки работоспособности всех необходимых функции. Измените значение
#  переменной $mail ниже на свой электронный адрес, а затем загрузите этот файл к себе на сервер
#  в любое место, где разрешен запуск скриптов PHP. Установите права на файл 644. Права папки также
#  должны разрешать запуск скриптов (обычно 711). Запустите скрипт и проверьте указанный ниже e-mail.
#  Если сообщение не получено, то по каким-то причинам вы не можете отправлять почту, и не имеет
#  смысла устанавливать основной скрипт. Если же при запуске вы видите ошибки, попробуйте их про-
#  анализировать и (или) спросить службу поддержки хостинга о том, что они означают. В случае,
#  если они сошлются ни ошибку в скрипте, тогда свяжитесь со мной приложив описание ошибок.

# Отредактируйте значение переменной $mail:
$mail = "someone@site.com";

# Ниже ничего менять не нужно
error_reporting (E_ALL);
  function go_exit ($message,$color) {
  echo "<html><head><meta http-equiv=\"content-language\" content=\"ru\">\r\n";
  echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\">\r\n";
  echo "<title>Проверка работоспостобности скрипта PHP SendMail</title>\r\n";
  echo "<style type=\"text/css\">{ }<!-- p { font-family: Verdana, Arial } --></style></head><body>\r\n";
  echo "<center><p><b><font color=\"#".$color."\">".$message."!</font></b></p></center></body></head></html>\r\n";
  exit();
  }
  if (function_exists("imap_binary")) {
  # Преобразуем в формат base64, а также добавляем слеши перед символами " \ ( )
    function mail_convert($str) {
    $str = trim(imap_binary(addcslashes($str, "\"!@\\!@(!@)")));
    return $str;
    }
  $subject = "=?Windows-1251?B?".trim(imap_binary("Поздравляем!"))."?=";
  $headers  = "From: =?Windows-1251?B?".mail_convert("Скрипт")."?= <postmaster@adultsingles.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Transfer-Encoding: 8bit\r\n";
  $headers .= "Content-Type: text/plain; charset=\"Windows-1251\"\r\n";
  $headers .= "X-Mailer: PHP v.".phpversion();
  $message  = "Поздравляем!\n\nЕсли вы видите только это сообщение, без ошибок и непонятных надписей в теле письма и";
  $message .= " заголовках, то можете приступать к настройке основного скрипта.";
    if (mail("=?Windows-1251?B?".mail_convert("Вебмастеру")."?= <".$mail.">",$subject,$message,$headers)) {
    go_exit ("Тестовое сообщение успешно отправлено - проверьте e-mail ".$mail,"009900");
    }
    else {
    go_exit ("Отказ функции &quot;mail&quot; - спросите у администратора хостинга об особенностях ее работы","CC0000");
    }
  }
  else {
  go_exit ("Недоступна функция &quot;imap_binary&quot; - обратитесь к администратору хостинга","CC0000");
  }
?>
