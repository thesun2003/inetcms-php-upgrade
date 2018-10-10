<?php
	require_once 'admin/config.php';
	require_once 'inc/functions.php';
	require_once 'modules/cat.inc.php';

	if (@$_GET['cat'] == 'photos' && isset($_GET['pag'])) $titlemod = 'title_jpg.inc';
	elseif (@$_GET['cat'] == 'response') $titlemod = 'title_jpg.inc';
	else $titlemod = 'title_swf.inc';

	if (empty($row['mtitle'])) {
        	if (empty($row['title'])) $title = 'Агентство событий и праздников `Ура`- event-маркетинг в Новосибирске';
        	else $title = $row['title'];
	} else $title = $row['mtitle'];

	$metadesc = (!empty($row['mdescr'])) ? $row['mdescr'] : 'Наше агентство праздников оказывает услуги в сфере организации и проведении корпоративных мероприятий';
	$metakey  = (!empty($row['mkeyw'])) ? $row['mkeyw'] : 'агентство праздников новосибирск, организация праздников,проведение праздников,проведение промо-акций, проведение корпоративных мероприятий новосибирск';

	if (isset($_GET['cat'])) {
	
		switch ($_GET['cat']) {
			case 'news':
			$mod = "news"; break;

			case 'projects':
			$mod = "news"; break;

			case 'photos':
			$mod = "photos"; break;

			case 'clients':
			$mod = "clients"; break;

			case 'pressa':
			$mod = "pressa"; break;

			case 'response':
			$mod = "response"; break;

			case 'spetialization':
			case 'service':
			$mod = "service"; break;
			
			default:
			$mod = "pages";
		}
	
	} else $mod = "projects";

	$banner = array('link' => 'http://www.agharta.ru', 'img' => 'omar.gif', 'alt' => '');
	$banner = array();

        /* show 404 page if no actual page found */
	if ("pages" == $mod && !$row) {
		header("HTTP/1.0 404 Not Found");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>Страница не найдена</title>
</head>
<body>
  <h1>404</h1>
  <p>Запрошенная страница не существует.</p>
</body>
</html>

<?php
		die;
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title><?php echo $title; ?></title>
  <meta name="description" content="<?php echo $metadesc; ?>" />
  <meta name="keywords" content="<?php echo $metakey; ?>" />
  <meta http-equiv="Content-Type" content="text/html;charset=windows-1251" />
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <link rel="stylesheet" type="text/css" href="/css/floatbox.css" />
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"> 
  <script type="text/javascript" src="/js/menu.js"></script>
  <script type="text/javascript" src="/js/floatbox/floatbox.js"></script>
  <script type="text/javascript" src="/js/JsHttpRequest.js"></script>
  <script type="text/javascript">
    function news_column(APage) {

        var req = new JsHttpRequest();
        req.loader = "script";
        req.onreadystatechange = function() {
            window.status += req.readyState;
            if (req.readyState == 4) {
                document.getElementById("news_column").innerHTML = req.responseText;
                window.scrollTo(0, 0);
            }
        }

        document.getElementById("load_progress").src = '/img/loading.gif';
        req.open(null, '/dynamic/news_column.php', true);
        req.send( { page:APage } );
    }  
  </script>
</head>

<body>
<div style="position:absolute">
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<img src='http://counter.yadro.ru/hit?r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' width=1 height=1 alt=''>")//--></script><!--/LiveInternet-->
</div>

<div id="topslogan">PR-акции, презентации, праздники, корпоративные события, событийный туризм</div>

<div style="width:898px; margin:auto">
<table cellpadding="0" cellspacing="0" align="center" id="title">
  <tr>
    <td>
      <div style="float:none; clear:both;"><?php include_once("inc/$titlemod"); ?></div>
    </td>
  </tr>
</table>

<?php include_once("inc/menu.php"); ?>

<table width="898" align="center" id="main" cellspacing="0" cellpadding="0">
  <tr>
    <td id="head_l"><?php echo $cat_name; ?></td>
    <td class="mid">&nbsp;</td>
    <td id="head_r">НОВОСТИ</td>
  </tr>

  <tr id="contenttop">
    <td id="top_l"></td>
    <td class="mid">&nbsp;</td>
    <td id="top_r"></td>
  </tr>

  <tr>
    <td valign="top" id="left"><div style="width:571px; overfow-x:hidden"><?php include_once "modules/$mod.php"; ?></div></td>
    <td class="mid">&nbsp;</td>
    <td id="right">
      <?php if ($banner) { ?>
      <div style="margin:-10px auto 1em auto; text-align:center"><a href="<?=$banner['link'];?>" target="_blank"><img src="/img/<?=$banner['img'];?>" alt="<?=$banner['alt'];?>" /></a></div>
      <?php } ?>
      <?php print_news_column(); /* invoke with no params - display top and announce news */ ?>
      <div id="news_column"><?php print_news_column(1); /* invoke with page # param - display page of simple news */ ?></div>
    </td>
  </tr>

  <tr id="contentbottom">
    <td id="bottom_l"></td>
    <td class="mid">&nbsp;</td>
    <td id="bottom_r"></td>
  </tr>
  
  <tr id="sitebottom">
    <td id="foot_l">
    <div style="width:571px; overflow:hidden">
      <table align="center" cellpadding="0" cellspacing="10" width="100">
        <tr><?php include_once("modules/banners.php"); ?></tr>
      </table>
    </div>
    </td>
    <td class="mid">&nbsp;</td>
    <td id="foot_r" valign="top">
      <div style="margin-top:15px"><?=$contact?></div>
      <div align="right"><!--LiveInternet logo--><a href="http://www.liveinternet.ru/stat/ypa.ru"
target="_blank"><img src="http://counter.yadro.ru/logo?24.2"
title="LiveInternet: показано число посетителей за сегодня"
alt="" border="0" width="88" height="15" /></a><!--/LiveInternet--></div>
    </td>
  </tr>
</table>
<!--center><img src="/img/foot.jpg" width="898" height="16" alt="" /></center-->
</div>
</body>

</html>
