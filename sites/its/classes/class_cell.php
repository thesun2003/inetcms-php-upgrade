<?php

class Cell
{
 var $name;
 var $name_v;
 var $type;
 var $value;
 var $c_value;
 var $result;
 var $class;
 function Cell($cv_name,$c_name,$c_type,$c_val,$cc_val,$dop = "")
 {
  $this->month = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
  $this->name_v=$cv_name;
  $this->name=$c_name;
  $this->type=$c_type;
  $this->value=$c_val;
  $this->c_value=$cc_val;
  $this->result="";
  $this->dop=$dop;
  $onclick = "";

  $cell_class="class=\"".$this->class."\"";

  switch ( $this->type )
  {
    case "city":
        $this->result  = '<table border="0" width="100%" cellspacing="0" style="font-size:12px"><tr>';
        $this->result .= '<td width="10%">Страна</td><td><input id="country" type="text" style="width:100%;" value="'.$this->value[0].'" name="city_t[0]"></td>';
        $this->result .= '<td rowspan="2" align="center" width="10%"><input type="button" value="выбрать" onClick="wopen(\'/inc/getcity.php\', \'city\', \'500\', \'350\', \'no\', \'no\', \'no\')"></td></tr>';
        $this->result .= '<tr><td>Город</td><td><input id="city" type="text" style="width:100%;" value="'.$this->value[1].'" name="city_t[1]"></td></tr>';
        $this->result .= '</table>';
        break;                                                                                                           
    case "date":
        $this->result = '<select '.$cell_class.' style="width: 50px;" name="'.$this->name.'[0]">';
        for ($i=1; $i < 32; $i++) {
            $checked = "";
            if ((int)$this->value[0] == $i) {
                $checked = "selected=\"selected\"";
            }
            $this->result .= "<option value='".$i."' $checked>".$i."</option>";
        }
        $this->result .= '</select>';
        $this->result .= '<select '.$cell_class.' style="width: 90px;" name="'.$this->name.'[1]">';
        for ($i=1; $i <= 12; $i++) {
            $checked = "";
            if ((int)$this->value[1] == $i) {
                $checked = "selected=\"selected\"";
            }
            $this->result .= "<option value='".$i."' $checked>".$this->month[$i-1]."</option>";
        }
        $this->result .= '</select>';
        $this->result .= '<select '.$cell_class.' style="width: 70px;" name="'.$this->name.'[2]">';
        $now_year = date("Y");
        for ($i=$now_year; $i >= 1950; $i--) {
            $checked = "";
            if ((int)$this->value[2] == $i) {
                $checked = "selected=\"selected\"";
            }
            $this->result .= "<option value='".$i."' $checked>".$i."</option>";
        }
        $this->result .= '</select>';
        break;
    case "text": {
        $this->result = '<input '.$cell_class.' style="width: 100%;" type="text" name="'.$this->name.'" value="'.$this->value.'">';
        break ;
        }  
    case "just_text": {
        $this->result = '<b>'.$this->value.'</b>';
        break ;
        }
        break;
    case "textarr" :
         {
            for ($i=0; $i < count($this->value); $i++)
            {
             $per=ceil(100/count($this->value))-(9*count($this->value));
         $this->result.= $this->c_value[$i].' <input '.$cell_class.' style="width: '.$per.'%;" type="text" name="'.$this->name.'['.$i.']" value="'.$this->value[$i].'" id="'.$this->name.'['.$i.']"> ';
        }
        break ;
         }  

    case "password" :
         {
        $this->result = '<input '.$cell_class.' style="width: 100%;" type="password" name="'.$this->name.'" value="'.$this->value.'">';
        break ;
         }  
    case "textarea" :
         {
        $this->result = '<textarea '.$cell_class.' style="width: 100%; height: 100px;" name="'.$this->name.'">'.$this->value.'</textarea>';
        break ;
         }  
    case "select" :
    {
        $this->result.= '<select '.$cell_class.' style="width: 100%;" name="'.$this->name.'">';
        if (count($this->value)-1 != count($this->c_value)) $err = new Error('Не совпадает кол-во имен и значений!\nИмен: '.count($this->value).'\nЗначений: '.count($this->c_value));
        $this->result.= '<option value="0">не выбрано</option>';
        for ($i=0; $i < count($this->value)-1; $i++)
        {
                 $selected="";
         if ($this->value[$i] == $this->value[count($this->value)-1]) $selected=" selected ";
         $this->result.= '<option value="'.$this->value[$i].'" '.$selected.'>'.$this->c_value[$i].'</option>';
        }
        $this->result.= '</select>';
        break ;
    }
    case "checkarr" :
         {
            for ($i=0; $i < count($this->c_value); $i++)
            {
             if ($i == count($this->c_value)-1) $dop="";
             $chek="";
             for ($j=count($this->c_value); $j < count($this->value); $j++)
             {
                  if ($this->value[$i] == $this->value[$j])  { $chek="checked"; break; }
             }
             $this->result.= '<nobr><input '.$cell_class.' '.$chek.' type="checkbox" name="'.$this->name.'['.$i.']" id="'.$this->name.'['.$i.']" value="'.$this->value[$i].'">&nbsp;'.$this->c_value[$i].'</nobr>'.$dop;
            }
        break ;
             }

    case "radioarr" :
         {
            for ($i=0; $i < count($this->c_value); $i++)
            {
             $chek="";
             for ($j=count($this->c_value); $j < count($this->value); $j++)
             {
                  if ($this->value[$i] == $this->value[$j])  { $chek="checked"; break; }
             }
             $this->result.= '<nobr><input '.$cell_class.' '.$chek.' type="radio" name="'.$this->name.'" id="'.$this->name.'['.$i.']" value="'.$this->value[$i].'">&nbsp;'.$this->c_value[$i].'</nobr>'.$dop;
            }
             $chek="";
            if ($this->value[count($this->value)-1] == 0) { 
                $chek="checked";
            }
            $this->result.= '<nobr><input '.$cell_class.' '.$chek.' type="radio" name="'.$this->name.'" id="'.$this->name.'['.$i.']" value="0">&nbsp;не выбрано</nobr>'.$dop;
        break ;
             }

    case "checkbox" :
         {
        $this->result = '<input '.$cell_class.' type="checkbox" name="'.$this->name.'" value="'.$this->value.'">';
        break ;
             }
    case "radio" :
         {
        $this->result = '<input '.$cell_class.' type="radio" name="'.$this->name.'" '.$this->value.'>';
        break ;
         }
    case "hidden" :
        $this->result = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'">';
        break ;
    case "button" :
         {
            if ($this->c_value !="") $onclick='onClick="'.$this->c_value.'"';
        $this->result = '<input '.$cell_class.' type="button" name="'.$this->name.'" value="'.$this->value.'" '.$onclick.'>';
        break ;
         }  
    case "submit" :
         {
            if ($this->c_value !="") $onclick='onClick="'.$this->c_value.'"';
        $this->result = '<input '.$cell_class.' type="submit" name="'.$this->name.'" value="'.$this->value.'" '.$onclick.'>';
        break ;
         }
    case "file" :
         {
            $this->result = '<input size=60 style="width:100%" '.$cell_class.' type="file" name="'.$this->name.'">';
            break ;
         }
    default :
        $this->result = $this->value;
                break ;
  }
/*
  $types = array('checkarr', 'radioarr', 'city');
    if (in_array($this->type, $types)) {
        $this->result .= "<hr class='myPage'";
    }
*/
 }
}
?>