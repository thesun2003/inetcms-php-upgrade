<?php

///----------------------------------------------------------------------------
/// ¬ывод формы
///----------------------------------------------------------------------------

class _form
{
    var $width,$align,$style;
    var $action,$method,$ctt;
    var $result;
    var $cels;
    var $submit;
// конструктор
    function _form($f_action,$f_method,$f_ctt,$class = "box")
    {
        $f_width="100%";
        $f_align="center";
      if ($f_action =="") $f_action=$PHP_SELF;
      if ($f_method =="") $f_method="GET";
        $this->width  = $f_width;
        $this->align  = $f_align;
        $this->action = $f_action;
        $this->method = $f_method;
        $this->dop = "";
        $this->ctt    = $f_ctt;
        $this->result="";
        $this->cels=array();
        $this->submit=array();
        if ($class!="") $this->class=' class="'.$class.'" ';
    }
// закончилс€ конструктор

        function AddCell($c_cell)
        {
             array_push($this->cels,$c_cell);
        }

        function AddSubmit($c_cell) {
             array_push($this->submit,$c_cell);
        }

        function Create()
        {
         $this->width  = "100%";
         $this->result.="<a name=form><form name='form' action=\"".$this->action."\" method=\"".$this->method."\" ".$this->dop." enctype=\"".$this->ctt."\">"."\r\n";
         $this->result.="<table border=\"0\" align=\"".$this->align."\" width=\"".$this->width."\" style=\"width:".$this->width.";".$this->style."\">"."\r\n";
         for ($i=0; $i < count($this->cels); $i++)
         {
          $this->result.="<tr>"."\r\n";
          switch ( $this->cels[$i]->type )
          {
                case "hidden":
                    {
                     $this->result.=$this->cels[$i]->result."\r\n";
                     break;
                    }
                case "button":
                    {
                     $this->result.="<td ".$this->class." colspan=2>".$this->cels[$i]->result."</td>"."\r\n";
                     break;
                    }
                default:
                    {
                     $this->result.="<td".$this->class." nowrap>".$this->cels[$i]->name_v.":</td>"."\r\n";
                     $this->result.="<td width='100%' ".$this->class.">".$this->cels[$i]->result."</td>"."\r\n";
                     break;
                    }
          }
          $this->result.="</tr>"."\r\n";
          $types = array('checkarr', 'radioarr', 'city');
          if (in_array($this->cels[$i]->type, $types)) {
              $this->result.="<tr><td colspan=\"2\"><hr class='myPage'></td></tr>";
          }

         }
         for ($i=0; $i < count($this->submit); $i++)
         {
          $this->result.="<tr>"."\r\n";
          $this->result.="<td colspan=2 align=\"center\">".$this->submit[$i]->result."</td>"."\r\n";
          $this->result.="</tr>"."\r\n";
         }

         $this->result.="</table>"."\r\n";
         $this->result.="</form>"."\r\n";
        }

        function Show()
        {
         print $this->result;
        }

}

?>