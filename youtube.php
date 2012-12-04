<?php
if ($_GET['q']) {
	$q = utf8_encode(urlencode($_GET['q'])); 
		} else { 
	$q = "n0ticed";
}
if ($_GET['key']) {
	$key = $_GET['key']; 
		} else { 
	$key = $youtube_key;
}
if ($_GET['lat']) {
	$lat = $_GET['lat']; 
		} else { 
	$lat = "51.534631";
}
if ($_GET['long']) {
	$long = $_GET['long']; 
		} else { 
	$long = "-0.121965";
}
if ($_GET['radius']) {
	$radius = $_GET['radius'] . "km"; 
		} else { 
	$radius = "10km";
}
if ($_GET['maxresults']) {
	$maxresults = $_GET['maxresults']; 
		} else { 
	$maxresults = "10";
}

include('config/globals.php');
include('header.php');
include('nav.php');
?>

<div class="hero-unit">
<h1>Build a feed with YouTube</h1>
<table class="table">
<tr><td><form action="" method="GET" class="well">
    Search terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo $_GET['q']; } else { echo "n0ticed"; } ?>" name="q" /><br>
    How many posts in the feed: <input type="text" class="text" value="<?php if($_GET['maxresults']) { echo $_GET['maxresults']; } else { echo "10"; } ?>" name="maxresults" /><br>
    Lat: <input type="text" class="text" value="<?php echo $lat; ?>" name="lat" /><br>
    Long: <input type="text" class="text" value="<?php echo $long; ?>" name="long" /><br>
    Radius: <input type="text" class="text" value="<?php if($_GET['radius']) { echo $_GET['radius']; } else { echo "10"; } ?>" name="radius" />(km)<br>
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

<?php 
$georssfeed_url = "https://gdata.youtube.com/feeds/api/videos?q=$q&orderby=published&max-results=$maxresults&v=2&time=this_week&location=$lat,$long&location-radius=$radius&genre=7&duration=short&key=" . $youtube_key;
$api_url="https://gdata.youtube.com/feeds/api/videos?q=$q&orderby=published&max-results=$maxresults&v=2&alt=json&time=this_week&location=$lat,$long&location-radius=$radius&genre=7&duration=short&key=" . $youtube_key;
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
$n0ticefeed_url="http://" . $_SERVER['SERVER_NAME'] . "/feeders/youtubefeed.php?" . $_SERVER['QUERY_STRING'];

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
echo "      <th>Thumbnail</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody>";

	$i = 0; 
	if ($array['feed']['openSearch$totalResults']['$t'] != 0) {
		foreach ($array['feed']['entry'] as $v) {
				echo "<tr><td>";
				echo htmlspecialchars($v['title']['$t']) . "<br>\n";
				echo htmlspecialchars($v['media$description']['$t']) . "<br>\n";
				echo $v['link'][0]['href'] . "<br>\n";
				$pubdate = strtotime($v['published']['$t']);
				echo date("D, d M y H:i:s O", $pubdate) . "<br>\n";
				echo "By " . $v['author'][0]['name']['$t'] . "<br>\n";
				echo "</td><td>";
				echo "<a href=\"" . $v['link'][0]['href'] . "\"><img src=\"" . $v['media$group']['media$thumbnail'][1]['url'] . "\"></a></td></tr>\n";
				$i++;
			} 
	} else {
		echo "no videos found...try another search";
	}
echo "  </tbody>";
echo "  </table>";
include ('warning.php');
}
?>

<?php include('footer.php'); ?>