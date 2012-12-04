<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

if ($_GET['client_id']) {
	$client_id = $_GET['client_id']; 
		} else { 
	$client_id = $instagram_key;
}

$lat = $_GET['lat'];
$lng = $_GET['lng'];
$distance = $_GET['distance'];
$count = $_GET['count'];

echo "<?xml version=\"1.0\"?>\n";


$place_api="https://api.instagram.com/v1/locations/search?client_id=$client_id&lat=$lat&lng=$lng";
$place_string .= file_get_contents($place_api); // get json content
$place_array = json_decode($place_string, true); //json decoder

$api_url="https://api.instagram.com/v1/locations/" . $place_array['data'][0]['id'] . "/media/recent?client_id=$client_id&count=$count";
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title><?php echo $place_array['data'][0]['name']; ?> Instagrams</title>
    <link><?php echo htmlentities($api_url); ?></link>
    <description>Instagram feed of recent photos near <?php echo $place_array['data'][0]['name']; ?></description>

<?php
$i = 0; 
foreach ($array['data'] as $v) {
	if ($v['caption'] && $v['location']['latitude']) {
		echo "<item>\n";
		echo "<title>" . utf8_encode(htmlentities($v['caption']['text'],ENT_COMPAT,'utf-8')) . " - Photo by " . $v['user']['username'] . "</title>\n";
		echo "<description></description>\n";
		echo "<media:content url=\"" . $v['images']['standard_resolution']['url'] . "\" type=\"image/jpeg\"></media:content>\n";
		echo "<link>" . $v['link'] . "</link>\n";
		echo "<guid>". $v['link'] . "</guid>\n";
		echo "<geo:lat>". $v['location']['latitude'] . "</geo:lat>\n";
		echo "<geo:long>". $v['location']['longitude'] . "</geo:long>\n";
		echo "<pubDate>" . date("D, d M y H:i:s O", $v['caption']['created_time']) . "</pubDate>";
		echo "</item>\n";
		$i++;
	}
}

?>
    </channel>
</rss>
