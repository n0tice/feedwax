<?php
include('config/globals.php');
include('header.php');
include('nav.php');

?>

<div class="hero-unit">
<h1>Build a feed from a spreadsheet</h1>
<strong>Guidelines:</strong> Your spreadsheet must be publicly shared (<a href="https://support.google.com/drive/bin/answer.py?hl=en&answer=37579">here's how to do that</a>...don't forget to click the 'Publish to web' button and <em>"Start Publishing"</em>).  It's also helpful to use these column titles: <em>title, description, url</em> and <em>lat, long</em>.  You can also use <em>address, city, postcode</em>, but location accuracy may vary.  You can optionally include <em>pubdate, image</em>.<br>
<a href="http://feedwax.com/img/spreadsheet-details.png"><img src="http://feedwax.com/img/spreadsheet-details.png" width="650" border="0"></a>
<form action="" method="GET" class="well">
    Google Spreadsheet URL: <input type="text" class="text input-block-level" value="<?php if($_GET['spreadsheet_url']) { echo urldecode($_GET['spreadsheet_url']); } else { echo ""; } ?>" name="spreadsheet_url" placeholder="https://docs.google.com/spreadsheet/ccc?key=SPREADSHEETKEY"/><br>

<?php
if ($_GET) {
	if (!empty($_GET['spreadsheet_url'])) {
	include('feeders/googlespreadsheetsfunctions.php');
	echo "Headline: ";
	echo "<select name=\"title[]\" class=\"selectpicker\" multiple=\"yes\"/>\n";
	foreach ($labels as $label) {
		for($num=0;$num<count($_GET["title"]);$num++){
			if ($_GET["title"][$num] == $label) {
				$selected = "selected";
			}
		}
		echo "    <option value=\"".$label."\" ".$selected.">".$label."</option>\n";	
		unset($selected);
	}
	echo "    </select><br>\n";

	echo "Description: ";
	echo "<select name=\"description[]\" class=\"selectpicker\" multiple=\"yes\"/>\n";
	foreach ($labels as $label) {
		for($num=0;$num<count($_GET["description"]);$num++){
			if ($_GET["description"][$num] == $label) {
				$selected = "selected";
			}
		}
		echo "    <option value=\"".$label."\" ".$selected.">".$label."</option>\n";	
		unset($selected);
	}
	echo "    </select><br>\n";

	echo "Link: ";
	echo "<select name=\"url\" class=\"selectpicker\"/>\n";
	foreach ($labels as $label) {
		if (isset($_GET['url']) && ($_GET['url'] == $label)) {
			echo "    <option value=\"".$label."\" selected>".$label."</option>\n";
		} elseif ($label == "url") {
			echo "    <option value=\"".$label."\" selected>".$label."</option>\n";
		} else {
			echo "    <option value=\"".$label."\">".$label."</option>\n";
		}
	}
	echo "    </select><br>\n";

	echo "Image: ";
	echo "<select name=\"image\" class=\"selectpicker\"/>\n";
	foreach ($labels as $label) {
		if (isset($_GET['image']) && ($_GET['image'] == $label)) {
			echo "    <option value=\"".$label."\" selected>".$label."</option>\n";
		} elseif ($label == "image") {
			echo "    <option value=\"".$label."\" selected>".$label."</option>\n";
		} else {
			echo "    <option value=\"".$label."\">".$label."</option>\n";
		}
	}
	echo "    </select><br>\n";

	$n0ticefeed_url="http://feedwax.com/feeders/googlespreadsheetsfeeder.php?".$_SERVER['QUERY_STRING'];
	$content = file_get_contents($n0ticefeed_url);
	$xml = @simplexml_load_string($content);
	?>
	<br>
	<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
	</form>
	</div>
	<?php
	echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
	echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
	echo "<button class=\"btn btn-large btn-primary span4 align-right\" type=\"submit\">feed this into n0tice</button>";
	echo "</form>";

	echo "<table class=\"table\" width=\"100%\">";
	
	echo " <thead>";
	echo "    <tr>";
	echo "      <th>Title</th>";
	echo "      <th>Description</th>";
	echo "      <th>Location</th>";
	echo "    </tr>";
	echo "  </thead>";
	echo "  <tbody class=\"well\">";
	
	$i = 0;
	foreach ($xml->channel->item as $v) {
			$namespaces = $v->getNameSpaces(true);
			$geo = $v->children($namespaces['geo']); 
			echo "<tr><td>";
			echo $v->title;
			echo "</td><td>\n";
			echo $v->description;
			echo "</td><td>\n";
			if ($geo->lat) {
				echo "<a href=\"https://maps.google.co.uk/maps?q=$geo->lat,$geo->long&ll=$geo->lat,$geo->long&z=10\"><img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$geo->lat,$geo->long&zoom=15&size=100x100&maptype=roadmap&sensor=true\"</a>";
			} else {
				echo "No location available.";
			}
			echo "</td></tr>\n";
			unset($geocoder_url);
			unset($geocoder_string);
			unset($geocoder_array);
		}
	echo "  </tbody>";
	echo "  </table>";
	
	include ('warning.php');

}

} else {
	echo "<button type=\"submit\" value=\"build feed\" class=\"btn\"><i class=\"icon-fire\"></i> build feed</button>";
	echo "</form>\n</div>";
}
	echo "<p align=\"right\"><a href=\"".$n0ticefeed_url."\">RSS</a></p>";

?>


<?php include('footer.php'); ?>