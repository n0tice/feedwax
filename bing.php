<?php
if ($_GET['accountKey']) {
	$accountKey = $_GET['accountKey']; 
		} else { 
	$accountKey = $bing_key;
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
if ($_GET['count']) {
	$count = $_GET['count']; 
		} else { 
	$count = "10";
}

include('config/globals.php');
include('header.php');
include('nav.php');
?>

<div class="hero-unit">
<h1>Build a feed with Bing</h1>
<table class="table">
<tr><td><form action="" method="GET" class="well">
    Search terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo $_GET['q']; } else { echo "n0ticed"; } ?>" name="q" /><br>
	How many posts in the feed: <input type="text" class="text" value="<?php if($_GET['count']) { echo $_GET['count']; } else { echo "10"; } ?>" name="count" /><br>
    Lat: <input type="text" class="text" value="<?php echo $lat; ?>" name="lat" /><br>
    Long: <input type="text" class="text" value="<?php echo $long; ?>" name="long" /><br>
    <input type="hidden" class="text" value="<?php if($_GET['accountKey']) { echo $_GET['accountKey']; } else { echo $bing_key; } ?>" name="accountKey" /><br>
<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form></td>
<td>
  Find Place: <input type="text" id="address"/><input type="button" value="Go" onclick="geocode()"><input type="button" value="Add Marker at Center" onclick="addMarkerAtCenter()"/>
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
$bingatom_url="https://api.datamarket.azure.com/Data.ashx/Bing/Search/v1/News?Query=%27" . urlencode($_GET['q']) . "%27&Adult=%27Strict%27&Latitude=$lat&Longitude=$long&NewsSortBy=%27Date%27&$top=$count&$format=Atom&Market=%27en-GB";
$api_url="https://api.datamarket.azure.com/Data.ashx/Bing/Search/v1/News?Query=%27" . urlencode($_GET['q']) . "%27&Adult=%27Strict%27&Latitude=$lat&Longitude=$long&NewsSortBy=%27Date%27&\$top=$count&\$format=JSON";
$bingfeed_url="http://" . $_SERVER['SERVER_NAME'] . "/feeders/bingplainfeed.php?q=" . urlencode($_GET['q']) . "&lat=$lat&long=$long";
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/anyrssfeeder.php?url=" . $bingfeed_url;
$process = curl_init($api_url);
curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($process, CURLOPT_USERPWD,  $accountKey . ":" . $accountKey);
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($process);

echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-primary\" type=\"submit\">feed this into n0tice</button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">";
echo " <thead>";
echo "    <tr>";
echo "      <th>Title</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody class=\"well\">";

$jsonobj = json_decode($response);

foreach($jsonobj->d->results as $value)
	{                        
	echo('<tr ID="resultList">');
		echo('<td class="resultlistitem"><a href="' 
				  . $value->URL . '">'.htmlspecialchars($value->Title).'</a><br>' 
				  . htmlspecialchars($value->Description));
				  echo "</td>";
	echo("</tr>");
	}

echo "</tbody></table>";
include ('warning.php');
}
?>

<?php include('footer.php'); ?>