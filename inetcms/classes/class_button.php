<?

class button {
  function button() {
    # nothing to do
  }

  public static function get($button_config = array(), $url = '', $title = '') {
    $title = strip_tags($title);
    if($button_config) {
      list($config_image, $config_title) = $button_config;
      $alt = $config_title . $title;
      return '<a href="' . $url . '" title="' . $alt . '"><img src="' . $config_image . '" alt="' . $alt . '" title="' . $alt . '" border="0" align="middle"></a>';
    }
    return false;
  }

}


class admin_button extends button {
  static $buttons_config = array(
    'new_menu' => array('/admin/img/fldr.gif', '�������� '),
    'new_page' => array('/admin/img/new.gif', '�������� '),
    'new_action' => array('/admin/img/fldr_green.gif', '�������� '),
    'edit' => array('/admin/img/edit.gif', '�������� '),
    'edit_new' => array("/admin/img/edit_new.gif","(������������) �������� "),
    'del' => array("/admin/img/b_drop.png","������� "), 
    'seo' => array("/admin/img/icon_seo.png","��� ��������� "),
    'hide' => array("/admin/img/hide.jpg","������ "),
    'show' => array("/admin/img/show.jpg","�������� "),
    'arrow_up' => array('/admin/img/sort_asc.gif', "�����"),
    'arrow_down' => array('/admin/img/sort_desc.gif', "����"),
    'new_admin' => array('/admin/img/users_icon.png', "�������� "),
  );
  function __construct() {
    #nothing to do
  }

  public static function get($button_name = 'none', $url = '', $title = '') {
    if(isset(self::$buttons_config[$button_name])) {
      return button::get(self::$buttons_config[$button_name], $url, $title);
    }
  }
}

/*
class text_button extends button {
  function text_button($text = "", $title = "") {
    $this->title = $title;
    $this->text  = $text;
  }

  function show($url = "", $title = "", $show_it = true) {
    $this->url = $url;
    $this->href = "<a href=\"" . $this->url . "\" title=\"" . $this->title . $title . "\">" . $this->text . "</a>";
    if ($show_it) echo $this->href; else return $this->href;
  }
}

*/