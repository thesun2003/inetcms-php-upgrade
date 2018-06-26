<?

class JPEG {
    function JPEG($img, $toSize = array(480, 200)) {
        $this->img = $img;
        $type = getimagesize($this->img);
        $this->width = $type[0];
        $this->height = $type[1];
        $this->type = $type['mime'];
        if ($type[0] > $type[1]) {
            $persent_big = $toSize[0] / $type[0];
            $persent = $toSize[1] / $type[0];
        }
        else {
            $persent_big = $toSize[0] / $type[1];
            $persent = $toSize[1] / $type[1];
        }
        $this->new_width_big = round($type[0] * $persent_big);
        $this->new_height_big = round($type[1] * $persent_big);
        $this->new_width = round($type[0] * $persent);
        $this->new_height = round($type[1] * $persent);
    }

    function save($dest) {
        $image_p = imagecreatetruecolor($this->new_width_big, $this->new_height_big);
        if ($this->type=="image/gif") $img_source = imagecreatefromgif($this->img);
        if ($this->type=="image/jpeg") $img_source = imagecreatefromjpeg($this->img);
        imagecopyresampled($image_p, $img_source, 0, 0, 0, 0, $this->new_width_big, $this->new_height_big, $this->width, $this->height);
        imagejpeg($image_p, $dest, 85);
    }

    function show() {
        header("Content-type: image/jpeg");
        $image_p = imagecreatetruecolor($this->new_width, $this->new_height);
        if ($this->type=="image/gif") $img_source = imagecreatefromgif($this->img);
        if ($this->type=="image/jpeg") $img_source = imagecreatefromjpeg($this->img);
        imagecopyresampled($image_p, $img_source, 0, 0, 0, 0, $this->new_width, $this->new_height, $this->width, $this->height);
        imagejpeg($image_p, '', 100);
    }
}
