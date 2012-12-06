<?php
if ($_GET['accountKey']) {
	$accountKey = $_GET['accountKey']; 
		} else { 
	$accountKey = $twitter_key;
}
if ($_GET['lat']) {
	$lat = $_GET['lat']; 
		} else { 
	$lat = "";
}
if (empty($_GET['q'])) {
	$q = "pic.twitter.com";
		} else { 
	$q = $_GET['q']; 
}
if ($_GET['long']) {
	$long = $_GET['long']; 
		} else { 
	$long = "";
}
if ($_GET['radius']) {
	$radius = $_GET['radius']; 
		} else { 
	$radius = "5";
}
if ($_GET['assignedlat']) {
	$assignedlat = $_GET['assignedlat']; 
		} else { 
	$assignedlat = "";
}
if ($_GET['assignedlong']) {
	$assignedlong = $_GET['assignedlong']; 
		} else { 
	$assignedlong = "";
}

$radiusmi = $radius . "mi";
if ($lat && $long) {$geocode = "&geocode=$lat,$long,$radiusmi";}
if ($_GET['assignedlat'] && $_GET['assignedlong']) {$altgeocode = "&altgeocode=".$_GET['assignedlat'].",".$_GET['assignedlong'];}

if ($_GET['rpp']) {
	$rpp = $_GET['rpp']; 
		} else { 
	$rpp = "20";
}
if ($_GET['media']) {
	$media = $_GET['media']; 
		} else { 
	$media = "all";
}
if ($_GET['defaultloc']) {
	$defaultloc = $_GET['defaultloc']; 
		} else { 
	$defaultloc = "exclude";
}

include('config/globals.php');
include('header.php');
include('nav.php');
?>

<div class="hero-unit">
<h1>Build a feed with Twitter</h1>
<table class="table">
<tr><td><form action="" method="GET" class="well">
    Search terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo $q; } else { echo ""; } ?>" name="q" placeholder="#hashtag pic.twitter.com -RT" /><br>
    Media: <input type="radio" class="radio" value="all" name="media" <?php if($media != "images") {echo "checked";} ?>/> All posts <input type="radio" class="text" value="images" name="media" <?php if($media == "images") {echo "checked";} ?>/> Posts wth images<br>
	Number of tweets: <input type="text" class="input-mini" size="10" value="<?php if($_GET['rpp']) { echo $_GET['rpp']; } else { echo "20"; } ?>" name="rpp" /> (max 100)<br>
    Lat: <input type="text" class="input-small" value="<?php echo $lat; ?>" name="lat" /> (eg 51.534631)<br>
    Long: <input type="text" class="input-small" value="<?php echo $long; ?>" name="long" />( eg -0.121965)<br>
    Radius: <input type="text" class="input-mini" value="<?php echo $radius; ?>" name="radius" />(in miles)<br>
<hr>
	Unlocated tweets:<br><input type="radio" class="radio" value="exclude" name="defaultloc" <?php if($defaultloc != "assign") {echo "checked";} ?>/> Exclude <input type="radio" class="text" value="assign" name="defaultloc" <?php if($defaultloc == "assign") {echo "checked";} ?>/> Assign<br>
    Assigned Lat: <input type="text" class="input-small" value="<?php echo $assignedlat; ?>" name="assignedlat" /><br>
    Assigned Long: <input type="text" class="input-small" value="<?php echo $assignedlong; ?>" name="assignedlong" /><br>
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

$twitterapi_url="https://search.twitter.com/search.json?q=" . urlencode($q) . $geocode . "&include_entities=true&result_type=mixed&rpp=$rpp";
$n0ticefeed_url="http://" . $_SERVER['SERVER_NAME'] . "/feeders/twitterfeed.php?q=" . urlencode($_GET['q']) . $geocode . $altgeocode . "&rpp=$rpp&media=$media&defaultloc=$defaultloc";
$string .= file_get_contents($twitterapi_url); // get json content
$array = json_decode($string, true); //json decoder

echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
?>
<div class="alert span7">
  <strong>Photo re-use guidelines:</strong> You may wish to issue a 'Call Out' or contact people directly for permission to republish their photograhs.  The Guardian's <a href="http://www.guardian.co.uk/music/interactive/2012/nov/23/live-music-map-gig-photos-twitter">#GdnGig Live Music Map</a> is a useful demonstration of a 'Call Out' to a community of participants.
</div>
<?php
echo "<button class=\"btn btn-large btn-primary span4 align-right\" type=\"submit\">feed this into n0tice</button>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">";

echo " <thead>";
echo "    <tr>";
echo "      <th>Results</th>";
echo "      <th width=\"200\">Location, if available</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody class=\"well\">";

if($array['results']) {
	$i = 0; 
	foreach ($array['results'] as $v) {
	if ($media == "images") {
		$filter = $v['entities']['media'];
	} else {
		$filter = $v['entities'];
	}
			if ($filter && $v['geo'] && ($v['geo']['coordinates'][0] != "0")) {
				echo "<tr class=\"success\"><td>";
				if($v['entities']['media']){echo "<img src=\"".$v['entities']['media'][0]['media_url']."\" width=\"150\" align=\"right\">";}
				echo utf8_encode(htmlentities($v['text'])) . " <a href=\"http://twitter.com/".$v['from_user']."/status/".$v['id']."\">(link)</a></td><td>";
				echo "location: " . $v['geo']['coordinates'][0] . "," . $v['geo']['coordinates'][1];
				echo "</td></tr>\n";
			} elseif($filter) {
				echo "<tr><td>";
				if($v['entities']['media']){echo "<img src=\"".$v['entities']['media'][0]['media_url']."\" width=\"150\" align=\"right\">";}
				echo htmlspecialchars($v['text']) . " <a href=\"http://twitter.com/".$v['from_user']."/status/".$v['id']."\">(link)</a></td>";
				if($defaultloc != "assign") {echo "<td>(No location...to be excluded)";}
				else {echo "<td>(No location found. Assigning: ".$assignedlat.",".$assignedlong.")";}
				echo "</td></tr>\n";
			}
		$i++;
	}
} else {
				echo "<tr><td colspan=2>";
				echo "Sorry, we were unable to find any geotagged tweets with your search query. Try another search?";
				echo "</td></tr>\n";
}
echo "  </tbody>";
echo "  </table>";
include ('warning.php');
}
?>

<?php include('footer.php'); ?>