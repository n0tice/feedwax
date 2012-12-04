<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

echo "<?xml version=\"1.0\"?>\n";
$thispage_url = "http://earthquake.usgs.gov/earthquakes/catalogs/eqs7day-M5.xml";
$xml = simplexml_load_file($thispage_url); 
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title><?php echo $xml->channel->title; ?></title>
    <link><?php echo htmlentities($thispage_url); ?></link>
    <description>Geotagged by feedwax.com</description>
<?php
if ($xml->channel) {
foreach ($xml->channel->item as $v) {
	$namespaces = $v->getNameSpaces(true);
	$geo = $v->children($namespaces['geo']); 
	$dc = $v->children($namespaces['dc']);
	if ($dc->subject == 5) { $warning = "#Earthquake "; } 
	elseif ($dc->subject == 6) { $warning = "Strong #earthquake "; }
	elseif ($dc->subject == 7) { $warning = "Severe #earthquake "; }
	elseif ($dc->subject > 7) { $warning = "Massive #earthquake "; }

		echo "<item>\n";
		$title = strip_tags($v->title);
		$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
		echo "     <title>" . $warning . $v->title . "</title>\n";
		echo "     <description>Reported by the USGS on " . htmlspecialchars($v->description) . ".  For more information, visit the USGS web site: " . htmlentities($v->link) . "</description>\n";
		echo "     <link>" . htmlentities($v->link) . "</link>\n";
		echo "     <guid>" . htmlentities($v->link) . "</guid>\n";
		echo "     <pubDate>" . $v->pubDate . "</pubDate>\n";
		echo "     <geo:lat>" . $geo->lat . "</geo:lat>\n";
		echo "     <geo:long>" . $geo->long . "</geo:long>\n";
		echo "</item>\n";
	}
} else {
echo "<item>No results found</item>";
}
?>
    </channel>
</rss>
