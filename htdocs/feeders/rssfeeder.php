<?php
include('../config/globals.php');
include('../header.php');
include('../nav.php');
$url = urlencode($_GET['url']);
?>

<div class="hero-unit">
<h1>Geocode your RSS feed</h1>
<form action="" method="GET" class="well">
    RSS feed URL: <input type="text" class="text" value="<?php if($_GET['url']) { echo $_GET['url']; } else { echo ""; } ?>" name="url" /><br>
<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
$geonames_url = "http://ws.geonames.net/rssToGeoRSS?feedUrl=$url&username=" . $geonames_key . "";
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/anyrssfeeder.php?url=" . $url;
$xml = simplexml_load_file($geonames_url); 

if ($_GET) {
echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-primary\" type=\"submit\">feed this into n0tice</button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">";
echo " <thead>";
echo "    <tr>";
echo "      <th>Title</th>";
echo "      <th>Location</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody class=\"well\">";
	if ($xml->channel) {
		foreach ($xml->channel->item as $v) {
			$namespaces = $v->getNameSpaces(true);
			$geo = $v->children($namespaces['geo']); 
			$textsearch = urlencode(strip_tags($v->title));
			$textsearch_url = "http://ws.geonames.net/searchJSON?operator=OR&maxRows=3&fuzzy=0.8&username=" . $geonames_key . "&q=" . $textsearch . "";
			$textsearch_string .= file_get_contents($textsearch_url); // get json content
			$textsearch_array = json_decode($textsearch_string, true); //json decoder

			if ($geo->lat) {
				$geocoder_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$geo->lat,$geo->long&sensor=false";
				$geocoder_string .= file_get_contents($geocoder_url); // get json content
				$geocoder_array = json_decode($geocoder_string, true); //json decoder

				echo "<tr><td>";
				echo $v->title . "</td>\n";
				echo "<td><a href=\"https://maps.google.co.uk/maps?q=$geo->lat,$geo->long&ll=$geo->lat,$geo->long&z=10\">" . $geocoder_array['results'][0]['formatted_address'] . "</a>";
				echo " GEONAMES:" . $textsearch_array['geonames'][0]['name'];
				echo "</td></tr>\n";
				unset($geocoder_url);
				unset($geocoder_string);
				unset($geocoder_array);
				}
			}
	} else {
				echo "<tr><td colspan=\"2\">";
				echo "No results found.  Try again.";
				echo "</td></tr>\n";
	}	
echo "  </tbody></table>";
include ('../warning.php');
}

include('../footer.php'); 

?>