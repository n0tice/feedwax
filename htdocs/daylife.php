<?php
if ($_GET['q']) {
	$q = urlencode($_GET['q']); 
		} else { 
	$q = "n0ticed";
}

if ($_GET['limit']) {
	$limit = $_GET['limit']; 
		} else { 
	$limit = "10";
}

include('config/globals.php');
include('header.php');
include('nav.php');
?>

<div class="hero-unit">
<h1>Build a feed with Daylife</h1>
<form action="" method="GET" class="well">
    Search Terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo $_GET['q']; } else { echo ""; } ?>" name="q" /><br>
    <!-- How many posts in the feed: <input type="text" class="text" value="<?php if($_GET['limit']) { echo $_GET['limit']; } else { echo "10"; } ?>" name="maxresults" /><br> -->

<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
# Configure the daylife api server url 
	$daylife_server = "freeapi.daylife.com";
	$protocol = "jsonrest";
	$version = "4.10";
	$publicapi_access_url = "http://" . $daylife_server . "/" . $protocol . "/publicapi/" . $version . "/";
	$method='search_getRelatedArticles';


$q = $_GET['q'];
$url_encoded_q = urlencode($q);

# For search_X methods, the Core Input is the query term itself
$signature = md5($daylife_accesskey . $daylife_sharedsecret . $q);

#Draw from news in last 3 days
$end_time = date(U);
$start_time = $end_time - (3 * 86400);

$api_url = $publicapi_access_url . $method . '?accesskey=' . $accesskey . '&signature=' . $signature . '&query=' . $url_encoded_q;
$daylifefeeder_url="http://" . $_SERVER['SERVER_NAME'] . "/feeders/daylifefeeder.php?" . $_SERVER['QUERY_STRING'];
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/anyrssfeeder.php?url=" . urlencode($daylifefeeder_url);
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder

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
echo "      <th>Source</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody>";

	$i = 0; 
	foreach ($array['response']['payload']['article'] as $v) {
				echo "<tr><td>";
				echo utf8_encode(htmlentities($v['headline'])) . " </td>\n";
				echo "<td>" . $v['source']['name'] . "</td></tr>\n";
			}
		$i++;

echo "  </tbody>";
echo "  </table>";
include ('warning.php');
} 

?>

<?php include('footer.php'); ?>