<?php

include('../config/globals.php');
header('Content-type: application/rss+xml; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

$api_url="http://content.guardianapis.com/tone/livereview?format=json&section=music&show-fields=all&show-media=all&show-factboxes=all&order-by=newest&show-redistributable-only=true&page-size=8&api-key=".$guardian_key;
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder

?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>The Guardian - <?php echo $array['response']['tag']['webTitle'] ?></title>
    <link><?php echo $array['response']['tag']['webUrl'] ?></link>
    <description>The Guardian - <?php echo $array['response']['tag']['webTitle'] ?> geotagged by FeedWax</description>

<?php

$i = 0; 
foreach ($array['response']['results'] as $v) {
	echo "<item>\n";
	echo "<title>" . htmlspecialchars($v['webTitle']) . " : " . strip_tags($v['fields']['standfirst']) . "</title>\n";
	if ($v['mediaAssets']) {
		$j = 0; 
		foreach ($array['response']['results'][$i]['mediaAssets'] as $z) {
		echo $z[$j]['fields'];
			if (in_array("480",$z['fields'])) {
				echo "<media:content url=\"" . $z['file'] . "\" type=\"image/jpeg\"></media:content>\n";
				$photo_credit = "Photo by ".$z['fields']['credit'];
			}
		$j++;
		}
	}
	echo "<description><![CDATA[";
	echo htmlspecialchars(strip_tags($v['fields']['trailText']));
	if ($photo_credit) {echo ". ".$photo_credit;}
	echo ". ".$v['webUrl'];
	echo "]]></description>\n";
	#echo "<media:content url=\"" . $v['fields']['thumbnail'] . "\" type=\"image/jpeg\"></media:content>\n";
	echo "<link>". $v['webUrl'] . "</link>\n";
	echo "<guid>". $v['webUrl'] . "</guid>\n";
		$place_url="https://maps.googleapis.com/maps/api/place/textsearch/json?query=".urlencode($v['fields']['standfirst'])."&sensor=true&key=".$google_key;
		$place_string .= file_get_contents($place_url); // get json content
		$place_array = json_decode($place_string, true); //json decoder
		$lat = $place_array['results'][0]['geometry']['location']['lat'];
		$long = $place_array['results'][0]['geometry']['location']['lng'];
		echo "<geo:lat>".$lat."</geo:lat>\n";
		echo "<geo:long>".$long."</geo:long>\n";
		unset($lat);
		unset($long);
		unset($place_url);
		unset($place_string);
		unset($place_array);
	echo "<pubDate>" . gmdate(DATE_RSS, strtotime($v['webPublicationDate'])) . "</pubDate>";
	echo "</item>\n";
	unset($photo_credit);		
	$i++;
}

?>
    </channel>
</rss>
