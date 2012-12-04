<?php

include('config/globals.php');
include('header.php');
include('nav.php');

$q = $_GET['q'];
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$count = $_GET['count'];

?>

<div class="hero-unit">
<h1>Build a weather feed</h1>
<div class="well">
<table class="table">
<tr><td><form action="" method="GET" class="well">
    Lat: <input type="text" class="text" value="<?php echo $lat; ?>" name="lat" /><br>
    Long: <input type="text" class="text" value="<?php echo $lng; ?>" name="lng" /><br>

<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form></td>
<td>
  Find Place: <input type="text" id="address"/><input type="button" value="Go" onclick="geocode()">
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
$timestamp = gmdate('U'); // 1200603038   
$sig = md5($patch_key . $patch_secret . time()); 
$api_url="http://news-api.patch.com/v1.1/nearby/$lat,$lng/stories?dev_key=$patch_key&keyword=$q&include-locations=true&sig=$sig";
echo $api_url;
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/patchfeed.php?" . $_SERVER['QUERY_STRING'];
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder

print_r($array);

echo "<b>Feed URL:</b> " . $n0ticefeed_url;
echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-primary\" type=\"submit\">feed this into n0tice</button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
echo "</form>";

echo "<h2>Sample Results</h2>";
echo "<p class=\"text-warning\"><em><b>NOTE:</b> If you intend to publish the content of this feed somewhere then it's important<br>that you have obtained permission to redistribute the content from the copyright owner.</em></p>";
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
}
?>

<?php include('footer.php'); ?>