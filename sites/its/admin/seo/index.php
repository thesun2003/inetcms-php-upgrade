<?
using::add_class('textfield');

$menu = new Menu();
$menu = $menu->find(array('id' => $_GET['id']))->next();

$title = new TextField('���������', 'title', $menu->get('title'));
$keywords = new TextField('��������', 'keyw', $menu->get('keyw'));
$description = new TextField('��������', 'descr', $menu->get('descr'));

$form  = $title->getAdminForm();
$form .= $keywords->getAdminForm();
$form .= $description->getAdminForm();
?>
<table width="780" align="center" style="margin-top:50px">
  <tr>
    <td>
      <h1><?=$menu->get('name')?></h1>
      <form id="seo_form" action="<?=MODULES_URL . '/core/?mode=JSON&context=seo&type=menu&id=' . $menu->get('id');?>" method="post"  onSubmit="ajax_form_submit('seo_form');return false;">
      <?=$form?>
      <input type='submit' name="update_form" value="��������" />
      </form>
    </td>
  </tr>
</table>