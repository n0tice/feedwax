<?php
header('Content-type: application/rss+xml; charset=utf-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

$spreadsheet_key = $_GET['spreadsheet_key'];
 
// Set your CSV feed
$feed = 'https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key='.$spreadsheet_key.'&single=true&gid=5&output=csv&123';
 
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
    <title>spreadsheet</title>
    <link>http://n0tice.com/</link>
    <description>description</description>
<?php
$i = 0; 
foreach ($newArray as $v) {
	if (!empty($v['image'])) {
		$mediacontent = "     <media:content url=\"" . $v['image'] . "\" type=\"image/jpeg\"></media:content>\n";
	}
	if (empty($v['pubdate'])) {
		$pubdate = date("D, d M Y H:i:s O");
	}

	echo "<item>\n";
		echo "     <title>".$v['title']."</title>\n";
		echo "     <description>".$v['description']."</description>\n";
		echo "     <geo:lat>".$v['lat']."</geo:lat>\n";
		echo "     <geo:long>".$v['long']."</geo:long>\n";
		echo $mediacontent;
		echo "     <pubDate>".$pubdate."</pubDate>\n";
		echo "     <link>".$v['url']."</link>\n";
		echo "     <guid>".$v['url']."</guid>\n";
	echo "</item>\n";
$i++;
}

?>
</channel>
</rss>