<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

if ($_GET['tag']) {
	$tag = $_GET['tag']; 
		} else { 
	$tag = "n0ticed";
}

if ($_GET['client_id']) {
	$client_id = $_GET['client_id']; 
		} else { 
	$client_id = $instagram_key;
}

$lat = $_GET['lat'];
$long = $_GET['long'];
$distance = $_GET['distance'];
$count = $_GET['count'];

echo "<?xml version=\"1.0\"?>\n";

$api_url="https://api.instagram.com/v1/tags/$tag/media/recent?client_id=$client_id&lat=$lat&long=$long&distance=$distance&count=$count";
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>#<?php echo $tag; ?> Instagrams</title>
    <link><?php echo htmlentities($api_url); ?></link>
    <description>Instagram feed of photos tagged with #<?php echo $tag; ?></description>

<?php
$i = 0; 
foreach ($array['data'] as $v) {
	if ($v['caption'] && $v['location']['latitude']) {
		echo "<item>\n";
		echo "<title>" . htmlentities($v['caption']['text']) . "</title>\n";
		echo "<description> </description>\n";
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
