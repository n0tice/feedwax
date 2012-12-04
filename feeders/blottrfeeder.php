<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

echo "<?xml version=\"1.0\"?>\n";
$path = $_GET['path'];
$thispage_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/blottrfeeder.php?" . urlencode($_SERVER['QUERY_STRING']);
$xml = simplexml_load_file($path); 
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title><?php echo $xml->channel->title; ?></title>
    <link><?php echo htmlentities($thispage_url); ?></link>
    <description>Geotagged by n0ticingfeeds.com</description>
<?php
if ($xml->channel) {
foreach ($xml->channel->item as $v) {
		echo "<item>\n";
		$title = strip_tags($v->title);
		$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
		echo "     <title>" . str_replace ( '&', '&amp;', $title ) . "</title>\n";
		echo "     <description>" . htmlspecialchars($v->description) . "</description>\n";
		echo "     <media:content url=\"" . $v->enclosure['url'] . "\" type=\"image/jpeg\"></media:content>\n";
		echo "     <link>" . htmlentities($v->link) . "</link>\n";
		echo "     <guid>" . htmlentities($v->link) . "</guid>\n";
		echo "     <pubDate>" . $v->pubDate . "</pubDate>\n";
		echo "</item>\n";
	}
} else {
echo "<item>No results found</item>";
}
?>
    </channel>
</rss>
