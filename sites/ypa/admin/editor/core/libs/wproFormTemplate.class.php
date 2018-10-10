<?php
// class for generating form template objects
class wproFormTemplate {
	
	var $innerHTML = '';
	var $parameters = array();
	/*var $formEditable = true;*/
	var $action = '';
	var $method = 'post';
	var $name = '';
	var $formTagExtra = '';
	var $description = '';
	var $longDescription = '';
	var $dataBindings = array(); // form variable names
	var $thumbnail = '';
	
	function registerParamater ($name, $label, $options='', $longDescription='', $required=false) {
		// $options may be a default value string or an array to display a list box
		array_push($this->parameters, array('name'=>$name, 'description'=>$label, 'options'=>$options, 'longDescription'=>$longDescription, 'required'=>$required));
	}
	
}
?>