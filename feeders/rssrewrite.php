<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
function string_to_lat_lon($string) {
	$string = str_replace('(', '', $string); // remove leading bracket
	$string = str_replace(')', '', $string); // remove trailing bracket
	return explode(', ', $string);
}
echo "<?xml version=\"1.0\"?>";

if ($_GET['accuracy'] == "precise") {
		$url = urldecode($_GET['url']);
	} else {
		$url = "http://ws.geonames.net/rssToGeoRSS?feedUrl=" . urldecode($_GET['url']) . "&username=".$geonames_key;
}
$thispage_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/rssrewrite.php?" . urlencode($_SERVER['QUERY_STRING']);
$xml = simplexml_load_file($url); 
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>FeedWax: <?php echo $xml->channel->title; ?></title>
    <link><?php echo htmlentities($thispage_url); ?></link>
    <description><?php if ($xml->channel->description) {echo $xml->channel->description;} ?> - Geotagged by FeedWax.com</description>

<?php
$n = 0;
foreach ($xml->channel->item as $v) {
if ($n < 6) {
	$namespaces = $v->getNameSpaces(true);
	$dc = $v->children($namespaces['dc']);
	$geo = $v->children($namespaces['geo']);
	$link = urlencode($v->link);
	if ($_GET['hashtag']) {$hashtag = "#" . $_GET['hashtag'];}
	$hint = urldecode($_GET['hint']);
	echo "<item>\n";
	$title = strip_tags($v->title);
	$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
	echo "     <title>";
		echo str_replace ( '&', '&amp;', $title );
		if ($dc->creator) {echo " - " . $dc->creator;}
		echo " " . $hashtag;
		if ($_GET['LinkInTitle'] == "true") {echo " " . htmlentities($v->link);}
	echo "</title>\n";
	echo "     <description>";
		if ($_GET['hint']) {echo $_GET['hint'] . ": ";}				
		echo htmlspecialchars(strip_tags($v->description, '<a>'));
	echo "</description>\n";
	if ($v->copyright) {echo "     <copyright>" . $v->copyright . "</copyright>\n";}
	echo "     <link>" . htmlentities($v->link) . "</link>\n";
	echo "     <guid>" . htmlentities($v->link) . "</guid>\n";
	echo "     <pubDate>" . $v->pubDate . "</pubDate>\n";
	if (!$geo->lat && !$geo->long) {
		$extract = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20contentanalysis.analyze%20where%20url%3D%22$link%22&format=json";
		$extract_string .= file_get_contents($extract); // get json content
		$extract_array = json_decode($extract_string, true); //json decoder
		#echo htmlentities($extract) . "\n";
		#echo $extract_array['query']['results']['entities']['entity']['0']['score'] . "\n";
		if ($extract_array['query']['results']) {
		#echo $extract_array['url'];
			$i = 0;
			foreach ($extract_array['query']['results']['entities']['entity'] as $v) {
				if ($v['metadata_list']['metadata']['woe_id'] && $i == "0") {
				list($lon,$lat) = string_to_lat_lon($v['metadata_list']['metadata']['geo_location']);
				
					echo "<geo:lat>" . $lat . "</geo:lat>\n";
					echo "<geo:long>" . $lon . "</geo:long>\n";
				$i++;
				unset($lat);
				unset($long);
				}
			}
		}
		unset($i);
		unset($extract);
		unset($extract_string);
		unset($extract_array);
	} else {
		echo "<geo:lat>" . $geo->lat . "</geo:lat>\n";
		echo "<geo:long>" . $geo->long . "</geo:long>\n";	
	}

	echo "</item>\n";
$n++;
}

}
?>
    </channel>
</rss>
