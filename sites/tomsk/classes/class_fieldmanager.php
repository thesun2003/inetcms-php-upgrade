<?
using::add_class('field');
define("GLOBAL_ERROR", "_global");

/* Used to log validation errors. */
define('EMPTY_FIELD_PREFIX', 'empty_');
class FieldManager{
    var $_fields;
    function FieldManager(){
        $this->_fields  = array();
    }

    function addError($fieldName, $error){
        $this->addField($fieldName);
        $this->_fields[$fieldName]->addError($error);
    }

    function hasError($fieldName){
        if ($this->isExists($fieldName)) {
            return $this->_fields[$fieldName]->hasError();
        }
        return false;
    }

    function getError($fieldName){
        if ($this->isExists($fieldName)) {
            return $this->_fields[$fieldName]->getError();
        }
        return false;
    }

    public function getErrors() {
  		$errors = array();

  		foreach($this->_fields as $fieldName => $field) {
  			if($field->hasError()) {
  				$errors[$fieldName] = $field->getError();
  			}
  		}

  		return $errors;
  	}

    function addField($fieldName){
        if (!$this->isExists($fieldName)) {
            $this->_fields[$fieldName]  = new Field();
        }
    }

    function set($fieldName, $value){
        $this->addField($fieldName);
        $this->_fields[$fieldName]->set($value);
    }

    function get($fieldName, $htmlencode=false){
        if ($this->isExists($fieldName)) {
            $res = stripslashes($this->_fields[$fieldName]->get());
            if ($htmlencode) {
                return htmlspecialchars($res);
            } else {
                return $res;
            }
        }
        return false;
    }

    function del($fieldName){
        unset ($this->_fields[$fieldName]);
    }

    function getAll(){
        $res = array();
        foreach($this->_fields as $name => $field) {
            if (!$field->shouldStore() || $name == GLOBAL_ERROR) continue;
            $res[$name] = $field->get();
        }
        return $res;
    }

    function setRequired($fieldName, $value = true){
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setRequired($value);
    }

    function setStored($fieldName, $value = true){
        $this->addField($fieldName);
        $this->_fields[$fieldName]->setStored($value);
    }

    function isExists($fieldName){
        return isset($this->_fields[$fieldName]);
    }

    function isValid($fields = false){
        $res = true;
        foreach(array_keys($this->_fields) as $fName) {
            if ((!$fields || in_array($fName, $fields)) && !$this->_fields[$fName]->isValid()) {
                // For now we assume Field checks only for not emptiness
                $this->log(EMPTY_FIELD_PREFIX . $fName, $this->get($fName));
                $res = false;
            }
        }
        return $res;
    }

    /**
     * Logs any validation error.
     * To turn the logging programmer has to explicitly set validationLogger object field.
     *
     * @param string Error key.
     * @param string Erroneous input value.
     */
    function log($errorKey, $errorValue) {
        if (isset($this->validationLogger)) {
            $this->validationLogger->log($errorKey, $errorValue);
        }
    }
}
?>
