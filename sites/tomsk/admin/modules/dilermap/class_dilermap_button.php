<?

class dilermap_button extends admin_button {
  static $buttons_config = array(
    'new_dilermap' => array('/admin/modules/dilermap/images/add_dilermap.png', "Добавить "),
  );

  public static function get($button_name = 'none', $url = '', $title = '') {
    if(isset(self::$buttons_config[$button_name])) {
      return button::get(self::$buttons_config[$button_name], $url, $title);
    }
  }

}