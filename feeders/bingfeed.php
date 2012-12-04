<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

if ($_GET['query']) {
	$query = utf8_encode(urlencode($_GET['query'])); 
		} else { 
	$query = "n0ticed";
}
if ($_GET['accountKey']) {
	$accountKey = $_GET['accountKey']; 
		} else { 
	$accountKey = $bing_key;
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
if ($_GET['count']) {
	$count = $_GET['count']; 
		} else { 
	$count = "10";
}

echo "<?xml version=\"1.0\"?>\n";

$api_url="https://api.datamarket.azure.com/Data.ashx/Bing/Search/v1/News?Query=%27$query%27&Adult=%27Strict%27&Latitude=$lat&Longitude=$long&NewsSortBy=%27Date%27&\$top=$count&\$format=JSON";

$n0ticefeed_url="http://" . $_SERVER['SERVER_NAME'] . "/bingfeed.php?" . $_SERVER['QUERY_STRING'];

?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>News Search</title>
    <link><?php echo htmlentities($n0ticefeed_url); ?></link>
    <description>News search via Bing</description>

<?php
$process = curl_init($api_url);
curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($process, CURLOPT_USERPWD,  $accountKey . ":" . $accountKey);
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($process);

$jsonobj = json_decode($response);

foreach($jsonobj->d->results as $v)
{                        
	echo "<item>\n";
	echo "<title>" . htmlspecialchars($v->Title) . " " . htmlspecialchars($v->Url) . "</title>\n";
	echo "<description>" . htmlspecialchars($v->Description) . " Source: " . $v->Source . "</description>\n";
	$pubdate = strtotime ($v->Date);
	echo "<pubDate>" . date("D, d M y H:i:s O", $pubdate) . "</pubDate>\n";
	echo "<link>" . $v->Url . "</link>\n";
	echo "<guid>" . $v->Url . "</guid>\n";
	echo "<geo:lat>" . $lat . "</geo:lat>\n";
	echo "<geo:long>" . $long . "</geo:long>\n";
	echo "</item>\n";
}

?>
    </channel>
</rss>
