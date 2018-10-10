<?

require_once 'config.php';
//mysql_query("SET NAMES cp1251");

if(isset($_GET['getCountriesByLetters']) && isset($_GET['letters'])){
	//$letters = preg_replace("/[^à-ÿ ]/si","",$_GET['letters']);
	
	//$letters = iconv('utf-8', 'windows-1251', $_GET['letters']);
  $letters = $_GET['letters'];
	$res = mysql_query("SELECT id, name FROM clients
                        WHERE (name LIKE '%".$letters."%')
                        ORDER BY name LIMIT 10");
	while($inf = mysql_fetch_array($res)){
    $countryName = $inf["name"];
		echo $inf["id"]."###".$countryName."|";
	}
}
?>