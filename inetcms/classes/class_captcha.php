<?
session_start();

class Captcha {
  static $_width = 160;
  static $_height = 60;

  private static function get_fonts() {
    return array(
      'Ubuntu-B',
      'Ubuntu-BI',
      'Ubuntu-I',
      'Ubuntu-R',
    );
  }

  private static function get_font() {
    $fonts = self::get_fonts();
    return $fonts[rand() % count($fonts)] . '.ttf';
  }

  private static function get_angle() {
    return (rand() % 20)-10;
  }

  private static function get_font_size() {
    return (rand() % 20)+15;
  }

  private static function get_background_coords() {
    $num_x = (rand() % 4);
    $num_y = (rand() % 8);
    $x = $num_x * (self::$_width + 20);
    $y = $num_y * (self::$_height + 10);
    return array(
      'x' => $x,
      'y' => $y,
    );
  }

  public static function generate_captcha() {
    $config_font = self::get_font_size();
    $config_code_color  = "FF0000"; //real color will not be exactly as this code, but closest existing in image palete

    $img_path = CMS_ROOT . "/images/captcha-backgrounds.png";
    $noautomationcode = substr(
      hash_hmac(
       'md5',
       time(),
       get_secret_key()
      ), 5, 6);
    $noautomationcode_array = array();
    for ($i=0; $i < strlen($noautomationcode); $i++) {
        $noautomationcode_array[] = $noautomationcode[$i];
    }
    shuffle($noautomationcode_array);
    $noautomationcode = implode($noautomationcode_array);

    // set captcha to $_SESSION
    $_SESSION['captcha_code'] = $noautomationcode;

    $img = imagecreatetruecolor(self::$_width, self::$_height);
    $img_bg = imagecreatefrompng($img_path);
    $bg_xy = self::get_background_coords();
    imagecopy($img, $img_bg, 0, 0, $bg_xy['x'], $bg_xy['y'], self::$_width, self::$_height);
    
    $img_size = array(self::$_width, self::$_height);

    $fw = $config_font*0.8;
    $fh = $config_font;

    $x = floor(($img_size[0] - strlen($noautomationcode) * $fw ) / 2);
    $y = floor(($img_size[1] - $fh) / 2)+$fh; // middle of the code string will be in middle of the background image

    $color = imagecolorallocate($img,
                  hexdec(substr($config_code_color,1,2)),
                  hexdec(substr($config_code_color,3,2)), 
                  hexdec(substr($config_code_color,5,2))
                  );

    // Set the enviroment variable for GD
    putenv('GDFONTPATH=' . CMS_ROOT . '/images/');
    imagettftext ($img, $config_font, self::get_angle(), $x, $y, $color, getenv('GDFONTPATH') . self::get_font(), $noautomationcode);

    header("Content-type: image/jpeg");
    imagejpeg($img);
    exit();
  }

  public static function check_captcha() {
    extract(vars_constants());
    vars_post(array(
      'captcha_word' => $VAR_STRING,
    ));
    extract($GLOBALS);

    if (!$post['captcha_word'] || $post['captcha_word'] != $_SESSION['captcha_code']) {
        return false;
    } else {
        return true;
    }
  }
}
