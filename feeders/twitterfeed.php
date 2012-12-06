<?php
header('Content-type: application/rss+xml; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
include('../config/globals.php');

if (!empty($_GET['media'])) {
	$media = $_GET['media']; 
		} else { 
	$media = "all";
}
if ($_GET['defaultloc']) {
	$defaultloc = $_GET['defaultloc']; 
		} else { 
	$defaultloc = "exclude";
}
function explodeCommas($commalist) {
	return explode(',', $commalist);
}

if (!empty($_GET['altgeocode'])) {
	list($defaultlat,$defaultlong) = explodeCommas($_GET['altgeocode']);
		} else { 
	list($defaultlat,$defaultlong) = explodeCommas($_GET['geocode']);
}

$tag = urlencode($_GET['q']);
$linkinfield = $_GET['linkinfield'];
$rpp = $_GET['rpp'];
$geocode = "&geocode=" . $_GET['geocode']; 

$twitterapi_url="https://search.twitter.com/search.json?include_entities=true&q=" . $tag . $geocode . "&result_type=mixed&rpp=" . $rpp;
$string .= file_get_contents($twitterapi_url); // get json content
$array = json_decode($string, true); //json decoder
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>Twitter: <?php echo urldecode($tag); ?></title>
    <link><?php echo htmlentities($twitterapi_url); ?></link>
    <description>Tweets using the query <?php echo urldecode($tag); ?></description>

<?php
$i = 0; 
foreach ($array['results'] as $v) {

	if (!empty($v['entities']['media'][0]['media_url'])) {
		$mediacontent = "<media:content url=\"" . $v['entities']['media'][0]['media_url'] . "\" type=\"image/jpeg\"></media:content>\n";
	} else {
		$mediacontent = null;
	}

	if ($media != "images") {
		$mediafilter = "ok";
	} elseif (($media == "images") && ($mediacontent != null)) {
		$mediafilter = "ok";
	} else {
		$mediafilter = "fail";
	}
		
	if (!empty($v['geo']) && ($v['geo']['coordinates'][0] != "0")) {
		$geolat = "<geo:lat>" . $v['geo']['coordinates'][0] . "</geo:lat>\n";
		$geolong = "<geo:long>" . $v['geo']['coordinates'][1] . "</geo:long>\n";
		$geodata = "ok";
	} elseif (($defaultloc == "assign") && (!empty($defaultlat))) {
		$geolat = "<geo:lat>" . $defaultlat . "</geo:lat>\n";
		$geolong = "<geo:long>" . $defaultlong . "</geo:long>\n";	
		$geodata = "ok";
	} else {
		$geodata = "fail";
	}

	if (($geodata != "fail") && ($mediafilter != "fail")) {
		echo "<item>\n";
		$url = "https://twitter.com/" . $v['from_user'] . "/status/" . $v['id_str'];
			echo "<title>@" . $v['from_user'] . " tweets: " . $v['text'];
			if ($linkinfield == "title") {echo " " . $url;}
			echo "</title>\n";
			echo "<description>";
			if ($linkinfield != "title") {echo $url . " ";}
			echo "https://twitter.com/" . $v['from_user'];
			echo "</description>\n";
			echo "<pubDate>" . $v['created_at']. "</pubDate>\n";
			echo $geolat;
			echo $geolong;
			echo $mediacontent;
			echo "<link>$url</link>\n";
			echo "<guid>$url</guid>\n";
		echo "</item>\n";
	} 
	$i++;
}
?>
</channel>
</rss>
