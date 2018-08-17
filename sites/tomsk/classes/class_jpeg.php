<?
using::add_class('bmp');

class JPEG
{
    function __construct($img, $toSize = array(480, 200)) {
        $this->watermark_image = ROOT . '/images/watermark.png';
        $this->img = $img;
        $type = getimagesize($this->img);
        $this->width = $type[0];
        $this->height = $type[1];
        $this->type = $type['mime'];

        if (!is_array($toSize)) {
          $toSize = array($this->width, $this->height);
        }

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
        if ($this->type=="image/bmp")  $img_source = imagecreatefrombmp($this->img);
        if ($this->type=="image/png")  $img_source = imagecreatefrompng($this->img);
        imagecopyresampled($image_p, $img_source, 0, 0, 0, 0, $this->new_width_big, $this->new_height_big, $this->width, $this->height);

        // add a watermark here
        $watermark = imagecreatefrompng($this->watermark_image);
        $marge_right = 10;
        $marge_bottom = 10;
        $sx = imagesx($watermark);
        $sy = imagesy($watermark);
        //imagecopy($image_p, $watermark, imagesx($image_p) - $sx - $marge_right, imagesy($image_p) - $sy - $marge_bottom, 0, 0, imagesx($watermark), imagesy($watermark));
        imagecopy($image_p, $watermark, (imagesx($image_p) - $sx) / 2, (imagesy($image_p) - $sy) / 2, 0, 0, imagesx($watermark), imagesy($watermark));

        imagejpeg($image_p, $dest, 85);
    }

    function show() {
        header("Content-type: image/jpeg");
        $image_p = imagecreatetruecolor($this->new_width, $this->new_height);
        if ($this->type=="image/gif") $img_source = imagecreatefromgif($this->img);
        if ($this->type=="image/jpeg") $img_source = imagecreatefromjpeg($this->img);
        if ($this->type=="image/bmp")  $img_source = imagecreatefrombmp($this->img);
        if ($this->type=="image/png")  $img_source = imagecreatefrompng($this->img);
        imagecopyresampled($image_p, $img_source, 0, 0, 0, 0, $this->new_width, $this->new_height, $this->width, $this->height);
        imagejpeg($image_p, '', 100);
    }
}
