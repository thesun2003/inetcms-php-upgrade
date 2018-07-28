<?

class news_button extends admin_button {
  static $buttons_config = array(
    'new_news' => array('/admin/modules/articles/images/add_news.png', "Добавить "),
  );

  public static function get($button_name = 'none', $url = '', $title = '') {
    if(isset(self::$buttons_config[$button_name])) {
      return button::get(self::$buttons_config[$button_name], $url, $title);
    }
  }

}