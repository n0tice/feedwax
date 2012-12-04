<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

if ($_GET['q']) {
	$q = $_GET['q']; 
		} else { 
	$q = "n0ticed";
}
if ($_GET['key']) {
	$key = $_GET['key']; 
		} else { 
	$key = $youtube_key;
}
if ($_GET['lat']) {
	$lat = $_GET['lat']; 
		} else { 
	$lat = "51.534631";
}
if ($_GET['long']) {
	$long = $_GET['long']; 
		} else { 
	$long = "-0.121965";
}
if ($_GET['radius']) {
	$radius = $_GET['radius'] . "km"; 
		} else { 
	$radius = "10km";
}
if ($_GET['maxresults']) {
	$maxresults = $_GET['maxresults']; 
		} else { 
	$maxresults = "10";
}

echo "<?xml version=\"1.0\"?>\n";

$api_url="https://gdata.youtube.com/feeds/api/videos?q=$q&orderby=published&max-results=$maxresults&v=2&time=this_week&location=$lat,$long&location-radius=$radius&genre=7&duration=short&key=".&youtube_key."&alt=json";
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>YouTube search: <?php echo $q; ?></title>
    <link><?php echo htmlentities($api_url); ?></link>
    <description><?php echo $maxresults; ?> YouTube videos of <?php echo $q; ?></description>

<?php
$i = 0; 

if ($array['feed']['openSearch$totalResults']['$t'] != 0) {
	foreach ($array['feed']['entry'] as $v) {
			echo "<item>";
			echo "<title>" . htmlspecialchars($v['title']['$t']) . " " . htmlentities($v['link'][0]['href']) . "</title>\n";
			echo "<description>";
			if ($v['media$description']['$t']) {echo htmlspecialchars($v['media$description']['$t']) . ". ";}
			echo "By " . $v['author'][0]['name']['$t'];
			echo "</description>\n";
			echo "<link>" . htmlentities($v['link'][0]['href']) . "</link>\n";
			echo "<guid>" . htmlentities($v['link'][0]['href']) . "</guid>\n";
			echo "<geo:lat>". $lat . "</geo:lat>\n";
			echo "<geo:long>". $long . "</geo:long>\n";
			$pubdate = strtotime ($v['published']['$t']);
			echo "<pubDate>" . date("D, d M y H:i:s O", $pubdate) . "</pubDate>";
			echo "</item>\n";
			$i++;
		} 
}
	
?>
    </channel>
</rss>
