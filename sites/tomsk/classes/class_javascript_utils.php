<?
class JavascriptUtils {
  
  /**
   * Turns a php value into a JSON string for use with javascript.
   * The $value must be a primitive php type (like string, int, boolean, float), or it can be an array with primitive keys and values.
   * Arrays may also be nested.
   * example:
   * <?
   * $value = array('jan'=>1000, 'feb'=>array(50, 100), 'mar'=>200);
   * ?>
   * <SCRIPT> 
   *    var foo=<?=JavascriptUtils::json_encode($value)?>;
   *    alert(foo);
   * </SCRIPT> 
   */
  static function json_encode($value) {
    $result = '';
    if (is_string($value)) {
      $result = '"' . self::escape_string($value) . '"';
    } elseif (is_bool($value)) {
      $result =  $value ? 'true' : 'false';
    } elseif (is_int($value) || is_float($value)) {
      $result = $value;
    } elseif (is_array($value)) {
      $result = array();
      foreach($value as $key=>$value) {
        // we can't create a javascript object with negative numbers as keys, so use the string form of the key in this case: (this was happening when we json encoded the -1 offer category (which is the "Most Popular" category))
        if (is_int($key) && ($key < 0)) $key = (string) $key;
        $key = self::json_encode($key);
        $value = self::json_encode($value);
        $result[] = "$key:$value";
      }
      $result = '{'. implode(',', $result) . '}';

    } elseif (is_null($value)) {
      $result = 'null';
    } else {
      throw new Exception('unknown type:'.$value);
    }
    return $result;
  }

  /**
   * Escapes a php string so that it can be used as a javascript string.
   * Single/double-quotes, new-line, back-slash characters are all escaped.
   * Note that this function does not add quotes around the string that is returned; you would have to wrap the 
   * returned string with single-quotes or double-quotes to make it a valid javascript string.
   */
  static function escape_string($string) {
    // we must replace '\' before we do the other replacements because otherwise, the '\' will be
    // double escaped:
    $from = array('\\', "\r", "\n", '"', "'");
    $to = array('\\\\', '\\r', '\\n', '\"', "\\'");
    $string = str_replace($from, $to, $string);
    return $string;
  }
  
}
