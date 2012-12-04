<?php
include('config/globals.php');
include('header.php');
include('nav.php');
$url = urlencode($_GET['url']);
?>

<div class="hero-unit">
<h1>Build a feed with SeeClickFix</h1>
<form action="" method="GET" class="well">
    Search near a location: <input type="text" class="text" value="<?php if($_GET['q']) { echo $_GET['q']; } else { echo ""; } ?>" name="q" /><br>
	How many posts in the feed: <input type="text" class="span2" value="<?php if($_GET['count']) { echo $_GET['count']; } else { echo "10"; } ?>" name="count" /><br>
<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
if ($_GET) {
$seeclickfix_api = "http://seeclickfix.com/api/issues.json?at=" . urlencode($_GET['q']) . "&zoom=10&end=0&page=1&num_results=" . $_GET['count'] . "&sort=issues.created_at";
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/seeclickfixfeeder.php?" . $_SERVER['QUERY_STRING'];
$string .= file_get_contents($seeclickfix_api); // get json content
$array = json_decode($string, true); //json decoder

echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-primary\" type=\"submit\">feed this into n0tice</button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">";

echo " <thead>";
echo "    <tr>";
echo "      <th>Title</th>";
echo "      <th>Image</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody>";

$i = 0; 
foreach ($array as $v) {
$item_api = "http://seeclickfix.com/api/issues/" . $v['id'] . ".json";
$item_string .= file_get_contents($item_api); // get json content
$item_array = json_decode($item_string, true); //json decoder
			echo "<tr><td>";
			echo htmlspecialchars($v['summary']) .": " . htmlspecialchars($v['description']) . "<br>\n";
			echo "<a href=\"" . $v['bitly'] . "\">" . $v['bitly'] . "</a><br>\n";
			echo "Location: " . $v['lat'] . "," . $v['lng'] . "<br>";
			echo date("D, d M y H:i:s O", $v['created_at']);
			echo "</rd><td><img src=\"" . $item_array[0]['square_image'] . "\"></td></tr>";
			$i++;
		}
include ('warning.php');
}
echo "  </tbody>";
echo "  </table>";

?>

<?php include('footer.php'); ?>