<?php
include('../config/globals.php');
header('Content-type: application/rss+xml; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$tag = urldecode($_GET['q']); 
function decouple($query) {
	$cleantag = str_replace('#', '', $query); // remove hash
	return explode(' ', $cleantag);
}
$matches = decouple($tag);
$cleantag1 = $matches[0];
$cleantag2 = $matches[1];

if ($_GET['client_id']) {
	$client_id = $_GET['client_id']; 
		} else { 
	$client_id = $instagram_key;
}

$lat = $_GET['lat'];
$long = $_GET['long'];
$distance = $_GET['distance'];
$count = $_GET['count'];
$not = $_GET['not'];
$defaultloc = $_GET['defaultloc'];
function explodeCommas($commalist) {
	return explode(',', $commalist);
}
list($defaultlat,$defaultlong) = explodeCommas($defaultloc);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n\n";
$instagram_cleantag1="https://api.instagram.com/v1/tags/".urlencode($cleantag1)."/media/recent?client_id=$client_id&count=$count";
$string_cleantag1 .= file_get_contents($instagram_cleantag1); // get json content
$array_cleantag1 = json_decode($string_cleantag1, true); //json decoder

$array_refined = array();

if (!empty($cleantag2)) {
	$instagram_cleantag2="https://api.instagram.com/v1/tags/" . urlencode($cleantag2) . "/media/recent?client_id=$client_id&count=$count";
	$string_cleantag2 .= file_get_contents($instagram_cleantag2); // get json content
	$array_cleantag2 = json_decode($string_cleantag2, true); //json decoder

	$b = 0;
	foreach ($array_cleantag1['data'] as $item) {
		if (in_array($cleantag2,$item['tags'])) {
			array_push($array_refined, $item);
		}
	$b++;
	}

	$c = 0;
	foreach ($array_cleantag2['data'] as $item) {
		if (in_array($cleantag1,$item['tags'])) {
			array_push($array_refined, $item);
		}
	$c++;
	}

} else {
	$a = 0;
	foreach ($array_cleantag1['data'] as $item) {
		array_push($array_refined, $item);
	$a++;
	}
}

?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>Instagrams: <?php echo $tag; ?></title>
    <link>http://feedwax.com/instagramtag.php?<?php echo urlencode($_SERVER['QUERY_STRING']); ?></link>
    <description>Instagram feed of photos tagged with "<?php echo $tag; ?>"</description>

<?php
$i = 0; 
foreach ($array_refined as $v) {
$array_tags = $v['tags'];
if (!in_array($not,$array_tags)  && (!empty($v['location']['latitude']) || !empty($defaultlat)) ){
		echo "<item>\n";
		echo "<title>Photo by " . $v['user']['username'] . ": \"" . htmlentities(utf8_decode($v['caption']['text'])) . "\"</title>\n";
		echo "<description>By @" . $v['user']['username'] . " via Instagram: http://instagram.com/" . $v['user']['username'] . "</description>\n";
		echo "<media:content url=\"" . $v['images']['standard_resolution']['url'] . "\" type=\"image/jpeg\"></media:content>\n";
		echo "<link>" . $v['link'] . "</link>\n";
		echo "<guid>". $v['link'] . "</guid>\n";
		if (empty($v['location']['latitude'])) {
			echo "<geo:lat>". $defaultlat . "</geo:lat>\n";
			echo "<geo:long>". $defaultlong . "</geo:long>\n";			
		} else {
			echo "<geo:lat>". $v['location']['latitude'] . "</geo:lat>\n";
			echo "<geo:long>". $v['location']['longitude'] . "</geo:long>\n";
		}
		echo "<pubDate>" . date("D, d M Y H:i:s O", $v['caption']['created_time']) . "</pubDate>\n";
		echo "</item>\n";
	}
unset($array_tags);
$i++;
}

?>
    </channel>
</rss>
