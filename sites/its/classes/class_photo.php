<?
using::add_class('user');
using::add_class("button");
using::add_class("jpeg");

define('PHOTO_COUNT', 5);

class Photo extends Entity {
    var $types = array('jpg', 'jpeg', 'gif');
    function Photo($info=false) {
        $this->Entity('photos');
        $this->form->addField('id');
        $this->form->addField('user_id');
        $this->form->addField('name');
        $this->form->addField('pos');

        if (!empty($info)) {
            $this->setInfo($info);
        }

        $this->ROOT = $_SERVER['DOCUMENT_ROOT'];
        $this->PHOTO_DIR = $this->ROOT.'/photos/';
        $this->addURL = '/?page=add_photo';
        $this->delURL = '/?page=delphoto';
        $this->movURL = '/?page=movephoto';
        $this->isAdmin = false;
    }

 function showNoPhoto() {
    return '<table border="0" cellspacing="0" cellpadding="2" align="left"><tr><td bgcolor="#EDEEF0"><img border="0" src="/inc/jpeg.php?id='.$this->get('id').'" border="0"></td></tr></table>';
 }

 function showAddPhoto($isAdmin) {
    $this->isAdmin = $isAdmin;
    if ($this->isAdmin) {
        $this->addURL = '?page=add_photo&id='.$_GET['id'];
        $this->delURL = '?page=delphoto';
        $this->movURL = '?page=movephoto';
    }
    $ret  = '<table border="0" cellspacing="0" cellpadding="0" align="left"><tr>';
    $ret .= '<td style="padding:16px;background-color:#eee" align="left">&nbsp;</td>';
    $ret .= '<td style="padding:10px"><a href="'.$this->addURL.'"><img border="0" src="/images/add_photo_big.gif" border="0"></a></td>';
    $ret .= '<td align="center" style="padding-right:50px;padding-left:50px;"><a class="myEditlink" href="'.$this->addURL.'">Добавить</a></td>';
    $ret .= '</tr></table>';
    return $ret;
 }


 function showMyPhoto($isAdmin) {
    $this->isAdmin = $isAdmin;
    if ($this->isAdmin) {
        return '<table border="0" cellspacing="0" cellpadding="2" align="left"><tr><td bgcolor="#EDEEF0"><img border="0" src="/inc/jpeg.php?id='.$this->get('id').'" border="0"></td></tr></table>';
    } else {
        return '<table border="0" cellspacing="0" cellpadding="2" align="left"><tr><td bgcolor="#EDEEF0"><a href="#nul" onclick="wopen(\'/inc/gallery.php?user_id='.$this->get('user_id').'&id='.$this->get('id').'\',\''.$this->get('id').'\',700,680,\'yes\',\'yes\',0);return false;"><img border="0" src="/inc/jpeg.php?id='.$this->get('id').'" alt="Нажмите, чтобы увеличить" title="Нажмите, чтобы увеличить" border="0"></a></td></tr></table>';
    }
 }

 function showAnket($user_id) {
    $user = new User();
    $find = $user->find(array('id' => $user_id));
    $user = $find->next();
    return '<table border="0" cellspacing="0" cellpadding="2" align="left"><tr><td bgcolor="#EDEEF0"><a href="'.$user->getMyLink().'"><img border="0" src="/inc/jpeg.php?id='.$this->get('id').'" alt="'.$user->get('username').", ".$user->getAge().'" title="'.$user->get('username').", ".$user->getAge().'" border="0"></a></td></tr></table>';
 }

 function showMyGallery() {
    return '<table border="0" cellspacing="0" cellpadding="5" align="left"><tr><td id="td_'.$this->get('id').'" bgcolor="#ffffff"><a href="#nul" onclick="showphoto(\''.$this->get('name').'\', \'' . $this->get('id') . '\');return false;"><img class="thumb" border="0" src="/inc/jpeg.php?id='.$this->get('id').'" alt="Нажмите, чтобы увеличить" title="Нажмите, чтобы увеличить" border="0"></a></td></tr></table>';
 }

 function getIDNext($str) {
     global $DB;
     $str++;
     $str = $str * PHOTO_COUNT;
     return $DB->getOne("SELECT id FROM photos WHERE user_id = " . $this->get('user_id') . " ORDER by pos limit $str, 1");
 }

 function getIDPrev($str, $user_id) {
     global $DB;
     $str--;
     $str = $str * PHOTO_COUNT;
     return $DB->getOne("SELECT id FROM photos WHERE user_id = " . $user_id . " ORDER by pos limit $str, 1");
 }

 function showGalleryNext() {
    if (!isset($_GET['str'])) {
        $str = 0;
    } else {
        $str = $_GET['str'];
    }
    $id = $this->getIDNext($str);
    $nextURL = "/inc/gallery.php?user_id=".$this->get('user_id')."&id=$id&str=" . ($str+1);
    return '<a href="'.$nextURL.'" class="galleryNext" alt="Далее" title="Далее">...</a>';
 }

 function showGalleryPrev($user_id) {
    if (!isset($_GET['str'])) {
        return false;
    } else {
        $str = $_GET['str'];
    }
    $id = $this->getIDPrev($str, $user_id);
    $nextURL = "/inc/gallery.php?user_id=".$user_id."&id=$id&str=" . ($str-1);
    return '<a href="'.$nextURL.'" class="galleryNext" alt="Предыдущие" title="Предыдущие">...</a>';
 }

 function showAlbum($str) {
    return '<table border="0" cellspacing="10" cellpadding="2" align="left"><tr><td><a href="#nul" onclick="wopen(\'/inc/gallery.php?user_id='.$this->get('user_id').'&id='.$this->get('id').'&str='.$str.'\',\''.$this->get('id').'\',700,680,\'yes\',\'yes\',0);return false;"><img class="thumbWhite" src="/inc/jpeg.php?id='.$this->get('id').'&size=album" alt="Нажмите, чтобы увеличить" title="Нажмите, чтобы увеличить"></a></td></tr></table>';
 }

 function showMyAlbum($str, $pos, $last, $prevID, $nextID, $isAdmin) {
    $this->isAdmin = $isAdmin;
    if ($this->isAdmin) {
        $this->addURL = '?page=add_photo&id='.$_GET['id'];
        $this->delURL = '?page=delphoto';
        $this->movURL = '?page=movephoto&id='.$_GET['id'];
    }
    $changePos = "";
    $first = "";
    $up = '<a href="'.$this->movURL.'&pid='.$this->get('id').'&nid='.$prevID.'&no_html=1"><img src="/images/greyup.gif" border="0"></a>';
    $dw = '<br /><br /><br /><a href="'.$this->movURL.'&pid='.$this->get('id').'&nid='.$nextID.'&no_html=1"><img src="/images/greydown.gif" border="0"></a>';

    if ($pos == 0 && $last == 1) {
        $up = $dw = '';
    }

    if ($pos == 0 && $last != 1) {
        $first = "class=\"first\"";
        $up = '';
        $dw = '<a href="'.$this->movURL.'&pid='.$this->get('id').'&nid='.$nextID.'&no_html=1"><img src="/images/greydown.gif" border="0"></a>';
    }
    if ($pos == $last-1 && $pos !=0) {
        $up = '<a href="'.$this->movURL.'&pid='.$this->get('id').'&nid='.$prevID.'&no_html=1"><img src="/images/greyup.gif" border="0"></a>';
        $dw = '';
    }
    $changePos = $up.$dw;
    $ret  = '<table '.$first.' border="0" cellspacing="0" cellpadding="0" align="left"><tr>';
    $ret .= '<td style="padding:10px;background-color:#eee" align="left">'.$changePos.'</td>';
    $ret .= '<td style="padding:10px">';
    if (!$this->isAdmin) {
        $ret .= '<a href="#nul" onclick="wopen(\'/inc/gallery.php?user_id='.$this->get('user_id').'&id='.$this->get('id').'&str='.$str.'\',\''.$this->get('id').'\',700,680,\'yes\',\'yes\',0);return false;"><img class="thumbWhite" src="/inc/jpeg.php?id='.$this->get('id').'&size=album" alt="Нажмите, чтобы увеличить" title="Нажмите, чтобы увеличить"></a>';
    } else {
        $ret .= '<img class="thumbWhite" src="/inc/jpeg.php?id='.$this->get('id').'&size=album">';
    }
    $rat .= '</td>';
    $ret .= '<td align="center" style="padding-right:50px;padding-left:50px;"><a class="myEditlink" href="#nul" onclick="ondel(\''.$this->delURL.'&pid='.$this->get('id').'&no_html=1\')">Удалить</a></td>';
    $ret .= '</tr></table>';
    return $ret;
 }

 function addForm($acts) {
     $this->act="add";    
     return $this->getform($acts);
 }

 function getform($acts)
 {
  $f = new _form($acts,"POST","multipart/form-data","box");
  $f->width="100%";
  $f->align="center";
  $f->style="";

  $val="";
  $dval="";
  $dop="";

  $c = new Cell("Файл","name","file",$dval,$val);
  $f->AddCell($c);

  $c = new Cell("","ok","hidden","ok","");
  $f->AddCell($c);
  $c = new Cell("","act","hidden",$this->act,"");
  $f->AddCell($c);
  $c = new Cell("","user_id","hidden",$this->get('user_id'),"");
  $f->AddCell($c);

  if ($this->act == 'change')
  {
   $c = new Cell("","id","hidden",$this->get('id'),"");
   $f->AddCell($c);
  }

  if ($this->act == 'add') $n_val='Добавить';
  if ($this->act == 'change') $n_val='Изменить';

  $c = new Cell("","","submit",$n_val,"");
  $f->AddSubmit($c);

  $f->Create();
  return $this->form = $f->result;
 }

 function upload() {
    $attr = array();
    if (!empty($_FILES)) {
        foreach ($_FILES as $key=>$image) {
            $ff=name2time($image['name']);
            if ($ff == "") $ff='';
            if (file_exists($this->PHOTO_DIR.$ff)&&($ff !=''))
            {
             $q=time('U');
             $dop=($q-(round($q/100000)-1)*100000);
             $ff=$dop.$ff;
            }
            if ($ff !='') {
                $photo = new JPEG($image['tmp_name']);
                $photo->Save($this->PHOTO_DIR.$ff);
            }
            $attr[$key] = $ff;
        }
    }
    return $attr;
 }

 function getPhotoCount() {
     global $DB;
     return $DB->getOne("SELECT count(*) FROM photos WHERE user_id = " . getUserID());
 }

 function add($attr) {
     $attr = $this->upload();
     if (!empty($attr['name'])) {
         $this->setInfo($attr);
         $this->save();
         $this->set('pos', $this->get('id'));
         $this->save();
         return true;
     } else {
         return false;
     }
 }

 function delete() {
    @unlink($this->PHOTO_DIR . $this->get('name'));
    $this->del();
 }

}

?>