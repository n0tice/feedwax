<?php

include('config/globals.php');
include('header.php');
include('nav.php');

if ($_GET['client_id']) {
	$client_id = $_GET['client_id']; 
		} else { 
	$client_id = $instagram_key;
}
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$count = $_GET['count'];

?>

<div class="hero-unit">
<h1>Build a feed with Instagram</h1>
<ul class="nav nav-tabs">
	<li><a href="instagramtag.php">Search by Tags</a></li>
	<li class="active"><a href="instagramgeo.php">Search by Location</a></li>
</ul>
<div class="well">
<table class="table">
<tr><td><form action="" method="GET" class="well">
    Lat: <input type="text" class="text" value="<?php echo $lat; ?>" name="lat" /><br>
    Long: <input type="text" class="text" value="<?php echo $lng; ?>" name="lng" /><br>

<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form></td>
<td>
  Find Place: <input type="text" class="input-medium" id="address"/><input type="button" value="Go" onclick="geocode()">
  <div id="map">
    <div id="map_canvas" style="width:100%; height:200px"></div>
    <div id="crosshair"></div>
  </div>

  <table>
    <tr><td>Lat/Lng:</td><td><div id="latlng"></div></td></tr>
    <tr><td>Address:</td><td><div id="formatedAddress"></div></td></tr>
  </table>
</td></tr>
</table>
</div>
</div>

<?php 
if ($_GET) {

$place_api="https://api.instagram.com/v1/locations/search?client_id=$client_id&lat=$lat&lng=$lng";
$place_string .= file_get_contents($place_api); // get json content
$place_array = json_decode($place_string, true); //json decoder

$api_url="https://api.instagram.com/v1/locations/" . $place_array['data'][0]['id'] . "/media/recent?client_id=$client_id&count=$count";
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/instagramgeofeed.php?" . $_SERVER['QUERY_STRING'];
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder

include ('policy-change.php');
#echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
#echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-disabled\" type=\"submit\"><strike>import into n0tice now</strike></button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
#echo "</form>";
?>
<!--<div class="alert span7">
  <strong>Photo re-use guidelines:</strong> Instagram expects you to issue a 'Call Out' or contact people directly for permission to republish their photograhs.  The Guardian's <a href="http://www.guardian.co.uk/music/interactive/2012/nov/23/live-music-map-gig-photos-twitter">#GdnGig Live Music Map</a> is a useful demonstration of a 'Call Out' to a community of participants.
</div>-->
<?php

echo "<table class=\"table\" width=\"100%\">";

echo " <thead>";
echo "    <tr>";
echo "      <th>Title</th>";
echo "      <th>Image</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody>";

	$i = 0; 
	if ($array['meta']['code'] == "200") {
		foreach ($array['data'] as $v) {
			if ($v['caption'] && $v['location']['latitude']) {
				echo "<tr><td>";
				echo htmlspecialchars($v['caption']['text']) ."<br>\n";
				echo "<a href=\"" . $v['link'] . "\">" . $v['link'] . "</a><br>\n";
				echo "Location: " . $v['location']['latitude'] . "," . $v['location']['longitude'] . "<br>";
				echo date("D, d M y H:i:s O", $v['caption']['created_time']);
				echo "</td><td><img src=\"" . $v['images']['standard_resolution']['url'] . "\"></td></tr>\n";
				$i++;
			}
		}
	}
echo "  </tbody>";
echo "  </table>";
include ('warning.php');
}
?>

<p class="text-warning">This product uses the Instagram API but is not endorsed or certified by Instagram.</p>

<?php 
echo "<a href=\"http://feedton0tice.com/feeds/new?url=" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"10\" align=\"right\"></a>";
include('footer.php'); 
?>