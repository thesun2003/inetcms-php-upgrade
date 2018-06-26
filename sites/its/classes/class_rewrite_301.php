<?

class Rewrite_301 extends Entity {
  function Rewrite_301($info=false){
    $this->Entity(getTablePrefix() . 'rewrite_301');
    $this->form->addField('id');
    $this->form->setRequired('template_url');
    $this->form->setRequired('serialized_template_url');
    $this->form->setRequired('rewrite_url');
    $this->form->set('date_last_requested', strftime(MYSQL_TIME));

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  public static function parse_url_params($params = '') {
    $url_params = array();
    $params_array = explode('/', $params);
    foreach($params_array as $key => $param) {
      if(!empty($param)) {
        $url_params[] = $param;
      }
    }
    return $url_params;
  }
  
  public static function casesensitive_uksort($a,$b) { 
      return $a < $b;
  } 

  public static function get_rewrite_keys() {
    // just for time
    return array('cat_id', 'item_id');
  }
  
  public static function parse_get_params($params = '') {
    $rewrite_keys = self::get_rewrite_keys();

    $get_params = array();
    $other_get_params = array();
    $params_array = explode('&', $params);
    foreach($params_array as $key => $param) {
      if(!empty($param)) {
        $get_pair = explode('=', $param);
        if($get_pair && count($get_pair) == 2) {
          if(in_array($get_pair[0], $rewrite_keys)) {
            $get_params[$get_pair[0]] = $get_pair[1];
          } else {
            $other_get_params[$get_pair[0]] = $get_pair[1];
          }
        }
      }
    }
    uksort($get_params, 'Rewrite_301::casesensitive_uksort');
    return array(
      'get' => $get_params,
      'other_get' => $other_get_params,
    );
  }
  
  public static function get_url_params($params = '') {
    $parsed_params = @parse_url($params); // Removed the E_WARNING that was emitted when URL parsing failed.
    $result_params = array(
      'url' => array(),
      'get' => array(),
      'other_get' => array(),
    );
    if(!empty($parsed_params['path'])) {
      $result_params['url'] = self::parse_url_params($parsed_params['path']);
    }
    if(!empty($parsed_params['query'])) {
      $result_params = array_merge($result_params, self::parse_get_params($parsed_params['query']));
    }  
    return $result_params;
  }
  
  public static function diff_url_params($request = '', $template = '') {
    $request_params = self::get_url_params($request);
    $template_params = self::get_url_params($template);
    return serialize($request_params) == serialize($template_params);
  }
  
  public static function static_run($url) {
    //echo "we should rewrite to " . $url;
    header("HTTP/1.1 301 Moved Permanently");
    header('Location: ' . $url);
    header("Connection: close");
    exit();
  }
  
  public function run() {
    $this->set('date_last_requested', strftime(MYSQL_TIME));
    $this->save();
    self::static_run($this->get('rewrite_url', false));
  }
  
  public function save() {
    $is_template_url_modified = true;
    if($this->get('id')) {
      $rewrite_old = new self();
      $rewrite_old = $rewrite_old->find(array('id' => $this->get('id')))->next();
      if($this->get('template_url') == $rewrite_old->get('template_url')) {
        $is_template_url_modified = false;
      }
    }
    if($is_template_url_modified) {
      $this->set('serialized_template_url', serialize(self::get_url_params($this->get('template_url', false))));
    }
    parent::save();
  }

  private function add_other_get($other_get_array = array()) {
    if($other_get_array) {
      $new_rewrite_url = $this->get('rewrite_url', false);
      $other_get = http_build_query($other_get_array);
      $separator = '&';
      if(strpos($new_rewrite_url, '?') === false) {
        $separator = '?';
      }
      $this->set('rewrite_url', $new_rewrite_url . $separator . $other_get);
    }
  }
  
  public static function get_rewrite($request = '') {
    $url_params = self::get_url_params($request);
    $other_get = $url_params['other_get'];
    unset($url_params['other_get']);

    $serialized_url_params = serialize($url_params);
    $rewrite = new self();
    $rewrite = $rewrite->find(array('serialized_template_url' => $serialized_url_params))->next();

    if($rewrite) {
      $rewrite->add_other_get($other_get);
    }
    return $rewrite;
  }
  
  public static function process_rewrites() {
    if($rewrite = self::get_rewrite($_SERVER['REQUEST_URI'])) {
      $rewrite->run();
    }
  }
}