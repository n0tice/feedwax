<?php
include('../config/globals.php');
header('Content-type: application/rss+xml; charset=utf-8');
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
if ($_GET['license'] == "all") {
	$license = "0,1,2,3,4,5,6,7"; 
	$licensenocomma = "01234567"; 
		} else { 
	$license = "4,5,6,7"; 
	$licensenocomma = "4567"; 
}
if ($_GET['unlocatedphotos']) {
	$unlocatedphotos = $_GET['unlocatedphotos']; 
		} else { 
	$unlocatedphotos = "exclude";
}
$assignedloc = $_GET['assignedloc'];
if ($_GET['unlocatedphotos'] == "exclude") {
	$assignedloc = null;
}
function explodeCommas($commalist) {
	return explode(',', $commalist);
}
list($assignedlat,$assignedlong) = explodeCommas($assignedloc);

echo "<?xml version=\"1.0\"?>\n";
$extras = "date_taken,date_upload,description,geo,last_update,license,owner_name,url_z";
$secret = $flickr_secret;
$api_key = $flickr_key;
$params = "accuracy11api_key" . $api_key . "extrasdate_takendescriptiongeoowner_nametagsformatjsonhas_geo1lat" . $lat . "license" . $licensenocomma . "lon" . $long . "methodflickr.photos.searchnojsoncallback1per_page" . $maxresults . "radius" . $radius . "sortdate-posted-desctags" . $q . "text" . $q;
$sigmaker = $secret.$params;
$api_sig = md5($sigmaker);
$flickr_url = "http://api.flickr.com/services/rest/?method=flickr.photos.search&accuracy=11&api_key=$api_key&extras=".urlencode($extras)."&format=json&has_geo=1&lat=$lat&license=".urlencode($license)."&lon=$long&nojsoncallback=1&per_page=$maxresults&radius=$radius&sort=date-posted-desc&tags=$q&text=$q";
$string .= file_get_contents($flickr_url); // get json content
$array = json_decode($string, true); //json decoder
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>Flickr search: <?php echo urldecode($q); ?></title>
    <link><?php #echo $api_url; ?>http://www.flickr.com/search/?q=<?php echo $q; ?></link>
    <description><?php echo $maxresults; ?> Flickr images of <?php echo $q; ?></description>

<?php
$i = 0; 
if ($array['photos']['total'] != 0) {
	foreach ($array['photos']['photo'] as $v) {
	if (preg_match("/_MG_|IMG_|DSC_/", $v['title'])) {
		$titlerewrite = true;
	} else {
		$titlerewrite = false;
	}
	if ($v['license'] == "0") {$getlicense = "All Rights Reserved";}
	if ($v['license'] == "1") {$getlicense = "Attribution-NonCommercial-ShareAlike License";}
	if ($v['license'] == "2") {$getlicense = "Attribution-NonCommercial License";}
	if ($v['license'] == "3") {$getlicense = "Attribution-NonCommercial-NoDerivs License";}
	if ($v['license'] == "4") {$getlicense = "Attribution License";}
	if ($v['license'] == "5") {$getlicense = "Attribution-ShareAlike License";}
	if ($v['license'] == "6") {$getlicense = "Attribution-NoDerivs License";}
	if ($v['license'] == "7") {$getlicense = "No known copyright restrictions";}

	if ((!empty($v['latitude']) || !empty($assignedlat)) ){
		$imageurl = "http://www.flickr.com/photos/" . $v['owner'] . "/" . $v['id'] . "/";
			echo "<item>\n";
			echo "<title>";
			if ($titlerewrite == false) {echo $v['title'] . " - Photo by " . $v['ownername'];} else {echo "Photo by ".$v['ownername'];}
			echo "</title>\n";
			echo "<description><![CDATA[";
			echo $v['description']['_content'];
			echo "\n\nBy " . $v['ownername'] . " via Flickr: http://www.flickr.com/people/".$v['owner']."\n";
			echo "\nLicense: ".$getlicense."\n";
			echo "]]></description>\n";
			echo "<media:content url=\"" . $v['url_z'] . "\" type=\"image/jpeg\"></media:content>\n";
			$pubdate = strtotime($v['datetaken']);
			echo "<pubDate>" . date("D, d M y H:i:s O", $pubdate) . "</pubDate>\n";
			echo "<link>".$imageurl."</link>\n";
			echo "<guid>".$imageurl."</guid>\n";
			if (empty($v['latitude'])) {
				echo "<geo:lat>". $assignedlat . "</geo:lat>\n";
				echo "<geo:long>". $assignedlong . "</geo:long>\n";			
			} else {
				echo "<geo:lat>". $v['latitude'] . "</geo:lat>\n";
				echo "<geo:long>". $v['longitude'] . "</geo:long>\n";
			}
			echo "</item>\n";
			$i++;
		}
	}
}
?>
    </channel>
</rss>
