<?

class news_button extends admin_button {
  static $buttons_config = array(
    'new_news' => array('/admin/modules/news/images/add_news.png', "�������� "),
  );

  public static function get($button_name = 'none', $url = '', $title = '') {
    if(isset(self::$buttons_config[$button_name])) {
      return button::get(self::$buttons_config[$button_name], $url, $title);
    }
  }

}