<?php
include('../config/globals.php');
include('googlespreadsheetsfunctions.php');
header('Content-type: application/rss+xml; charset=utf-8');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

if ($_GET['hashtag']) {$hashtag = " #".$_GET['hashtag'];}
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>Google spreadsheet RSS feed</title>
    <link>http://feedwax.com/</link>
    <description>Google spreadsheet RSS feed powered by FeedWax</description>
<?php
$z = 0; 
foreach ($newArray as $v) {

	if ((empty($v['lat'])) && (!empty($v['postcode']))) {
	$fulladdress = $v['address'].",".$v['city'].",".$v['postcode'];
		if (is_numeric($v['postcode'])) {	
			$place_url="http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($fulladdress)."&sensor=true";
			$place_string .= file_get_contents($place_url); // get json content
			$place_array_check = json_decode($place_string, true); //json decoder
		} else {
			$place_url="http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($v['postcode'])."&sensor=true";
			$place_string .= file_get_contents($place_url); // get json content
			$place_array_check = json_decode($place_string, true); //json decoder		
		}

		if($place_array_check[status] != "OK") {
			$backup_place_url="https://maps.googleapis.com/maps/api/place/textsearch/json?query=".urlencode($fulladdress)."&sensor=true&key=".$google_key;
			$backup_place_string .= file_get_contents($backup_place_url); // get json content
			$backup_place_array = json_decode($backup_place_string, true); //json decoder
			$place_array = $backup_place_array;
		} else {
			$place_array = $place_array_check;
		}
		$lat = $place_array['results'][0]['geometry']['location']['lat'];
		$long = $place_array['results'][0]['geometry']['location']['lng'];
		$geolat = "     <geo:lat>".$lat."</geo:lat>\n";
		$geolong = "     <geo:long>".$long."</geo:long>\n";
	} elseif ((isset($v['lat'])) && (isset($v['long']))) {
		$geolat = "     <geo:lat>".$v['lat']."</geo:lat>\n";
		$geolong = "     <geo:long>".$v['long']."</geo:long>\n";
	}

	if (!empty($v['image'])) {
		$mediacontent = "     <media:content url=\"" . $v['image'] . "\" type=\"image/jpeg\"></media:content>\n";
	}
	if (!empty($v['tag'])) {
		$tag = " #".$v['tag'];
	}
	
	if (!empty($v['url'])) {
		$originalurl  = $v['url'];
		$urlpieces = explode("http://", $originalurl);
		if ($urlpieces[0] != "") {$rsslink = "http://".$urlpieces[0];} else {$rsslink = "http://".$urlpieces[1];}
	} else {
		$rsslink = "https://maps.google.com/maps?q=".urlencode($v['title'])."+".urlencode($fulladdress)."&amp;hl=en";
	}
	
	if (empty($_GET['title']) && (!empty($v['title']))) {
		$title = "     <title>".str_replace('&', '&amp;', $v['title']).$tag.$hashtag."</title>\n";
	} else {
		$title_value = $_GET['title'];
		$title_data = htmlentities(urldecode($v[$title_value]));
		$title = "     <title>".$title_data.$tag.$hashtag."</title>\n";
		unset($label_value);
	}

	if (empty($_GET['description']) && (!empty($v['description']))) {
		$description = "     <description><![CDATA[".$v['description']."]]></description>\n";
	} elseif (!empty($_GET['description'])) {
		$description_value = $_GET['description'];
		$description_data = htmlentities(urldecode($v[$description_value]));
		$description = "     <description><![CDATA[".$description_data."]]></description>\n";
	} else {
		$description = "     <description> </description>\n";
	}

	$pubdate = date("D, d M Y H:i:s O");

	if ($geolat != "     <geo:lat></geo:lat>") {
	echo "<item>\n";
		echo $title;
		echo $description;
		echo $geolat;
		echo $geolong;
		echo $mediacontent;
		echo "     <pubDate>".$pubdate."</pubDate>\n";
		echo "     <link>".$rsslink."</link>\n";
		echo "     <guid>".$rsslink."</guid>\n";
	echo "</item>\n";
	}
	
unset($fulladdress);
unset($geolat);
unset($geolong);
unset($place_url);
unset($place_string);
unset($place_array_check);
unset($backup_place_url);
unset($backup_place_string);
unset($backup_place_array);
unset($place_array);

$z++;
}
unset($titleformat);

?>
</channel>
</rss>