<?

class All{
  public static function Array2JSArray($array) {
    if (!is_array($array)) {
      return '{}';
    } else {
      $new_array = array();

      foreach ($array as $key => $value) {
        $new_array[] = '\'' . $key . '\': \'' . $value . '\'';
      }
      return '{' . implode(', ', $new_array) . '}';
    }
  }

  public static function urlReplace($change, $delete = false, $return_only_get = false, $custom_url = false) {
      $params_arr = array();
      $url = '';
      if ($custom_url) {
        $url_params = parse_url($custom_url);
        parse_str($url_params['query'], $query_array);
        $url = $url_params['path'];
      } else {
        $query_array = $_GET;
        $url = ($return_only_get) ? '' : $_SERVER['PHP_SELF'];
      }
      foreach(array_merge($query_array, $change) as $param => $value) {
          if(is_array($delete) && in_array($param, $delete)) {
              continue;
          }            
          if(is_array($value)) {
              foreach($value as $v) {
                  $params_arr[] = $param.'[]='.urlencode($v);
              }
          } else {
              $params_arr[] = $param.'='.urlencode($value);
          }
      }
      if($params_arr) {
          $url .= '?';
          $url .= implode('&', $params_arr);
      }
      return $url;
  }

  public static function iconv_array(&$array, $from_charset = 'UTF-8', $to_charset = 'windows-1251') {
    foreach($array as &$value) {
      $value = iconv($from_charset, $to_charset, $value); 
    }
    return $array;
  }
  
  public static function get_limit($items_on_page = 10, &$pn, $use_limit_word) {
    $limit = '';
    if(!empty($_GET['pn']) && is_numeric($_GET['pn'])) {
      $pn = $_GET['pn'];
    } else {
      $pn = 1;
    }
    if((!empty($_GET['pn']) && $_GET['pn']!='all') || (!isset($_GET['pn'])) || (!empty($_GET['showall']) && !empty($_GET['pn']) && $_GET['pn']=='all')) {
      $from_limit = ($pn-1)*$items_on_page;    
      if($use_limit_word) {
        $limit = ' LIMIT ' . $from_limit . ', '. $items_on_page;
      } else {
        $limit = $from_limit . ', '. $items_on_page;
      }
    }
    if(empty($_GET['showall']) && !empty($_GET['pn']) && $_GET['pn']=='all') {
      $pn = 'all';
    }
    return $limit;
  }
  
  public static function get_pages($current_page, $on_page, $total, $show_all = true, $url = false) {
    $result = '';
    $pages_count = ceil($total / $on_page);
    for ($i = 1; $i <= $pages_count; $i++) {
      $result .= '<a class="' . ($i == $current_page ? 'current_' : '') . 'num_pages" href="'.All::urlReplace(array('pn' => $i), false, true, $url).'">&nbsp;' . $i . '&nbsp;</a>   ';
    }
    if($show_all) {
      $result .= '<a class="' . ($current_page == 'all' ? 'current_' : '') . 'num_pages" href="'.All::urlReplace(array('pn' => 'all'), false, true, $url).'">&nbsp;Все&nbsp;</a>';
    }
    return $result;
  }

  public static function filter_array(Array $array, Array $filter) {
    foreach($array as $key => $value) {
      if(!in_array($key, $filter)) {
        unset($array[$key]);
      }
    }
    return $array;
  }
  
}

?>
