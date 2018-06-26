<?
define('ERR_SEPARATOR', "<br />");
class Field{
    var $_error;
    var $_value;
    var $_isRequired;
    var $_isStored;

    function Field(){
        $this->_error = $this->_value   = '';
        $this->_isRequired = false;
        $this->_isStored = true; // Temporary fields won't be saved
    }

    function addError($error){
        if (!empty($this->_error)) $this->_error    .= ERR_SEPARATOR;
        $this->_error .= $error;
    }

    function hasError(){
        return !empty($this->_error);
    }

    function getError(){
        return $this->_error;
    }

    function set($value){
        $this->_value = trim($value);
    }

    function get(){
        return $this->_value;
    }

    function setRequired($req){
        $this->_isRequired = $req;
    }

    function setStored($req){
        $this->_isStored = $req;
    }

    function isValid(){
        global $LNG;
        if ($this->_value === '' && $this->_isRequired) {
            $this->addError($LNG['err_field_empty']);
            return false;
        }
        return !$this->hasError();
    }

    function shouldStore(){
        return $this->_isStored;
    }
}
?>