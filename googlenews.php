<?php
include('config/globals.php');
include('header.php');
include('nav.php');
?>

<div class="hero-unit">
<h1>Build a feed with Google</h1>
<form action="" method="GET" class="well">
    Search Terms: <input type="text" class="text" value="<?php if($_GET['q']) { echo urldecode($_GET['q']); } else { echo ""; } ?>" name="q" /><br>
    <!-- Country: <input type="text" class="text" value="<?php if($_GET['gl']) { echo $_GET['gl']; } else { echo "uk"; } ?>" name="gl" />(two-letter code, ie 'uk')<br> -->
    <input type="hidden" name="type" value="news" >
<!--
	<input type="radio" name="type" value="news" <?php if ($_GET['type'] == "news" || ($_GET['type']) == "") { echo "checked"; } ?>>&nbsp;&nbsp;News Sources&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" name="type" value="blogs" <?php if ($_GET['type'] == "blogs") { echo "checked"; } ?>>&nbsp;&nbsp;Blogs <br><br>
-->
<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
if ($_GET) {

if($_GET['type'] == "blogs") {
		$google_url = "https://www.google.com/search?q=" . urlencode($_GET['q']) . "&hl=en&tbm=blg&gl=" . $_GET['gl'] . "&um=1&ie=UTF-8&output=rss";
} else {
		$google_url = "https://news.google.com/news/feeds?q=" . $_GET['q'] . "&hl=en&gl=" . $_GET['gl'] . "&um=1&ie=UTF-8&output=rss";
}
$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/googlenewsfeeder.php?url=" . urlencode($google_url) . "&q=" . urlencode($_GET['q']) . "&source=googlenews&type=" . $_GET['type'] . "&LinkInTitle=true";
$content = utf8_encode(file_get_contents($google_url));
$xml = simplexml_load_string($content);

include ('policy-change.php');
#echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
#echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
#echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-disabled\" type=\"submit\"><strike>import into n0tice now</strike></button></td>";
#echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
#echo "</form>";

echo "<table class=\"table\" width=\"100%\">";
echo " <thead>";
echo "    <tr>";
echo "      <th>Headline</th>";
echo "      <th>Location *</th>";
echo "    </tr>";
echo "  </thead>";
echo "  <tbody class=\"well\">";
	if ($xml->channel) {
		foreach ($xml->channel->item as $v) {
			$namespaces = $v->getNameSpaces(true);
			parse_str(($v->link), $output);
			$link = urlencode($output['url']);
			$title_clean = strip_tags(utf8_encode($v->title));
			$title = html_entity_decode($title_clean, ENT_COMPAT, 'UTF-8');


			$extract = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20contentanalysis.analyze%20where%20url%3D%22" . $link . "%22&format=json";
			$extract_string .= file_get_contents($extract); // get json content
			$extract_array = json_decode($extract_string, true); //json decoder
			
			if ($extract_array['query']['results']) {
				$i = 0;
				foreach ($extract_array['query']['results']['entities']['entity'] as $v) {
					if ($v['metadata_list']['metadata']['woe_id'] && $i == "0" && ($v['score'] > "0")) {
						echo "<tr class=\"success\"><td>";
						echo $title . "</td>\n";
						echo "<td>\n";
							echo "<em>";
						if ($v['metadata_list']['metadata']['geo_streetname']) {echo $v['metadata_list']['metadata']['geo_streetname'] . ", ";}
						if ($v['metadata_list']['metadata']['geo_town']) {echo $v['metadata_list']['metadata']['geo_town'] . ", ";}
						if ($v['metadata_list']['metadata']['geo_name']) {echo $v['metadata_list']['metadata']['geo_name'] . ", ";}
						if ($v['metadata_list']['metadata']['geo_country']) {echo $v['metadata_list']['metadata']['geo_country'];}
						if ($v['metadata_list']['metadata']['geo_location']) {echo "<br>" . $v['metadata_list']['metadata']['geo_location'] . "<br>";}
						echo "</em>";
						$raw_score = $v['score'] * 100;
						list($whole, $decimal) = explode('.', $raw_score);
						echo "<br>Certainty: " . $whole . " out of 100\n";
					$i++;
					}
					if ($v['metadata_list']['metadata']['woe_id'] && $i == "0" && ($v['score'] == "0")) {
						echo "<tr><td>";
						echo $title . "</td>\n";
						echo "<td>\n";
						echo "<em>";
						if ($v['metadata_list']['metadata']['geo_streetname']) {echo $v['metadata_list']['metadata']['geo_streetname'] . ", ";}
						if ($v['metadata_list']['metadata']['geo_town']) {echo $v['metadata_list']['metadata']['geo_town'] . ", ";}
						if ($v['metadata_list']['metadata']['geo_name']) {echo $v['metadata_list']['metadata']['geo_name'] . ", ";}
						if ($v['metadata_list']['metadata']['geo_country']) {echo $v['metadata_list']['metadata']['geo_country'];}
						if ($v['metadata_list']['metadata']['geo_location']) {echo "<br>" . $v['metadata_list']['metadata']['geo_location'] . "<br>";}
						echo "<br>High uncertainty";
						echo "</em>";
					$i++;
					}
					if (!$v['metadata_list'] && $i == "0") {
						echo "<tr><td>";
						echo $title . "</td>\n";
						echo "<td>\n";
						echo "we can't seem to find a location...we'll keep trying, though.";
					$i++;
					}
					
				}
			} else {
			
			echo "<tr><td>";
			echo $title . "</td>\n";
			echo "<td>\n";
			echo "searching for locations...give us 2 minutes and try again.";
			
			}
			unset($i);
			unset($extract);
			unset($extract_string);
			unset($extract_array);
			echo "</td></tr>\n";

		}
	} else {
			echo "<tr><td colspan=\"2\">";
			echo "No results found.  Try again.";
			echo "</td></tr>\n";
	}
echo "<tr><td colspan=2>* If we couldn't get your location the first time, please check again in a few minutes.  For more information on how we identify locations, read more about FeedWax here.</td></tr>";
echo "  </tbody></table>";
include ('warning.php');
}

echo "<a href=\"http://feedton0tice.com/feeds/new?url=" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"10\" align=\"right\"></a>";
include('footer.php'); 

?>