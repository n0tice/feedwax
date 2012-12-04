<?php
include('config/globals.php');
include('header.php');
include('nav.php');
?>

<div class="hero-unit">
<h1>Geocode your RSS feed</h1>
<form action="" method="GET" class="well">
    RSS feed URL: <input type="text" class="text" value="<?php if($_GET['url']) { echo $_GET['url']; } else { echo ""; } ?>" name="url" /><br>
<button type="submit" value="build feed" class="btn"><i class="icon-fire"></i> build feed</button>
</form>
</div>

<?php 
if ($_GET) {

$n0ticefeed_url = "http://" . $_SERVER['SERVER_NAME'] . "/feeders/rssrewrite.php?url=" . urlencode($_GET['url']) . "&accuracy=precise&LinkInTitle=true";
$rss_url = urldecode($_GET['url']);
$xml = simplexml_load_file($rss_url); 


echo "<form action=\"http://feedton0tice.com/feeds/new\" method=\"GET\">\n";
echo "<input type=\"hidden\" name=\"url\" value=\"" . $n0ticefeed_url . "\">";
echo "<table width=\"100%\"><tr><td><button class=\"btn btn-large btn-primary\" type=\"submit\">feed this into n0tice</button></td>";
echo "<td align=\"right\"><a href=\"" . $n0ticefeed_url . "\"><img src=\"/img/rss1.png\" width=\"40\"></a></td></tr></table>";
echo "</form>";
echo "<div class=\"navbar\">\n";
echo "    <a class=\"brand\" href=\"\">Results</a> \n";
echo "    <ul class=\"nav nav-tabs pull-right\">\n";
echo "      <li class=\"active\"><a href=\"rss-precise.php?url=". $_GET['url'] . "\">Strict</a></li>\n";
echo "      <li><a href=\"rss-loose.php?url=". $_GET['url'] . "\">Loose</a></li>\n";
echo "    </ul>\n";
echo "</div>\n";

echo "<table class=\"table\" width=\"100%\">";
echo "  <tbody>";
		if ($xml->channel) {
			foreach ($xml->channel->item as $x) {
				$namespaces = $x->getNameSpaces(true);
			
					$extract = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20contentanalysis.analyze%20where%20url%3D%22" . $x->link . "%22&format=json";
					$extract_string .= file_get_contents($extract); // get json content
					$extract_array = json_decode($extract_string, true); //json decoder
					if ($extract_array['query']['results']) {
						$i = 0;
						foreach ($extract_array['query']['results']['entities']['entity'] as $v) {
							if (!empty($v['metadata_list']['metadata']) && ($i == 0) && ($v['score'] > '0')) {
								echo "<tr class=\"success\"><td>";
								echo $x->title . "</td>\n";
								echo "<td>\n";
								echo "<em>";
								if ($v['metadata_list']['metadata']['geo_streetname']) {echo $v['metadata_list']['metadata']['geo_streetname'] . ", ";}
								if ($v['metadata_list']['metadata']['geo_town']) {echo $v['metadata_list']['metadata']['geo_town'] . ", ";}
								if ($v['metadata_list']['metadata']['geo_name']) {echo $v['metadata_list']['metadata']['geo_name'] . ", ";}
								if ($v['metadata_list']['metadata']['geo_country']) {echo $v['metadata_list']['metadata']['geo_country'];}
								echo "</em>";
								$raw_score = $v['score'] * 100;
								list($whole, $decimal) = explode('.', $raw_score);
								echo "<br>Certainty: " . $whole . " out of 100\n";
							$i++;
							}
							if (!empty($v['metadata_list']['metadata']) && ($i == 0) && ($v['score']=='0')) {
								echo "<tr><td>";
								echo $x->title . "</td>\n";
								echo "<td>\n";
								echo "<em>";
								if ($v['metadata_list']['metadata']['geo_streetname']) {echo $v['metadata_list']['metadata']['geo_streetname'] . ", ";}
								if ($v['metadata_list']['metadata']['geo_town']) {echo $v['metadata_list']['metadata']['geo_town'] . ", ";}
								if ($v['metadata_list']['metadata']['geo_name']) {echo $v['metadata_list']['metadata']['geo_name'] . ", ";}
								if ($v['metadata_list']['metadata']['geo_country']) {echo $v['metadata_list']['metadata']['geo_country'];}
								echo "<br>High uncertainty";
								echo "</em>";
							$i++;
							}
							
						}
					} else {
					
					echo "<tr><td>";
					echo $x->title . "</td>\n";
					echo "<td>\n";
					echo "searching for locations...please check again later.";
					
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
echo "<tr><td colspan=2>* If we couldn't get your location the first time, please check again in a few minutes.  For more information on how we identify locations, read more about FeedWax <a href=\"about.php\">here</a>.</td></tr>";
echo "  </tbody></table>";
include ('warning.php');
}

include('footer.php'); 

?>