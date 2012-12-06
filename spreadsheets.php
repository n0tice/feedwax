<?php
include('config/globals.php');
include('header.php');
include('nav.php');
$spreadsheets_key = $_GET['spreadsheets_key'];
?>

<div class="hero-unit">
<h1>Build a feed from a spreadsheet</h1>
<form action="" method="GET" class="well">
    Google Spreadsheet Key: <input type="text" class="text" value="<?php if($_GET['spreadsheets_key']) { echo $spreadsheets_key; } else { echo ""; } ?>" name="spreadsheets_key" /><br>
	<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
if ($_GET) {

$n0ticefeed_url="http://feedwax.com/feeders/googlespreadsheetsfeeder.php?spreadsheet_key=0AqI_Buqmkn8gdFFjNWczODdtMTF0dUhSb3lXZGk5LVE#gid=0";
$content = utf8_encode(file_get_contents($n0ticefeed_url));
$xml = simplexml_load_string($content);

echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
?>
<div class="alert span7">
  <strong>Formatting guidelines:</strong> Your spreadsheet must meet two requirements: First, it must be publicly shared (<a href="https://support.google.com/drive/bin/answer.py?hl=en&answer=37579">here's how to do that</a>).  Second, it must have the following column titles: <em>title, description, url</em> and either <em>lat, long</em> or <em>address, city, postcode</em>.  You can optionally include <em>pubdate, image</em>.
<a href="http://feedwax.com/img/spreadsheet-details.png"><img src="http://feedwax.com/img/spreadsheet-details.png" width="450" border="0"></a>
</div>
<?php
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
	echo "<tr><td><strong>".$v->title."</strong><br>".$v->description."</td></tr>";
	$i++;
	}
}
echo "  </tbody>";
echo "  </table>";

include ('warning.php');
}
?>

<?php include('footer.php'); ?>