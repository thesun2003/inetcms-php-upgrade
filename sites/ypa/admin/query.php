<?php

  // configuration

  $db_host = 'localhost';
  $db_user = 'acyparu_user';
  $db_pass = 'KUrZMJ8G';
  $db_name = 'acyparu_ypa';

  // do not modify anything below this line
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>MySQL query executor</title>
  <meta http-equiv="content-type" content="text/html; charset=windows-1251">
  <style type="text/css">
    td
    {
      padding:3px;
    }

    a
    {
      color:blue;
      text-decoration:none;
    }
  </style>
  <script language="javascript" type="text/javascript">
    function put_val(obj)
    {
      document.getElementById('query').value += obj.firstChild.nodeValue;
//      document.getElementById('query').setFocus();
      return false;
    }
  </script>
</head>
<body style="font-size:90%; font-family:'Trebuchet MS', Verdana, Tahoma, sans-serif">

  <div style="80%; margin-left:10%; margin-right:10%;">

<?php    

  if (USE_DL && !extension_loaded('mysql')) dl('mysql.so');

  error_reporting(0);
  set_time_limit(0);

  ini_set('magic_quotes', 'Off');

  $haserror = false;

  if (count($_POST))
  {
    if (!( $c = mysql_connect($db_host, $db_user, $db_pass)))
    {
      echo mysql_errno().': '.mysql_error();
      $haserror = true;
    }

    if (false == $haserror)
    {
      mysql_select_db($db_name);
      $lines = split(';', stripslashes($_POST['query']));

      foreach ($lines as $sql)
      if ($sql = trim($sql))
      {
        if ($ok = mysql_query($sql)) 
        {
          $rows = mysql_affected_rows($ok);
          
          if (!$rows) $rows = mysql_num_rows($ok);
          else $rows .= ' rows affected';
          
          if (!$rows) $rows = "";
          else $rows .= ' rows returned';
          
          if ($rows) $rows = " ($rows)";

          echo '<strong style="color:green">Query successfully executed' .$rows. '</strong><br />';
          if (mysql_num_rows($ok))
          {
            echo '<table style="font:8pt Verdana" border="1" celpadding="2">';
            echo '<tr style="font:bolder">';
            echo '<th style="background:silver">#</th>';
            for ($i = 0; $i < mysql_num_fields($ok); $i++)
            {
              $meta = mysql_fetch_field($ok, $i);
              echo '<th>'.$meta->name.'</th>';
            }
            echo '</tr>';
            $counter = 0;
            while ($row = mysql_fetch_array($ok, MYSQL_NUM))
            {
              echo '<tr>';
              echo '<td style="background:#dddddd">' .(++$counter). '</td>';
              for ($i = 0; $i < count($row); $i++) echo '<td><a onclick="put_val(this);" href="#" title="Add to query">' .$row[$i]. '</a></td>';
              echo '</tr>';
            }
            echo '</table>';
            unset($row);
          }  
        }
        else echo '<strong style="color:red">'.mysql_errno().': '.mysql_error().'</strong><br />';
      }
      mysql_close($c);
    }
  }

?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="width:100%; margin-top:1em; border-top:1px solid black; padding-top:1em">

      <strong>Query to execute:</strong><br />
      <textarea name="query" id="query" rows="20" cols="100" style="width:100%; font-family:monospace"><?php if (count($_POST)) echo stripcslashes($_POST['query']); ?></textarea><br />
      <div style="text-align:right; border-top:1px solid black; margin-top:1em; padding-top:0.5em; clear:both"><input type="submit" value="Execute"></div>
   
    </form>

  </div>

</body>
</html>

