<?php

global $use;

class TextField {
  function TextField($field_title, $field_name, $content){
    $this->field_title = $field_title;
    $this->field_name = $field_name;
    $this->content = $content;
  }

  function getAdminForm() {
    $form = array();
    $form[] = '<table width="100%">';
    $form[] = ' <tr>';
    $form[] = '  <td width="100%">';
    $form[] = '    ' .$this->field_title. ': <input type="text" id="'.$this->field_name.'" name="'.$this->field_name.'" value="'.$this->content.'" style="width:500px">';
    $form[] = '  </td>';
    $form[] = ' </tr>';
    $form[] = '</table>';
    return implode("\n", $form);
  }
}