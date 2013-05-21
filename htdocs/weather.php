<?php

include('config/globals.php');
include('header.php');
include('nav.php');

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

$place_api="http://where.yahooapis.com/geocode?location=$lat,$lng&flags=J&gflags=R&appid=".$yahoo_place_key;
$place_string .= file_get_contents($place_api); // get json content
$place_array = json_decode($place_string, true); //json decoder
#echo $place_api;
#echo $place_array['ResultSet']['Results']['woeid'];

$api_url="http://weather.yahooapis.com/forecastrss?w=" . $place_array['ResultSet']['Results'][0]['woeid'];
#echo $api_url;
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/weatherfeed.php?" . $_SERVER['QUERY_STRING'];
$xml = simplexml_load_file($api_url); 

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
echo "  <tbody>";
	if ($xml->channel) {
		foreach ($xml->channel->item as $v) {
			$namespaces = $v->getNameSpaces(true);
			$geo = $v->children($namespaces['geo']); 
	
			if ($geo->lat) {
				$geocoder_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$geo->lat,$geo->long&sensor=false";
				$geocoder_string .= file_get_contents($geocoder_url); // get json content
				$geocoder_array = json_decode($geocoder_string, true); //json decoder

				echo "<tr><td>";
				echo $v->title . "<br>" . $v->description . "</td>\n";
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
include ('warning.php');
}
echo "  </tbody>";

include('footer.php'); 

?>