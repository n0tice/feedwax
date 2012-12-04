<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

echo "<?xml version=\"1.0\"?>\n";
$seeclickfix_api = "http://seeclickfix.com/api/issues.json?at=" . urlencode($_GET['q']) . "&zoom=10&end=0&page=1&num_results=" . $_GET['count'] . "&sort=issues.created_at";
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/seeclickfixfeeder.php?" . $_SERVER['QUERY_STRING'];
$string .= file_get_contents($seeclickfix_api); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>SeeClickFix.com: <?php echo $_GET['q']; ?></title>
    <link><?php echo "http://" . $_SERVER['SERVER_NAME'] . "/feeders/seeclickfixfeeder.php?" . urlencode($_SERVER['QUERY_STRING']); ?></link>
    <description>Search for <?php echo $_GET['q']; ?></description>

<?php
	$i = 0; 
	foreach ($array as $v) {
		echo "<item>";
		echo "<title>" . htmlspecialchars($v['summary']) . " " . htmlentities($v['bitly']) . "</title>\n";
		echo "<description>";
		echo htmlspecialchars($v['description']) . " - Source: SeeClickFix.com";
		echo "</description>\n";
		echo "<geo:lat>" . htmlentities($v['lat']) . "</geo:lat>\n";
		echo "<geo:long>" . htmlentities($v['lng']) . "</geo:long>\n";
		echo "<link>" . htmlentities($v['bitly']) . "</link>\n";
		echo "<guid>" . htmlentities($v['bitly']) . "</guid>\n";
		$pubdate = strtotime ($v['created_at']);
		echo "<pubDate>" . date("D, d M y H:i:s O", $pubdate) . "</pubDate>";
		$item_api = "http://seeclickfix.com/api/issues/" . $v['id'] . ".json";
		$item_string .= file_get_contents($item_api); // get json content
		$item_array = json_decode($item_string, true); //json decoder
			echo "<media:content url=\"" . $item_array[0]['square_image'] . "\" type=\"image/jpeg\"></media:content>\n";
		echo "</item>\n";
	$i++;
}	
?>
    </channel>
</rss>
