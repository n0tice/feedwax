<?php
include('../config/globals.php');
header('Content-type: application/rss+xml');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

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
echo "<?xml version=\"1.0\"?>\n";
$secret = $flickr_secret;
$api_key = $flickr_key;
$params = "accuracy11api_key" . $api_key . "extrasdescriptiongeoformatjsonhas_geo1lat" . $lat . "lon" . $long . "methodflickr.photos.searchnojsoncallback1per_page" . $maxresults . "radius" . $radius . "tags" . $q . "text" . $q;
$sigmaker = $secret.$params;
$api_sig = md5($sigmaker);

$api_url="http://api.flickr.com/services/rest/?method=flickr.photos.search&accuracy=11&api_key=$api_key&format=json&has_geo=1&lat=$lat&license=4,5,6,7&lon=$long&nojsoncallback=1&per_page=$maxresults&radius=$radius&tags=$q&text=$q&extras=description,date_taken,owner_name,geo,path_alias,url_z&sort=date-posted-desc";
$string .= file_get_contents($api_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>Flickr search: <?php echo $q; ?></title>
    <link><?php echo htmlentities($api_url); ?></link>
    <description><?php echo $maxresults; ?> Flickr images of <?php echo $q; ?></description>

<?php
$i = 0; 
if ($array['photos']['total'] != 0) {
	foreach ($array['photos']['photo'] as $v) {
		echo "<item>\n";
		echo "<title>" . $v['title'] . " - Photo by " . $v['ownername'] . "</title>\n";
		echo "<description>" . $v['description']['_content'] . "</description>";
		echo "<media:content url=\"" . $v['url_z'] . "\" type=\"image/jpeg\"></media:content>\n";
		$pubdate = strtotime($v['datetaken']);
		echo "<pubDate>" . date("D, d M y H:i:s O", $pubdate) . "</pubDate>";
		echo "<link>http://www.flickr.com/photos/" . $v['pathalias'] . "/" . $v['id'] . "/</link>";
		echo "<guid>http://www.flickr.com/photos/" . $v['pathalias'] . "/" . $v['id'] . "/</guid>";
		echo "<geo:lat>". $v['latitude'] . "</geo:lat>\n";
		echo "<geo:long>". $v['longitude'] . "</geo:long>\n";
		echo "</item>\n";
		$i++;
	}
}
?>
    </channel>
</rss>
