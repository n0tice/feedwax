<?php
include('config/globals.php');
include('header.php');
include('nav.php');
$spreadsheet_key = $_GET['spreadsheet_key'];
?>

<div class="hero-unit">
<h1>Build a feed from a spreadsheet</h1>
<form action="" method="GET" class="well">
    Google Spreadsheet Key: <input type="text" class="text" value="<?php if($_GET['spreadsheet_key']) { echo $spreadsheet_key; } else { echo ""; } ?>" name="spreadsheet_key" /><br>
	<div class="alert pull-right">
	  <strong>Formatting guidelines:</strong> Your spreadsheet must meet two requirements: First, it must be publicly shared (<a href="https://support.google.com/drive/bin/answer.py?hl=en&answer=37579">here's how to do that</a>).  Second, it must have the following column titles: <em>title, description, url</em> and either <em>lat, long</em> or <em>address, city, postcode</em>.  You can optionally include <em>pubdate, image</em>.
	<a href="http://feedwax.com/img/spreadsheet-details.png"><img src="http://feedwax.com/img/spreadsheet-details.png" width="650" border="0"></a>
<strong>Publish your spreadsheet:</strong> You must also click the 'Publish to web' button and <em>"Start Publishing"</em> your spreadsheet.  Otherwise, FeedWax can't see your spreadsheet on the Internet.
	</div>
	<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
if ($_GET) {

$n0ticefeed_url="http://feedwax.com/feeders/googlespreadsheetsfeeder.php?spreadsheet_key=".$spreadsheet_key."#gid=0";
$content = file_get_contents($n0ticefeed_url);
$xml = @simplexml_load_string($content);

#echo $n0ticefeed_url;
echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<button class=\"btn btn-large btn-primary span4 align-right\" type=\"submit\">feed this into n0tice</button>";
echo "</form>";

echo "<table class=\"table\" width=\"100%\">";

echo " <thead>";
echo "    <tr>";
echo "      <th>Results</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody class=\"well\">";

if ($xml->channel) {
$i = 0;
	foreach ($xml->channel->item as $v) {
			$namespaces = $v->getNameSpaces(true);
			$geo = $v->children($namespaces['geo']); 
	
			if ($geo->lat) {
				$geocoder_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$geo->lat,$geo->long&sensor=false";
				$geocoder_string .= file_get_contents($geocoder_url); // get json content
				$geocoder_array = json_decode($geocoder_string, true); //json decoder

				echo "<tr><td>";
				echo $v->title . " - " . $v->description . "</td>\n";
				echo "<td><a href=\"https://maps.google.co.uk/maps?q=$geo->lat,$geo->long&ll=$geo->lat,$geo->long&z=10\">" . $geocoder_array['results'][0]['formatted_address'] . "</a>";
				echo " ";
				echo "</td></tr>\n";
				unset($geocoder_url);
				unset($geocoder_string);
				unset($geocoder_array);
				}
			}
}
echo "  </tbody>";
echo "  </table>";

include ('warning.php');
}
?>

<?php include('footer.php'); ?>