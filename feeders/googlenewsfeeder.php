<?php
include('../config/globals.php');
header('Content-type: application/rss+xml; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
if ($_GET['type'] == "news") {
	$google_url = "https://news.google.com/news/feeds?q=" . urlencode($_GET['q']) . "&hl=en&gl=" . $_GET['gl'] . "&um=1&ie=UTF-8&output=rss";
} elseif($_GET['type'] = "blogs") {
	$google_url = "https://www.google.com/search?q=" . urlencode($_GET['q']) . "&hl=en&tbm=blg&gl=" . $_GET['gl'] . "&um=1&ie=UTF-8&output=rss";
}
$geonames_url = "http://ws.geonames.net/rssToGeoRSS?feedUrl=" . urlencode($google_url) . "&username=" . $geonames_key . "";

if ($_GET['hashtag']) {
	$hashtag = "#" . ($_GET['hashtag']);
}
$thispage_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/anyrssfeeder.php?" . urlencode($_SERVER['QUERY_STRING']) . "&q=" . urlencode($_GET['q']);
$xml = simplexml_load_file($geonames_url); 
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>FeedWax: <?php echo $xml->channel->title; ?></title>
    <link><?php echo htmlentities($google_url); ?></link>
    <description><?php if ($xml->channel->description) {echo 
$xml->channel->description;} 
?> - Geotagged by feedwax.com</description>
<?php
if ($xml->channel) {
foreach ($xml->channel->item as $v) {
	$namespaces = $v->getNameSpaces(true);
	$geo = $v->children($namespaces['geo']); 
	$dc = $v->children($namespaces['dc']);
	if ($_GET['source'] == "googlenews" && $_GET['type'] == "news") {
		parse_str(($v->link), $output);
		$link = htmlentities($output['url']);
	} else {
		$link = htmlentities($v->link);
	}

		if ($geo->lat) {
			echo "<item>\n";
			$title = strip_tags($v->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
			echo "     <title>" . str_replace ( '&', '&amp;', $title );
			if ($dc->creator) {echo " - " . $dc->creator;}
			echo " " . $hashtag . " " . $link . "</title>\n";
			if ($_GET['source'] == "googlenews" && $_GET['type'] == "news") {
				echo "     <description><![CDATA[" . $v->description . "<br><br>".htmlentities($v->link)."]]></description>\n";
			} else {
				echo "     <description><![CDATA[" . htmlspecialchars($v->description) . "<br><br>".htmlentities($v->link)."]]></description>\n";
			}
			if ($v->copyright) {echo "     <copyright>" . $v->copyright . "</copyright>\n";}
			echo "     <link>" . $link . "</link>\n";
			echo "     <guid>" . htmlentities($v->link) . "</guid>\n";
			echo "     <pubDate>" . $v->pubDate . "</pubDate>\n";
			echo "     <geo:lat>" . $geo->lat . "</geo:lat>\n";
			echo "     <geo:long>" . $geo->long . "</geo:long>\n";
			echo "</item>\n";
		}
	}
} else {
echo "<item>No results found</item>";
}
?>
    </channel>
</rss>