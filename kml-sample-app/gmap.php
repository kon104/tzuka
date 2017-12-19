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

<style>
body {
	margin: 0px;
}

#container{
    display: flex;
}
#side{
    width: 300px;
	height: 100%;
	overflow: auto;
}
#control{
	width: 100%;
	height: 100%;
}
#main{
    flex: 1;
	height: 99.5%;
}

</style>

</head>
<body>

<div id="container">

	<div id="side">
		<div class="control">

			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a href="#tab1" class="nav-link navbar-default active" data-toggle="tab">自治会選択</a>
				</li>
				<li class="nav-item">
					<a href="#tab2" class="nav-link navbar-default" data-toggle="tab">住所判定</a>
				</li>
			</ul>

			<div class="tab-content">
				<div id="tab1" class="tab-pane active">
<?php
	GenerateHtml::partCommunityTree($kmlxmls);
?>
					<div>
						<div>lat: <span id="lat">---</spans></div>
						<div>lng: <span id="lng">---</spans></div>
					</div>

				</div>
				<div id="tab2" class="tab-pane">
					<div>
						<input type="button" id="btn" class="btn btn-primary" onClick="buttonClick()" value="判定" />
					</div>
					<div>
						<textarea name="textarea" id="txtarea" cols="28" rows="5" placeholder="number,address">1,兵庫県宝塚市宮の町１０−３
2,兵庫県宝塚市武庫川町7-23</textarea>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="main">
		<div id="map"></div>
		<input id="pac-input" class="controls" type="text" placeholder="Search Box" />
	</div>

</div>

<script>

var map;
var kmlLayers = [];
var markers = [];

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
		document.getElementById('lat').innerHTML = e.latLng.lat();
		document.getElementById('lng').innerHTML = e.latLng.lng();
		placeMarker(map, kmlLayers, markers, e.latLng, null);
	});
}

function placeMarker(map, layers, markers, latlng, caption)
{
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

	var content = '<div>lat: ' + latlng.lat() + '</div><div>lng: ' + latlng.lng() + '</div>';
	if (caption !== null) {
		content = '<div>' + caption + '</div>' + content;
	}
	var info = new google.maps.InfoWindow(
	{
		content: content,
	});
	marker.addListener('click', function(){
		info.open(map, marker);
	});

	markers.push(marker);

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

function buttonClick()
{
	for(var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	}

	var geocoder = new google.maps.Geocoder();

	var str =  document.getElementById("txtarea").value;
	var lines = str.split(/\r\n|\r|\n/);
	for(var i = 0; i < lines.length; i++) {
		var items = lines[i].split(',');
		var number = items[0];
		(function(){
			var num = number;
			geocoder.geocode({'address': items[1]}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					placeMarker(map, kmlLayers, markers, results[0].geometry.location, "#" + num);
				} else {
					console.log('Geocode was not successful for the following reason: ' + status);
				}
			});
		})();
	}
}

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwOoWKGysSQwWN1QWByK-lJCVnoWcxl_o&libraries=places&callback=initMap"></script>

</body>
</html>
