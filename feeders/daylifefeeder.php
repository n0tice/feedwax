<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$q = $_GET['q']; 
$url_encoded_q = urlencode($q);

if ($_GET['limit']) {
	$limit = $_GET['limit']; 
		} else { 
	$limit = "10";
}

echo "<?xml version=\"1.0\"?>\n";

# Configure the daylife api server url 
	$daylife_server = "freeapi.daylife.com";
	$protocol = "jsonrest";
	$version = "4.10";
	$publicapi_access_url = "http://" . $daylife_server . "/" . $protocol . "/publicapi/" . $version . "/";
	$method='search_getRelatedArticles';

# Configure your api credentials 

# For search_X methods, the Core Input is the query term itself
$signature = md5($daylife_accesskey . $daylife_sharedsecret . $q);

#Draw from news in last 3 days
$end_time = date(U);
$start_time = $end_time - (3 * 86400);

$api_url = $publicapi_access_url . $method . '?accesskey=' . $accesskey . '&signature=' . $signature . '&query=' . $url_encoded_q;
$daylifefeeder_url="http://" . $_SERVER['SERVER_NAME'] . "/feeders/daylifefeeder.php?" . urlencode($_SERVER['QUERY_STRING']);
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/daylifefeeder-geocoded.php?url=" . urlencode($daylifefeeder_url);
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title><?php echo $_GET['q']; ?></title>
    <link><?php echo $n0ticefeed_url; ?></link>
    <description><?php echo $_GET['q']; ?></description>

<?php
	$i = 0; 
	foreach ($array['response']['payload']['article'] as $v) {
		echo "<item>";
		echo "<title>" . htmlspecialchars($v['headline']) . "</title>\n";
		echo "<description>";
		echo htmlspecialchars($v['excerpt']) . " - By " . htmlspecialchars($v['source']['name']);
		echo "</description>\n";
		echo "<link>" . htmlentities($v['url']) . "</link>\n";
		echo "<guid>" . htmlentities($v['url']) . "</guid>\n";
		$pubdate = strtotime ($v['timestamp']);
		echo "<pubDate>" . date("D, d M y H:i:s O", $pubdate) . "</pubDate>";
		echo "</item>\n";
	$i++;
}	
?>
    </channel>
</rss>
