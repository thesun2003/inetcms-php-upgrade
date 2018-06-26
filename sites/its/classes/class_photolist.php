<?
define('ALBUM_COUNT', 10);
define('NO_FOTO_ID', -1);

using::add_class("photo");

class PhotoList {
    function PhotoList($user_id) {
        global $DB;
        $this->ROOT = $_SERVER['DOCUMENT_ROOT'];
        $this->PHOTO_DIR = $this->ROOT.'/photos/';
        $this->user_id = $user_id;
        $this->photoCount = 0;
        $this->isAdmin = false;
    }

    function setAnkets() {
        global $DB;
        $this->photo_id = $DB->getOne("SELECT id FROM photos WHERE user_id = " . $this->user_id . " ORDER by pos limit 1");
    }

    function del() {
        global $DB;
        $this->photo_id = $DB->getAll("SELECT id FROM photos WHERE user_id = " . $this->user_id);
        foreach ($this->photo_id as $photo_id) {
            $photo = new Photo();
            $find = $photo->find(array('id'=>$photo_id['id']));
            $photo = $find->next();
            $photo->delete();
        }
    }

    function setMyMessage() {
        $this->setAnkets();
        $photo_id = $this->photo_id;
        $this->photo_id = array();
        $this->photo_id[0]['id'] = ($photo_id?$photo_id:NO_FOTO_ID);
    }

    function setMyPage() {
        global $DB;
        $this->photo_id = $DB->getAll("SELECT id FROM photos WHERE user_id = " . $this->user_id . " ORDER by pos limit 3");
        if (!$this->photo_id) {
            $this->photo_id['id'] = NO_FOTO_ID;
        }
    }

    function setMyAlbum() {
        global $DB;
        $this->photo_id = $DB->getAll("SELECT id FROM photos WHERE user_id = " . $this->user_id . " ORDER by pos");
    }

    function setMyAdmin($id) {
        $this->isAdmin = true;
        $this->setMyAlbum();
    }

    function setGallery($str = 0) {
        global $DB;
        $str = $str * PHOTO_COUNT;
        $this->photo_id = $DB->getAll("SELECT id FROM photos WHERE user_id = " . $this->user_id . "  ORDER by pos limit $str, " . PHOTO_COUNT);
        $this->photoCount = $DB->getOne("SELECT count(id) FROM photos WHERE user_id = " . $this->user_id);
        $this->photoCount -= $str;
    }

    function prepareToShow() {
        $return = '';
        $return.= "<table cellspacing='2'><tr>";
        foreach ($this->photo_id as $photo_id) {
            if (!is_array($photo_id)) {
                $tmp = $photo_id;
                $photo_id = array();
                $photo_id['id'] = $tmp;
            }
            $photo = new Photo();
            $find = $photo->find(array('id' => $photo_id['id']));
            $photo = $find->next();
            if ($photo->get('id') == -1) {
                $return.= "<td>".$photo->showNoPhoto()."</td>";                
            } else {
                $return.= "<td>".$photo->showMyPhoto($this->isAdmin)."</td>";
            }
        }
        $return.= "</tr></table>";
        return $return;
    }


    function show() {
/*
        echo "<table cellspacing='2'><tr>";
        foreach ($this->photo_id as $photo_id) {
            if (!is_array($photo_id)) {
                $tmp = $photo_id;
                $photo_id = array();
                $photo_id['id'] = $tmp;
            }
            $photo = new Photo();
            $find = $photo->find(array('id' => $photo_id['id']));
            $photo = $find->next();
            if ($photo->get('id') == -1) {
                echo "<td>".$photo->showNoPhoto()."</td>";                
            } else {
                echo "<td>".$photo->showMyPhoto($this->isAdmin)."</td>";
            }
        }
        echo "</tr></table>";
*/
    echo $this->prepareToShow();
    }

    function showAnkets() {
        if (empty($this->photo_id)) {
            $this->photo_id = NO_FOTO_ID;
        }
        $photo = new Photo();
        $find = $photo->find(array('id' => $this->photo_id));
        $photo = $find->next();
        echo "<table cellspacing='2'><tr>";
        echo "<td>".$photo->showAnket($this->user_id)."</td>";
        echo "</tr></table>";
    }

    function showGallery() {
        echo "<table cellspacing='2' align='center'><tr>";
        $countPhotoShow = count($this->photo_id);
        $photo = new Photo();
        if (!empty($_GET['str']) && $_GET['str'] > 0) {
            echo "<td>".$photo->showGalleryPrev($this->user_id)."</td>";
        }
        foreach ($this->photo_id as $photo_id) {
            $find = $photo->find(array('id' => $photo_id['id']));
            $photo = $find->next();
            echo "<td>".$photo->showMyGallery()."</td>";
        }
        if ($this->photoCount > $countPhotoShow) {
            echo "<td>".$photo->showGalleryNext()."</td>";
        }
        echo "</tr></table>";
    }

    function showAlbum() {
        echo "<table cellspacing='2' align='center' width='95%'>";
        $photoCount = count($this->photo_id);
        $max_col = PHOTO_COUNT;
        $i = 0;
        $str = 0;
        foreach ($this->photo_id as $photo_id) {
            $photo = new Photo();
            $find = $photo->find(array('id' => $photo_id['id']));
            $photo = $find->next();
            if ($i % $max_col == 0) echo "<tr>";
            echo "<td align='center'>".$photo->showAlbum($str)."</td>";
            if ($i % $max_col == $max_col-1) {
                $str++;
                echo "</tr>";
            }
            $i++;
        }
        if ($i % $max_col != 0) echo "</tr>";
        echo "</table>";
    }

    function showMyAlbum() {
        echo "<table cellspacing='2' align='center' width='95%'>";
        $photoCount = count($this->photo_id);
        $max_col = 2;
        $i = 0;
        $str = 0;
        $last = count($this->photo_id);
        $prev = 0;
        $next = 0;
        $this->photo_id[] = array('id' => 'add_photo');
        foreach ($this->photo_id as $photo_id) {

            if ($i>0 && $this->photo_id[$i]['id'] != 'add_photo') {
                $photo = new Photo();
                $find = $photo->find(array('id' => $this->photo_id[$i-1]['id']));
                $photo = $find->next();
                $prev = $photo->get('id');
            }

            if ($i < $last-1 && $this->photo_id[$i+1]['id'] != 'add_photo') {
                $photo = new Photo();
                $find = $photo->find(array('id' => $this->photo_id[$i+1]['id']));
                $photo = $find->next();
                $next = $photo->get('id');
            }

            $photo = new Photo();
            if ($photo_id['id'] != 'add_photo') {
                $find = $photo->find(array('id' => $photo_id['id']));
                $photo = $find->next();
            }

            if ($i % $max_col == 0) echo "<tr>";
            echo "<td align='center' width='50%'>";
            if ($photo_id['id'] == 'add_photo') {
                echo $photo->showAddPhoto($this->isAdmin);
            } else {
                echo $photo->showMyAlbum($str, $i, $last, $prev, $next, $this->isAdmin);
            }
            echo "</td>";
            if ($i % $max_col == $max_col-1) echo "</tr>";
            if ($i % PHOTO_COUNT == PHOTO_COUNT-1) {
                $str++;
            }
            $i++;
        }
        if ($i % $max_col != 0) echo "</tr>";
        echo "</table>";
    }

}

?>