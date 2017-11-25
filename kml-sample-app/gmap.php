<?php

	require_once("./MyCoord.class.inc");
	require_once("./GenerateHtml.class.inc");

	$kmlxmls = MyCoord::fetchKmlXmls();
?>
<html>
<head>
<style type="text/css"> 
#map {
	width: 100%;
	height: 80%;
}
</style>
<link rel="stylesheet" href="./style/searchbox.css" type="text/css">

<script type="text/javascript" src="./jsg/searchbox.js"></script>

</head>
<body>

<div id="map"></div>
<input id="pac-input" class="controls" type="text" placeholder="Search Box">
<ul>
	<li>lat: <span id="lat">---</spans></li>
	<li>lng: <span id="lng">---</spans></li>
</ul>

<script>
function initMap() {
	// display the map
	var map = new google.maps.Map(document.getElementById('map'), {
		center: {
			lat: <?php echo MyCoord::$center['lat']; ?>,
			lng: <?php echo MyCoord::$center['lng']; ?>
		},
		zoom: 16
	});

	// set a behavior to search a place
	behaviorSearchBox(map);

	// draw polygons on KML
	var kmlUrls = [
<?php
	foreach(MyCoord::$kmlUrls as $kml) {
		printf("\t\t\"%s\",\n", $kml);
	}
?>
	];

	var kmlLayers = [];
	for(var i = 0; i < kmlUrls.length; i++)
	{
		var layer = new google.maps.KmlLayer(kmlUrls[i], {
			clickable: false,
			suppressInfoWindows: true,
			preserveViewport: false,
		});
		layer.setMap(map);
		kmlLayers.push({
			layer: layer,
			visible: true
		});
	}

	// create class of polygon from KML
	var kmlPolygons = [];
<?php	foreach($kmlxmls as $kml) { ?>
	kmlPolygons.push(new google.maps.Polygon({
		paths: [
<?php
			foreach($kml['coordinates'] as $pos) {
				printf("\t\t\tnew google.maps.LatLng(%s , %s),\n", $pos['lat'], $pos['lng']);
			}
?>
		]
	}));
<?php	} ?>

	map.addListener('click', function(e)
	{
		getClickLatLng(e.latLng, map, kmlPolygons);
	});
}

function getClickLatLng(latlng, map, polygons)
{
	document.getElementById("lat").innerHTML = latlng.lat();
	document.getElementById('lng').innerHTML = latlng.lng();

	var imgBaseUrl = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|';
	var pinImage = null;
	for(var i = 0; (pinImage == null) && (i < polygons.length); i++)
	{
		pinImage = google.maps.geometry.poly.containsLocation(latlng, polygons[i]) ?
			new google.maps.MarkerImage(imgBaseUrl + '6495ed') :
			null;
	}

	var marker = new google.maps.Marker(
	{
		position: latlng,
		map: map,
		icon: pinImage,
	});
}

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwOoWKGysSQwWN1QWByK-lJCVnoWcxl_o&libraries=places&callback=initMap"></script>

</body>
</html>
