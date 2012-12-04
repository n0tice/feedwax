<?php
if ($_GET['q']) {
	$q = urlencode($_GET['q']); 
		} else { 
	$q = "n0ticed";
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
	$radius = $_GET['radius']; 
		} else { 
	$radius = "10";
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
<h1>Build a feed with Flickr</h1>
<table class="table">
<tr><td><form action="" method="GET" class="well">
    Search Terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo $_GET['q']; } else { echo ""; } ?>" name="q" /><br>
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
if ($_GET) {
$secret = $flickr_secret;
$api_key = $flickr_key;
$params = "accuracy11api_key" . $api_key . "extrasdescriptiongeoformatjsonhas_geo1lat" . $lat . "license4567lon" . $long . "methodflickr.photos.searchnojsoncallback1per_page" . $maxresults . "radius" . $radius . "sortdate-posted-desctags" . $q . "text" . $q;
$sigmaker = $secret.$params;
$api_sig = md5($sigmaker);
$flickr_url = "http://api.flickr.com/services/rest/?method=flickr.photos.search&accuracy=11&api_key=$api_key&extras=date_upload,description,geo,last_update,url_z&format=json&has_geo=1&lat=$lat&license=4,5,6,7&lon=$long&nojsoncallback=1&per_page=$maxresults&radius=$radius&sort=date-posted-desc&tags=$q&text=$q";
$string .= file_get_contents($flickr_url); // get json content
$array = json_decode($string, true); //json decoder
$n0ticefeed_url="http://" . $_SERVER['SERVER_NAME'] . "/feeders/flickrfeed.php?" . $_SERVER['QUERY_STRING'];

echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-primary\" type=\"submit\">feed this into n0tice</button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">\n";
echo " <thead>\n";
echo "    <tr>\n";
echo "      <th>Title</th>\n";
echo "      <th>Image</th>\n";
echo "    </tr>\n";
echo "  </thead>\n";
echo "  <tbody class=\"well\">\n";


	$i = 0; 
	if ($array['photos']['total'] != 0) {
		foreach ($array['photos']['photo'] as $v) {
			echo "<tr><td>";
			echo $v['title'] . "<br>Date: " . date("D, d M y H:i:s O", $v['dateupload']) . "<br>Location: " . $v['latitude'] . "," . $v['longitude'] . "</td><td><img src=\"" . $v['url_z'] . "\">";
			echo "</td></tr>\n";
			$i++;
		}
	} else {
		if ($array['stat'] == "fail") {
			echo $array['message'];
		} else {
			echo "no images found...try another search\n";
		}
	}
echo "  </tbody>\n";
echo "  </table>\n";
include ('warning.php');
}

?>

<?php include('footer.php'); ?>