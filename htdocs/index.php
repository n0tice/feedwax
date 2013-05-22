<?php
if ($_GET) {
	#if ($_GET['source'] == "googlenews.php") {$query = $_GET['q'] . "&gl=uk&type=news";}
	#else {$query = $_GET['q'];}
	header("Location: http://" . $_SERVER['SERVER_NAME'] . "/" . $_GET['source'] . "?q=" . urlencode($_GET['q']) . "");
}
include('config/globals.php');
include('header.php');
include('nav.php');
?>

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>Curate local information</h1>
<form action="" method="GET" class="well">
    Search: <input type="text" class="text" value="<?php if($_GET['q']) { echo $_GET['q']; } else { echo ""; } ?>" name="q" /><br>

<button type="radio" value="twitter.php" class="btn-large" name="source"><i class="icon-fire"></i> twitter</button> 
<button type="radio" value="instagramtag.php" class="btn-large" name="source"><i class="icon-fire"></i> instagram</button>
<button type="radio" value="flickr.php" class="btn-large" name="source"><i class="icon-fire"></i> flickr</button> 
<button type="radio" value="youtube.php" class="btn-large" name="source"><i class="icon-fire"></i> youtube</button><br>
<button type="radio" value="daylife.php" class="btn-large" name="source"><i class="icon-fire"></i> daylife</button> 
<button type="radio" value="bing.php" class="btn-large" name="source"><i class="icon-fire"></i> bing news</button> 
<button type="radio" value="googlenews-loose.php" class="btn-large" name="source"><i class="icon-fire"></i> google news</button> 
</form>
        <p>FEEDWAX helps you curate news, photos, video, tweets, data, and links - streams of information about what's happening in your local area right now.  Use FEEDWAX to build location-aware media streams and feed them into n0tice.com or any RSS-friendly platform.  </p>
      </div>

      <!-- Example row of columns -->
    <div class="row-fluid">
        <div class="span4">
          <h2>RSS Feeds</h2>
          <p>Add location data to your RSS feeds.  The <a href="rss.php">FeedWax geocoder</a> will make a best guess of relevant locations based on the content in your feed and add latitude and longitude to each item in your feed for you.</p>
          <!-- <p><a class="btn" href="anyrss.php">View details &raquo;</a></p> -->
        </div>
        <div class="span4">
          <h2>Social Media</h2>
          <p>Stream hashtagged tweets, Instagrams and YouTube videos covering a location you care about.  Use your feed to track music festivals, protest movements, and evolving news stories in your area.</p>
          <!-- <p><a class="btn" href="twitter.php">View details &raquo;</a></p> -->
       </div>
        <div class="span4">
          <h2>Live Searches</h2>
          <p>Search for local stories via news aggregators.  Curate sources using search terms and generate new location-aware RSS feeds that include headlines, excerpts, links and geotags.</p>
          <!-- <p><a class="btn" href="instagram.php">View details &raquo;</a></p> -->
        </div>
	</div>

<?php include('footer.php'); ?>