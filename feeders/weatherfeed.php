<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$lat = $_GET['lat'];
$lng = $_GET['lng'];

echo "<?xml version=\"1.0\"?>\n";

$place_api="http://where.yahooapis.com/geocode?location=$lat,$lng&flags=J&gflags=R&appid=".$yahoo_place_key;
$place_string .= file_get_contents($place_api); // get json content
$place_array = json_decode($place_string, true); //json decoder
$api_url="http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%3D" . $place_array['ResultSet']['Results'][0]['woeid'] . "&format=json";
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title><?php echo $array['query']['results']['channel']['description']; ?></title>
    <link><?php echo htmlentities($api_url); ?></link>
    <description><?php echo $array['query']['results']['channel']['description']; ?></description>

<?php
echo "<item>\n";
echo "<title>Today's forecast for " . $array['query']['results']['channel']['location']['city'] . ": " . $array['query']['results']['channel']['item']['forecast'][0]['text'] . " " . $array['query']['results']['channel']['item']['forecast'][0]['high'] . "/" . $array['query']['results']['channel']['item']['forecast'][0]['low'] . " F</title>\n";
echo "<description><![CDATA[" . $array['query']['results']['channel']['item']['description'] . "]]></description>\n";
echo "<link>" . $array['query']['results']['channel']['item']['link'] . "</link>\n";
echo "<guid>". $array['query']['results']['channel']['item']['link'] . "</guid>\n";
echo "<geo:lat>". $array['query']['results']['channel']['item']['lat'] . "</geo:lat>\n";
echo "<geo:long>". $array['query']['results']['channel']['item']['long'] . "</geo:long>\n";
echo "<pubDate>" . $array['query']['results']['channel']['item']['pubDate'] . "</pubDate>";
echo "</item>\n";
?>
    </channel>
</rss>
