<?php

	require_once("./MyCoord.class.inc");
	require_once("./GenerateHtml.class.inc");

	$kmlxmls = MyCoord::fetchKmlXmls();
?>
<html>
<head>
<meta http-equiv="Content-Type" Content="text/html;charset=UTF-8">
<?php
	GenerateHtml::cssJsTree();
	GenerateHtml::cssBootStrap();
?>
<link rel="stylesheet" href="./style/map.css" type="text/css">
<link rel="stylesheet" href="./style/searchbox.css" type="text/css">

<?php
	GenerateHtml::jsJQuery();
	GenerateHtml::jsJsTree();
	GenerateHtml::jsBootStrap();
?>
<script type="text/javascript" src="./jsg/searchbox.js"></script>
</head>
<body>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3">
<?php
	GenerateHtml::partCommunityTree($kmlxmls);
?>
			<div>
				<div>lat: <span id="lat">---</spans></div>
				<div>lng: <span id="lng">---</spans></div>
			</div>
		</div>
		<div class="col-sm-9">
			<div id="map"></div>
			<input id="pac-input" class="controls" type="text" placeholder="Search Box" />
		</div>
	</div>
</div>

<script>

var map;
var kmlLayers = [];

function initMap() {
	// display the map
	map = new google.maps.Map(document.getElementById('map'), {
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
	foreach($kmlxmls as $kml) {
		printf("\t\t\"%s\",\n", $kml['url']);
	}
?>
	];

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
			polygon: null,
			visible: true
		});
	}

	// create class of polygon from KML
<?php	foreach($kmlxmls as $i => $kml) { ?>
	kmlLayers[<?php echo $i; ?>].polygon = new google.maps.Polygon({
		paths: [
<?php
			foreach($kml['coordinates'] as $pos) {
				printf("\t\t\tnew google.maps.LatLng(%s , %s),\n", $pos['lat'], $pos['lng']);
			}
?>
		]
	});
<?php	} ?>

	map.addListener('click', function(e)
	{
		getClickLatLng(e.latLng, map, kmlLayers);
	});
}

function getClickLatLng(latlng, map, layers)
{
	document.getElementById('lat').innerHTML = latlng.lat();
	document.getElementById('lng').innerHTML = latlng.lng();

	var pinColor = null;
	for(var i = 0; (pinColor == null) && (i < layers.length); i++)
	{
		if (layers[i].visible == false) {
			continue;
		}
		pinColor = google.maps.geometry.poly.containsLocation(latlng, layers[i].polygon) ?
			'6495ed' :
			null;
	}
	if (pinColor == null) {
		pinColor = 'c0c0c0';
	}

	var imgBaseUrl = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|';
	var pinImage = new google.maps.MarkerImage(imgBaseUrl + pinColor);

	var marker = new google.maps.Marker(
	{
		position: latlng,
		map: map,
		icon: pinImage,
	});

	var info = new google.maps.InfoWindow(
	{
		content: '<div>lat: ' + latlng.lat() + '</div><div>lng: ' + latlng.lng() + '</div>',
	});
	marker.addListener('click', function(){
		info.open(map, marker);
	});


}

$(function () {
	$('#kml_tree').jstree({
		"plugins": ["checkbox"]
	});
});

$('#kml_tree').on("changed.jstree", function (e, data) {
	if (kmlLayers.length == 0) {
		return;
	}
	var checks = [];
	for(var i = 0; i < data.selected.length; i++) {
		var items = data.selected[i].split("_");
		var num = items[1];
		if (num == 'root') continue;
		checks[num] = num;
	}
	for(var i = 0; i < kmlLayers.length; i++) {
		if (kmlLayers[i].visible === true) {
			if (typeof checks[i] === "undefined") {
				kmlLayers[i].layer.setMap(null);
				kmlLayers[i].visible = false;
			}
		} else {
			if (typeof checks[i] !== "undefined") {
				kmlLayers[i].layer.setMap(map);
				kmlLayers[i].visible = true;
			}
		}
	}
});

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwOoWKGysSQwWN1QWByK-lJCVnoWcxl_o&libraries=places&callback=initMap"></script>

</body>
</html>
