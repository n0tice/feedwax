<?php
include('../config/globals.php');
header('Content-type: application/rss+xml; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$tag = urldecode($_GET['q']); 
function explodeSpaces($query) {
	$query = str_replace('#', '', $query); // remove hash
	return explode(' ', $query);
}
list($query1,$query2) = explodeSpaces($tag);

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
$api_url1="https://api.instagram.com/v1/tags/$query1/media/recent?client_id=$client_id&lat=$lat&long=$long&distance=$distance&count=$count";
#echo $api_url1;
$string1 .= file_get_contents($api_url1); // get json content
$array1 = json_decode($string1, true); //json decoder

if ($query2) {
	$api_url2="https://api.instagram.com/v1/tags/$query2/media/recent?client_id=$client_id&lat=$lat&long=$long&distance=$distance&count=$count";
	#echo $api_url2;
	$string2 .= file_get_contents($api_url2); // get json content
	$array2 = json_decode($string2, true); //json decoder
	$new_array = array_merge_recursive($array1, $array2);
} else {
	$new_array = $array1;
}

?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>#<?php echo $tag; ?> Instagrams</title>
    <link>http://feedwax.com/instagramtag.php?q=<?php echo urlencode($_GET['q']); ?></link>
    <description>Instagram feed of photos tagged with "<?php echo $tag; ?>"</description>

<?php
$i = 0; 
foreach ($new_array['data'] as $v) {
	if (!(in_array($not, $v['tags']))) {
		if (!empty($v['caption']) && (($defaultlat)) || ($v['location']['latitude'])) {
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
			$i++;
		}
	}
}

?>
    </channel>
</rss>
