<?php
include('config/globals.php');
include('header.php');
include('nav.php');
?>

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>About FeedWax</h1>
        <p>FeedWax was inspired by <a href="http://n0tice.com">n0tice.com</a>, the open community noticeboard.</p>
        
        <p>We've been working on opening the n0tice API (more on <a href="http://n0tice.org">http://n0tice.org</a>) so that people could both push and pull data across the platform. Once that work was done it got us thinking...what would happen if you poured GeoRSS into it?</p>
        
        <p>We first built a RSS-to-n0tice importer called <a href="feedton0tice.com">feedton0tice.com</a>.  We built it in part to demonstrate the new writeable API and OAuth support, in part because it seemed like a useful way to bring aggregation to the platform.  That code is on Github here: <a href="https://github.com/n0tice/rss-to-n0tice">https://github.com/n0tice/rss-to-n0tice</a></p>

        <p>That experiment proved fruitful, so we pushed the idea further and started thinking about how to help others curate useful feeds.  One experiment led to another and we found ourselves looking at a fantastic curation tool here.</p>
        
        <p>We call it FeedWax.</p>
        
        <p>We think FeedWax will be most interesting to local community leaders and local publishers, people who want to collect information about what's happening around them.  The combination of FeedWax and n0tice then becomes a very powerful local information system.</p>
        
        <p>Along with the ability to geocode the RSS feeds you already produce, FeedWax helps you curate local information published by people on other useful community platforms such as Twitter, Flickr, Instagram, and YouTube.  You can also get streams of headlines coming from various news aggregators we work with such as Daylife, Bing, and Google News.</p>
        
        <p>We will add more sources as people start showing interest in different sources.  Please tell us if you want a source added.  Send us an email: feedwax@n0tice.com.</p>
        
        <p>The code was written in PHP.  The CSS is Bootstrap.  And the geocoding tool is a mixture of Geonames and Yahoo!'s ContentAnalysis via YQL.</p>
        
        <p>The code has been published with a GPL license on Github here: <a href="https://github.com/n0tice/feedwax">https://github.com/n0tice/feedwax</a></p>
        
        <p>Please use it, take it, rewrite it, improve it, or whatever you want to do.  We'll keep an eye on pull requests but make no promises about accepting them at the moment.  Don't take offense if that happens...we're just busy at the moment.</p>
        
        <p>Our intention is to offer this service for free forever.  We may need to charge for geocoding your RSS feeds if that starts costing us a lot of money.</p>
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