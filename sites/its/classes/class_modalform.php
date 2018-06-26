<?php

class ModalForm {
  const ID = "modal_form";
    function ModalForm() {
      echo using::add_js_file('modalform.js');
      $this->form = "\n" . '
<div id="' . self::ID . '">
  <form id="' . self::ID . '_form" action="" method="post">
    <table align="center" border="0" width="100%" height="100%">
      <tr>
        <td colspan="2" id="' . self::ID . '_content" height="100%"><!-- Modal Form Dialog --></td>
      </tr>
      <tr>
        <td align="center"><input type="button" value="Отменить" onclick="javascript:hideModalForm()"></td>
        <td align="center"><input id="' . self::ID . '_submit" type="submit" value="Добавить"></td>
      </tr>
    </table>
  </form>
</div>';
      $this->form_x = '<div id="' . self::ID . '_x"></div>';
    }

    function show() {
        echo $this->form;
        echo $this->form_x;
        echo "\n<script type=\"text/javascript\">initModalForm('" . self::ID . "');modalFormX_init('" . self::ID . "_x');</script>";
    }

    function getLink($form_name) {
      return "javascript:showModalForm('" . $form_name . "');";
    }

    function getEditLink($form_name, $prefix, $ids) {
      return "javascript:showEditModalForm('" . $form_name . "', '" . $prefix . "', " . All::Array2JSArray($ids) . ");";
    }

    function showLink($form_name) {
      echo $this->getLink($form_name);
    }
// ----------- middle style ------------------
    function getEditLinkX($prefix, $action, $id) {
      return "javascript:showEditModalFormX('" . $prefix . "', '" . $action . "', ".$id.");";
    }
// ----------- new style ------------------
    public static function getLinkX($prefix, $action, $id) {
      return "javascript:modalFormX_show('" . $prefix . "', '" . $action . "', '".$id."');";
    }
    public static function get_template() {
      return SimplePage::process_template_file(
        MODULES . '/core',
        '/modalformx/modalform',
        array(
          'self_ID' => self::ID,
          'ajax_loading' => Ajax_Loader::run(ADMIN_URL . '/img/upload.gif'),
        )
      );
    }
}
?>