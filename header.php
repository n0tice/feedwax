<?php 
header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<?php
  $cache = '';
  if(isset($_GET['eraseCache'])){
    echo '<meta http-equiv="Cache-control" content="no-cache">';
    echo '<meta http-equiv="Expires" content="-1">';
    $cache = '?'.time();
  }
?>
<meta charset="utf-8">
<title>FeedWax - Curate local information sources, news, photos, video and social media</title>
<meta name="description" content="FeedWax helps you curate sources covering things happening in your local area right now. Create location-aware media streams including news, photos, videos, social media, and data, and feed them into n0tice.com or any RSS-friendly platform or content management system.">
<meta property="og:type" content="website"/>
<meta property="og:title" content="FeedWax"/>
<meta property="og:description" content="FeedWax helps you curate sources covering things happening in your local area right now. Create location-aware media streams including news, photos, videos, social media, and data, and feed them into n0tice.com or any RSS-friendly platform or content management system."/>
<meta property="og:site_name" content="FeedWax"/>
<meta property="og:url" content="http://feedwax.com/"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-responsive.min.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 100px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }
        </style>

        <script src="/js/bootstrap.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>

        <!-- Google Geocoder stuff -->
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript">
            var map;
            var geocoder;
            var centerChangedLast;
            var reverseGeocodedLast;
            var currentReverseGeocodeResponse;

            function initialize() {
                var latlng = new google.maps.LatLng(32.5468,-23.2031);
                var myOptions = {
                    zoom: 2,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                geocoder = new google.maps.Geocoder();


                setupEvents();
                centerChanged();
            }

            function setupEvents() {
                reverseGeocodedLast = new Date();
                centerChangedLast = new Date();

                setInterval(function() {
                    if((new Date()).getSeconds() - centerChangedLast.getSeconds() > 1) {
                        if(reverseGeocodedLast.getTime() < centerChangedLast.getTime())
                            reverseGeocode();
                    }
                }, 1000);

                google.maps.event.addListener(map, 'zoom_changed', function() {
                    document.getElementById("zoom_level").innerHTML = map.getZoom();
                });

                google.maps.event.addListener(map, 'center_changed', centerChanged);

                google.maps.event.addDomListener(document.getElementById('crosshair'),'dblclick', function() {
                    map.setZoom(map.getZoom() + 1);
                });

            }

            function getCenterLatLngText() {
                return '(' + map.getCenter().lat() +','+ map.getCenter().lng() +')';
            }

            function centerChanged() {
                centerChangedLast = new Date();
                var latlng = getCenterLatLngText();
                document.getElementById('latlng').innerHTML = latlng;
                document.getElementById('formatedAddress').innerHTML = '';
                currentReverseGeocodeResponse = null;
            }

            function reverseGeocode() {
                reverseGeocodedLast = new Date();
                geocoder.geocode({latLng:map.getCenter()},reverseGeocodeResult);
            }

            function reverseGeocodeResult(results, status) {
                currentReverseGeocodeResponse = results;
                if(status == 'OK') {
                    if(results.length == 0) {
                        document.getElementById('formatedAddress').innerHTML = 'None';
                    } else {
                        document.getElementById('formatedAddress').innerHTML = results[0].formatted_address;
                    }
                } else {
                    document.getElementById('formatedAddress').innerHTML = 'Error';
                }
            }


            function geocode() {
                var address = document.getElementById("address").value;
                geocoder.geocode({
                    'address': address,
                    'partialmatch': true}, geocodeResult);
            }

            function geocodeResult(results, status) {
                if (status == 'OK' && results.length > 0) {
                    map.fitBounds(results[0].geometry.viewport);
                } else {
                    alert("Geocode was not successful for the following reason: " + status);
                }
            }

            function addMarkerAtCenter() {
                var marker = new google.maps.Marker({
                    position: map.getCenter(),
                    map: map
                });

                var text = 'Lat/Lng: ' + getCenterLatLngText();
                if(currentReverseGeocodeResponse) {
                    var addr = '';
                    if(currentReverseGeocodeResponse.size == 0) {
                        addr = 'None';
                    } else {
                        addr = currentReverseGeocodeResponse[0].formatted_address;
                    }
                    text = text + '<br>' + 'address: <br>' + addr;
                }

                var infowindow = new google.maps.InfoWindow({ content: text });

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
                });
            }

        </script>
        <!-- End Google Geocoder stuff -->
        <script type="text/javascript">
		
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-25949597-5']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>
    </head>