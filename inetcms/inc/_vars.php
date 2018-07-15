<?php

function vars_constants() {
  return 
    array(
      'VAR_EXISTS' => 0x001,
      'VAR_INT'    => 0x002,
      'VAR_STRING' => 0x004,
      'VAR_BOOL'   => 0x008,
      'VAR_TOGGLE' => 0x010,
      'VAR_ARRAY'  => 0x020
    );
}


/*********************************************
 * Looks for the specified variables in GET.  If a variable is not found, returns a suitable
 * default value.
 */
function vars_get($vars) {
  return _vars_handle($_GET, $vars, $GLOBALS['get']);
}


/*********************************************
 * Looks for the specified variables in POST.  If a variable is not found, returns a suitable
 * default value.
 */
function vars_post($vars) {
  return _vars_handle($_POST, $vars, $GLOBALS['post']);
}


/*********************************************
 * If a POST occurred, looks for the specified variables in POST; if a variable is not found,
 * returns a suitable default value.  If a POST did not occur, looks for the information in GET.
 */
function vars_request($vars) {
  if($_POST) {
    return _vars_handle($_POST, $vars, $GLOBALS['req']);
  } else {
    return _vars_handle($_GET, $vars, $GLOBALS['req']);
  }
}

/*********************************************
 * Looks for specific variables in both GET and POST.  If a variable is not found in 
 * either, returns a suitable default value for that variable.
 */
function vars_request2($vars) {
	$req = $_POST + $_GET; // we do not want to use $_REQUEST because it includes cookies
  return _vars_handle($req, $vars, $GLOBALS['req']);
}

/*********************************************
 * Gets a request parameter. If the parameter does not exist, then the default value is returned.
 * This should only be used for request parameters that control UI and therefore, have insignificant consequences if hacked.
 * For all request parameters that end up in the database the functions in this file should not be used. Instead use the Form class.
 * @param string $name the name of the parameter
 * @param string $default (optional) the default value for this parameter if it does not exist
 * @param array @params (optional) which parameter bundle to use. eg: $_GET, $_POST, $_REQUEST. defaults to $_REQUEST. 
 */
function vars_param($name, $default='', $params=false) {
  if ($params === false) $params = $_REQUEST;
  return (empty($params[$name]) ? $default : $params[$name]);
}

/*********************************************
 * Looks for the specified variables first in POST, then GET.  If a variable is not found in 
 * either, returns a suitable default value for that variable.
 */
function vars_request_all($vars) {
  // First load the scope with information from GET and/or default values.
  $status = _vars_handle($_GET, $vars, $GLOBALS['req']);

  // Next overwrite variables in the scope with any information found in POST.
  if($_POST) {
    $status = $status && _vars_handle($_POST, $vars, $GLOBALS['req']);
  }
  return $status;
}


/*********************************************
 * Looks for the specified cookies.  For cookies that don't exist, returns a suitable default value.
 */
function vars_cookie($vars) {
  return _vars_handle($_COOKIE, $vars, $GLOBALS['cookie']);
}


/*********************************************
 * Looks for the specified variables in the source; if found, the variables are type checked
 * and set in the specified scope (any existing values will be overwritten).  If a variable
 * is not in the source, and is not already defined in the scope, the variable will be set in
 * the scope with the user-specified default value (otherwise false).
 *
 * @param   array  $src              The source array in which to look for variables.
 * @param   array  $vars             A list of variable names, types, and optional default values.
 *                                   Each element in the array can have the format "name => type"
 *                                   or "name => array(type, default)".  Note that the default
 *                                   value does not apply for the $VAR_EXISTS or $VAR_BOOL types.
 * @param   array  $scope            The destination array in which to store type-checked variables
 *                                   and/or default values.
 */
function _vars_handle($src, $vars, &$scope=array()) {

  extract(vars_constants());
  $VAR_NULL_OK = $VAR_INT;

  if (!is_array($vars)) {
    _vars_error('PARAM: invalid valist provided');
  }

  foreach($vars as $var=>$spec) {
    $type = (is_array($spec) && count($spec) > 0) ? $spec[0] : $spec;
    $default = ($has_default = (is_array($spec) && count($spec) > 1)) ? $spec[1] : false;

    if(isset($src[$var])) {
      $ref = $src[$var];
      
      if($type & $VAR_NULL_OK) {
        if ($ref == '') {
          $scope[$var] = ($has_default ? $default : null);
          continue;
        }
      }
      if($type & $VAR_INT) {
        if(ctype_digit($ref) || ((substr($ref, 0, 1) == '-') && (ctype_digit(substr($ref, 1))))) {
          $scope[$var] = (int)$ref;
        } else {
          _vars_error("_vars_handle: invalid content for $var, expected INT: $ref");
        }

      } elseif($type & $VAR_EXISTS) { 
        $scope[$var] = true;

      } elseif($type & $VAR_STRING) {
        $scope[$var] = (string)$ref;

      } elseif($type & $VAR_BOOL) {
        $trimmed_value = trim($ref);
        if (empty($trimmed_value) || (strtolower($trimmed_value) === 'false')) {
          $scope[$var] = false;
        }
        else {
          $scope[$var] = true;
        }

      } elseif($type & $VAR_TOGGLE) {
        $trimmed_value = trim($ref);
        if ((strtolower($trimmed_value) === 'n') || 
            (strtolower($trimmed_value) === 'no') || 
            (strtolower($trimmed_value) === 'off') || 
            (strtolower($trimmed_value) === '0')) {
          $scope[$var] = 'off';
        }
        else if ((strtolower($trimmed_value) === 'y') || 
                 (strtolower($trimmed_value) === 'yes') || 
                 (strtolower($trimmed_value) === 'on') || 
                 (strtolower($trimmed_value) === '1')) {
          $scope[$var] = 'on';
        }
        else {
          $scope[$var] = false;
        }
      } elseif($type & $VAR_ARRAY) {
        if(is_array($ref)) {
          $scope[$var] = $ref;
        } elseif($ref == -1) {
          $scope[$var] = array();
        } else {
          _vars_error("_vars_handle: invalid content for $var, expected ARRAY: $ref");
        }
      } elseif ($type == 0) {
        _vars_error("_vars_handle: invalid type for $var: $type");
      }
    } elseif($type & $VAR_EXISTS) {
      $scope[$var] = false;
    } else if($type & $VAR_BOOL) {
      $scope[$var] = false;
    } else if (!isset($scope[$var])) {
      $scope[$var] = $default;
    }
  }

  return true;
}


/*********************************************
 * If a variable failed a type check, throw an error.
 */  
function _vars_error($str) {
  debug_log($str);
  redirect_home();
}

/*********************************************
 * Checks to see if a given bag of arguments has a specific list of arguments.
 *
 * @param   array  $vars             The bag of arguments to be checked on.
 * @param   array  $required_vars    A list of key names, to be checked for.
 */
 function vars_check($vars, $required_vars) {
  foreach ($required_vars as $k) {
    if (!isset($vars[$k])) {
      log_warning('missing argument: ' . $k);
      return false;
    }
  }
  
  return true;
 }
