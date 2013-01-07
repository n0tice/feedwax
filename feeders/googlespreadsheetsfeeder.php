<?php
header('Content-type: application/rss+xml; charset=utf-8');
include('../config/globals.php');
include('../config/cacheheader.php');
include('googlespreadsheetsfunctions.php');


echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>Google spreadsheet RSS feed</title>
    <link><?php echo urldecode($_GET['spreadsheet_url']); ?></link>
    <description>Google spreadsheet RSS feed powered by FeedWax</description>
<?php
$z = 0; 
foreach ($newArray as $v) {
	if ((isset($v['lat'])) && (isset($v['long']))) {
		$geolat = "     <geo:lat>".$v['lat']."</geo:lat>\n";
		$geolong = "     <geo:long>".$v['long']."</geo:long>\n";
	} elseif (isset($v['postcode'])) {
		$fulladdress = $v['address'].",".$v['city'].",".$v['postcode'];
		if (is_numeric($v['postcode'])) {	
			$place_url="https://maps.googleapis.com/maps/api/place/textsearch/json?query=".urlencode($fulladdress)."&keyword=".urlencode($v['address'])."&sensor=true&key=".$google_key;
		} else {
			$place_url="https://maps.googleapis.com/maps/api/place/textsearch/json?query=".urlencode($v['postcode'])."&keyword=".urlencode($fulladdress)."&sensor=true&key=".$google_key;
		}
		$place_string .= file_get_contents($place_url); // get json content
		$place_array = json_decode($place_string, true); //json decoder		
		$lat = $place_array['results'][0]['geometry']['location']['lat'];
		$long = $place_array['results'][0]['geometry']['location']['lng'];
		$geolat = "     <geo:lat>".$lat."</geo:lat>\n";
		$geolong = "     <geo:long>".$long."</geo:long>\n";
	}

	if ($_GET['hashtag']) {$hashtag = " #".$_GET['hashtag'];}

	if (!empty($v['tag'])) {
		$tag = " #".$v['tag'];
	}
	
	$pubdate = date("D, d M Y H:i:s O");

	echo "<item>\n";
		echo "     <title>";
		if (empty($_GET['title'])) {
			echo str_replace('&', '&amp;', $v['title']);
		} elseif (isset($_GET['title'])) {
			foreach ($labels as $label) {
				for($num=0;$num<count($_GET["title"]);$num++){
					if ($_GET["title"][$num] == $label) {
						echo $v[$label]." ";
					}
				}
			}		
		}
		echo $tag.$hashtag;
		echo "</title>\n";
		echo "     <description><![CDATA[";
		if (empty($_GET['description'])) {
			echo $v['description'];
		} elseif (isset($_GET['description'])) {
			foreach ($labels as $label) {
				for($num=0;$num<count($_GET["description"]);$num++){
					if ($_GET["description"][$num] == $label) {
						echo $v[$label]." ";
					}
				}
			}		
		}
		echo "]]></description>\n";
		echo $geolat;
		echo $geolong;

		if (empty($_GET['image'])) {
			$imageurl = $v['image'];
		} elseif (isset($_GET['image'])) {
			foreach ($labels as $label) {
				if ($_GET['image'] == $label) {
					$imageurl = $v[$label];
				}
			}		
		}
		if ($imageurl != "") {
			echo "     <media:content url=\"" . $imageurl . "\" type=\"image/jpeg\"></media:content>\n";
		}	
		if (empty($_GET['url'])) {
			$originalurl = $v['url'];
			$urlpieces = explode("http://", $originalurl);
			if ($urlpieces[0] != "") {$rsslink = "http://".$urlpieces[0];} else {$rsslink = "http://".$urlpieces[1];}
		} elseif (isset($_GET['url'])) {
			foreach ($labels as $label) {
				if ($_GET['url'] == $label) {
					$originalurl = $v[$label];
				}
			}		
			$urlpieces = explode("http://", $originalurl);
			if ($urlpieces[0] != "") {$rsslink = "http://".$urlpieces[0];} else {$rsslink = "http://".$urlpieces[1];}
		}
		echo "     <link>".$rsslink."</link>\n";
		echo "     <guid>".$rsslink."</guid>\n";
		echo "     <pubDate>".$pubdate."</pubDate>\n";
	echo "</item>\n";

unset($place_url);
unset($place_string);
unset($place_array);

$z++;
}

?>
</channel>
</rss>