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
	$cleantag = str_replace('#', '', $query); // remove hash
	return explode(' ', $cleantag);
}
$matches = decouple($tag);
$cleantag1 = $matches[0];
$cleantag2 = $matches[1];
?>

<div class="hero-unit">
<h1>Build a feed with Instagram</h1>
<div class="well">
<ul class="nav nav-tabs">
	<li class="active"><a href="instagramtag.php">Search by Tags</a></li>
	<li><a href="instagramgeo.php">Search by Location</a></li>
</ul>
	<form action="" method="GET" class="well">
		Tags: <input type="text" class="span3" value="<?php if($_GET['q']) { echo $cleantag1." ".$cleantag2; } else { echo ""; } ?>" name="q" placeholder="hashtag"/><br>
		Assign default location to unlocated Instagrams:<br><input type="text" class="input-xlarge" value="<?php if($_GET['defaultloc']) { echo $_GET['defaultloc']; } else { echo ""; } ?>" name="defaultloc"  placeholder="optional" /> (lat,long)<br>
		<input type="hidden" value="<?php if($_GET['count']) { echo $_GET['count']; } else { echo "10"; } ?>" name="count" />
		<input type="hidden" value="<?php if($_GET['not']) { echo $_GET['not']; } else { echo ""; } ?>" name="not" />
	<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
	</form>
</div>
</div>

<?php 
if ($_GET) {
$instagram_cleantag1="https://api.instagram.com/v1/tags/".urlencode($cleantag1)."/media/recent?client_id=$client_id&count=$count";
$string_cleantag1 .= file_get_contents($instagram_cleantag1); // get json content
$array_cleantag1 = json_decode($string_cleantag1, true); //json decoder

if (!empty($cleantag2)) {
	$instagram_cleantag2="https://api.instagram.com/v1/tags/" . urlencode($cleantag2) . "/media/recent?client_id=$client_id&count=$count";
	$string_cleantag2 .= file_get_contents($instagram_cleantag2); // get json content
	$array_cleantag2 = json_decode($string_cleantag2, true); //json decoder
	
	$array_hashtags = array_merge($array_cleantag1, $array_cleantag2);
	$onetag = "false";
} else {
	$array_hashtags = $array_cleantag1;
	$onetag = "true";
}

$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/instagramfeed.php?" . $_SERVER['QUERY_STRING'];

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
foreach ($array_hashtags['data'] as $v) {
$array_tags = $v['tags'];
	if ($onetag == "true") {
		if (empty($v['location']['latitude'])) {
			echo "<tr><td>";
			$locationmsg = "Location not found<br>";				
		} else {
			echo "<tr class=\"success\"><td>";
			$locationmsg = "Location: " . $v['location']['latitude'] . "," . $v['location']['longitude'] . "<br>";
		}
		echo htmlspecialchars($v['caption']['text']) ."<br><br>Tags: ";
		foreach ($array_tags as $t) {echo "#".$t." ";}
		echo "<br><a href=\"" . $v['link'] . "\">" . $v['link'] . "</a><br>\n";
		echo $locationmsg;
		echo date("D, d M y H:i:s O", $v['caption']['created_time']);
		echo "</td><td><img src=\"" . $v['images']['standard_resolution']['url'] . "\"></td></tr>\n";
	} elseif ($onetag == "false" && in_array($cleantag1, $array_tags) && in_array($cleantag2, $array_tags)){		
		if (empty($v['location']['latitude'])) {
			echo "<tr><td>";
			$locationmsg = "Location not found<br>";				
		} else {
			echo "<tr class=\"success\"><td>";
			$locationmsg = "Location: " . $v['location']['latitude'] . "," . $v['location']['longitude'] . "<br>";
		}
		echo htmlspecialchars($v['caption']['text']) ."<br><br>Tags: ";
		foreach ($array_tags as $t) {echo "#".$t." ";}
		echo "<br><a href=\"" . $v['link'] . "\">" . $v['link'] . "</a><br>\n";
		echo $locationmsg;
		echo date("D, d M y H:i:s O", $v['caption']['created_time']);
		echo "</td><td><img src=\"" . $v['images']['standard_resolution']['url'] . "\"></td></tr>\n";
	}
unset($array_tags);
$i++;
}
echo "  </tbody>";
echo "  </table>";
include ('warning.php');
}

?>

<p class="text-warning">This product uses the Instagram API but is not endorsed or certified by Instagram.</p>

<?php include('footer.php'); ?>