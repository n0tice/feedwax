<?php
include('config/globals.php');
include('header.php');
include('nav.php');
$url = urlencode($_GET['url']);
?>

<div class="hero-unit">
<h1>Build a feed with Google</h1>
<form action="" method="GET" class="well">
    Search Terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo urldecode($_GET['q']); } else { echo ""; } ?>" name="q" /><br>
    <!-- Country: <input type="text" class="text" value="<?php if($_GET['gl']) { echo $_GET['gl']; } else { echo "uk"; } ?>" name="gl" />(two-letter code, ie 'uk')<br> -->
    <input type="hidden" name="type" value="news" >
<!--
	<input type="radio" name="type" value="news" <?php if ($_GET['type'] == "news" || ($_GET['type']) == "") { echo "checked"; } ?>>&nbsp;&nbsp;News Sources&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" name="type" value="blogs" <?php if ($_GET['type'] == "blogs") { echo "checked"; } ?>>&nbsp;&nbsp;Blogs <br><br>
-->
<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
if ($_GET) {

if($_GET['type'] == "blogs") {
		$google_url = "https://www.google.com/search?q=" . urlencode($_GET['q']) . "&hl=en&tbm=blg&gl=" . $_GET['gl'] . "&um=1&ie=UTF-8&output=rss";
} else {
		$google_url = "https://news.google.com/news/feeds?q=" . urlencode($_GET['q']) . "&hl=en&gl=" . $_GET['gl'] . "&um=1&ie=UTF-8&output=rss";
}
$geonames_url = "http://ws.geonames.net/rssToGeoRSS?feedUrl=".urlencode($google_url)."&username=".$geonames_key;
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/googlenewsfeeder.php?url=" . urlencode($google_url) . "&q=" . urlencode($_GET['q']) . "&source=googlenews&type=" . $_GET['type'] . "&LinkInTitle=true";
$xml = simplexml_load_file($geonames_url); 
#echo $geonames_url;
include ('policy-change.php');
#echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
#echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
#echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-disabled\" type=\"submit\"><strike>import into n0tice now</strike></button></td>";
#echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
#echo "</form>";
#echo "<div class=\"navbar\">\n";
#echo "    <a class=\"brand\" href=\"\">Results</a> \n";
#echo "    <ul class=\"nav nav-tabs pull-right\">\n";
#echo "      <li><a href=\"rss-precise.php?url=". $_GET['url'] . "\">Strict</a></li>\n";
#echo "      <li class=\"active\"><a href=\"rss-loose.php?url=". $_GET['url'] . "\">Loose</a></li>\n";
#echo "    </ul>\n";
#echo "</div>\n";
echo "<table class=\"table\" width=\"100%\">";
echo "  <tbody class=\"well\">";
	if ($xml->channel) {
		foreach ($xml->channel->item as $v) {
			$namespaces = $v->getNameSpaces(true);
			$geo = $v->children($namespaces['geo']); 
	
			if ($geo->lat) {
				$geocoder_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$geo->lat,$geo->long&sensor=false";
				$geocoder_string .= file_get_contents($geocoder_url); // get json content
				$geocoder_array = json_decode($geocoder_string, true); //json decoder

				echo "<tr><td>";
				echo $v->title . "</td>\n";
				echo "<td><a href=\"https://maps.google.co.uk/maps?q=$geo->lat,$geo->long&ll=$geo->lat,$geo->long&z=10\">" . $geocoder_array['results'][0]['formatted_address'] . "</a>";
				echo " ";
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
echo "<tr><td colspan=2>* If we couldn't get your location the first time, please check again in a few minutes.  For more information on how we identify locations, read more about FeedWax <a href=\"about.php\">here</a>.</td></tr>";
echo "  </tbody></table>";
include ('warning.php');
}

echo "<a href=\"http://feedton0tice.com/feeds/new?url=" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"10\" align=\"right\"></a>";
include('footer.php'); 

?>