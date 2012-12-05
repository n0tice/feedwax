<?php

include('config/globals.php');
include('header.php');
include('nav.php');
$tag = $_GET['q']; 
if ($_GET['client_id']) {
	$client_id = $_GET['client_id']; 
		} else { 
	$client_id = $instagram_key;
}
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$distance = $_GET['distance'];
$count = $_GET['count'];
$not = $_GET['not'];
$defaultloc = $_GET['defaultloc'];

function decouple($query) {
	$query = str_replace('#', '', $query); // remove hash
	return explode(' ', $query);
}
list($query1) = decouple($tag);

?>

<div class="hero-unit">
<h1>Build a feed with Instagram</h1>
<div class="well">
<ul class="nav nav-tabs">
	<li class="active"><a href="instagramtag.php">Search by Tags</a></li>
	<li><a href="instagramgeo.php">Search by Location</a></li>
</ul>
	<form action="" method="GET" class="well">
		Search for a tag: <input type="text" class="span3" value="<?php if($_GET['q']) { echo $query1; } else { echo "n0ticed"; } ?>" name="q" /> (only one tag)<br>
		How many posts in the feed: <input type="text" class="span2" value="<?php if($_GET['count']) { echo $_GET['count']; } else { echo "10"; } ?>" name="count" /><br>
		Default location: <input type="text" class="span2" value="<?php if($_GET['defaultloc']) { echo $_GET['defaultloc']; } else { echo ""; } ?>" name="defaultloc"  placeholder="optional" /> (lat,long)<br>
		<input type="hidden" class="span2" value="<?php if($_GET['not']) { echo $_GET['not']; } else { echo ""; } ?>" name="not" />
	<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
	</form>
</div>
</div>

<?php 
if ($_GET) {
$api_url="https://api.instagram.com/v1/tags/" . urlencode($query1) . "/media/recent?client_id=$client_id&count=$count";
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/instagramfeed.php?" . $_SERVER['QUERY_STRING'];
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder

echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
?>
<div class="alert span7">
  <strong>Photo re-use guidelines:</strong> Instagram expects you to issue a 'Call Out' or contact people directly for permission to republish their photograhs.  The Guardian's <a href="http://www.guardian.co.uk/music/interactive/2012/nov/23/live-music-map-gig-photos-twitter">#GdnGig Live Music Map</a> is a useful demonstration of a 'Call Out' to a community of participants.
</div>
<?php
echo "<button class=\"btn btn-large btn-primary span4 align-right\" type=\"submit\">feed this into n0tice</button>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">";

echo " <thead>";
echo "    <tr>";
echo "      <th>Results</th>";
echo "      <th>Image</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody class=\"well\">";

	$i = 0; 
	if ($array['meta']['code'] == "200") {
		foreach ($array['data'] as $v) {
			if ($v['caption']) {
				if (empty($v['location']['latitude'])) {
					echo "<tr><td>";
					$locationmsg = "Location not found<br>";				
				} else {
					echo "<tr class=\"success\"><td>";
					$locationmsg = "Location: " . $v['location']['latitude'] . "," . $v['location']['longitude'] . "<br>";
				}
				echo htmlspecialchars($v['caption']['text']) ."<br>\n";
				echo "<a href=\"" . $v['link'] . "\">" . $v['link'] . "</a><br>\n";
				echo $locationmsg;
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

<?php include('footer.php'); ?>