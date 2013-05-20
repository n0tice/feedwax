<?php
include("header.php");
include("nav.php");
?>

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>Something didn't work. Sorry.</h1>
        <h2>Try again? Or search below...</h2>
	</div>
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
<button type="radio" value="googlenews.php" class="btn-large" name="source"><i class="icon-fire"></i> google news</button> 
</form>
        <p>FEEDWAX helps you curate news, photos, video, tweets, data, and links - streams of information about what's happening in your local area right now.  Use FEEDWAX to build location-aware media streams and feed them into n0tice.com or any RSS-friendly platform.  </p>
      </div>


<?php include('footer.php'); ?>