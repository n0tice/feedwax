<?php
include('../config/globals.php');
header('Content-type: application/rss+xml; charset=utf-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

$spreadsheet_key = $_GET['spreadsheet_key'];
 
// Set your CSV feed
$feed = 'https://docs.google.com/a/guardian.co.uk/spreadsheet/pub?key='.$spreadsheet_key.'&single=true&gid=0&output=csv';
 
// Arrays we'll use later
$keys = array();
$newArray = array();
 
// Function to convert CSV into associative array
function csvToArray($file, $delimiter) { 
  if (($handle = fopen($file, 'r')) !== FALSE) { 
    $i = 0; 
    while (($lineArray = fgetcsv($handle, 6000, $delimiter, '"')) !== FALSE) { 
      for ($j = 0; $j < count($lineArray); $j++) { 
        $arr[$i][$j] = $lineArray[$j]; 
      } 
      $i++; 
    } 
    fclose($handle); 
  } 
  return $arr; 
} 
 
// Do it
$data = csvToArray($feed, ',');
 
// Set number of elements (minus 1 because we shift off the first row)
$count = count($data) - 1;
 
//Use first row for names  
$labels = array_shift($data);  
 
foreach ($labels as $label) {
  $keys[] = $label;
}
 
// Add Ids, just in case we want them later
$keys[] = 'id';
 
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}
 
// Bring it all together
for ($j = 0; $j < $count; $j++) {
  $d = array_combine($keys, $data[$j]);
  $newArray[$j] = $d;
}
 
// Print it out as JSON
#echo json_encode($newArray);
?>

<rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:media="http://search.yahoo.com/mrss/">
<channel>
    <title>localshopping spreadsheet</title>
    <link>http://localshopping.n0tice.com/</link>
    <description>localshopping description</description>
<?php
$i = 0; 
foreach ($newArray as $v) {

	if ((empty($v['lat'])) && (!empty($v['postcode']))) {
	$fulladdress = $v['address'].",".$v['city'].",".$v['postcode'];
	$place_url="https://maps.googleapis.com/maps/api/place/textsearch/json?query=".urlencode($fulladdress)."&sensor=true&key=".$google_key;
		$place_string .= file_get_contents($place_url); // get json content
		$place_array = json_decode($place_string, true); //json decoder
		$lat = $place_array['results'][0]['geometry']['location']['lat'];
		$long = $place_array['results'][0]['geometry']['location']['lng'];
		$geolat = "     <geo:lat>".$lat."</geo:lat>\n";
		$geolong = "     <geo:long>".$long."</geo:long>\n";
	} else {
		$geolat = "     <geo:lat>".$v['lat']."</geo:lat>\n";
		$geolong = "     <geo:long>".$v['long']."</geo:long>\n";
	}

	if (!empty($v['image'])) {
		$mediacontent = "     <media:content url=\"" . $v['image'] . "\" type=\"image/jpeg\"></media:content>\n";
	}

	$pubdate = date("D, d M Y H:i:s O");

	echo "<item>\n";
		echo "     <title>".$v['title']."</title>\n";
		echo "     <description>".$v['description']."</description>\n";
		echo $geolat;
		echo $geolong;
		echo $mediacontent;
		echo "     <pubDate>".$pubdate."</pubDate>\n";
		echo "     <link>".$v['url']."</link>\n";
		echo "     <guid>".$v['url']."</guid>\n";
	echo "</item>\n";
unset($fulladdress);
unset($geolat);
unset($geolong);
unset($place_url);
unset($place_string);
unset($place_array);
$i++;
}

?>
</channel>
</rss>