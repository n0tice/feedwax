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
<ul class="nav nav-tabs">
	<li class="active"><a href="instagramtag.php">Search by Tags</a></li>
	<li><a href="instagramgeo.php">Search by Location</a></li>
</ul>
<div class="well">
	<form action="" method="GET" class="well">
		Tags to search: <input type="text" class="input-medium" value="<?php if($_GET['q']) { echo $cleantag1." ".$cleantag2; } ?>" name="q" placeholder="tag1 tag2"/> (up to 2 tags)<br>
		Tag to exclude: <input type="text" class="input-mini" value="<?php if(!empty($not)) { echo $not; } ?>" name="not" placeholder="optional"/><br>
		Assign default location to unlocated Instagrams:<br><input type="text" class="input-xlarge" value="<?php if($_GET['defaultloc']) { echo $_GET['defaultloc']; } else { echo ""; } ?>" name="defaultloc"  placeholder="optional" /> (lat,long)<br>
		<input type="hidden" value="<?php if($_GET['count']) { echo $_GET['count']; } else { echo "10"; } ?>" name="count" />
	<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
	</form>
</div>
</div>

<?php 
if ($_GET) {

$instagram_cleantag1="https://api.instagram.com/v1/tags/".urlencode($cleantag1)."/media/recent?client_id=$client_id&count=$count";
$string_cleantag1 .= file_get_contents($instagram_cleantag1); // get json content
$array_cleantag1 = json_decode($string_cleantag1, true); //json decoder

$array_refined = array();

if (!empty($cleantag2)) {
	$instagram_cleantag2="https://api.instagram.com/v1/tags/" . urlencode($cleantag2) . "/media/recent?client_id=$client_id&count=$count";
	$string_cleantag2 .= file_get_contents($instagram_cleantag2); // get json content
	$array_cleantag2 = json_decode($string_cleantag2, true); //json decoder

	$b = 0;
	foreach ($array_cleantag1['data'] as $item) {
		if (in_array($cleantag2,$item['tags'])) {
			array_push($array_refined, $item);
		}
	$b++;
	}

	$c = 0;
	foreach ($array_cleantag2['data'] as $item) {
		if (in_array($cleantag1,$item['tags'])) {
			array_push($array_refined, $item);
		}
	$c++;
	}

} else {
	$a = 0;
	foreach ($array_cleantag1['data'] as $item) {
		array_push($array_refined, $item);
	$a++;
	}
}

#echo "tag1: ".$cleantag1."<br>";
#echo "tag2: ".$cleantag2."<br>";
#echo json_encode($array_refined);

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
foreach ($array_refined as $v) {
if (!in_array($not,$v['tags'])) {
	$array_tags = $v['tags'];
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
	unset($array_tags);
}
$i++;
}
echo "  </tbody>";
echo "  </table>";
include ('warning.php');
}

?>

<p class="text-warning">This product uses the Instagram API but is not endorsed or certified by Instagram.</p>

<?php include('footer.php'); ?>