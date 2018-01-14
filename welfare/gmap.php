<?php

	require_once("./MyCoord.class.inc");
	require_once("./GenerateHtml.class.inc");
	require_once("./define.inc");

	$kmlxmls = MyCoord::fetchKmlXmls(MyCoord::$kmlUrls);
?>
<html>
<head>
<meta http-equiv="Content-Type" Content="text/html;charset=UTF-8">
<?php
	GenerateHtml::cssJsTree();
?>
<link rel="stylesheet" href="./style/map.css" type="text/css">
<link rel="stylesheet" href="./style/searchbox.css" type="text/css">
<link rel="stylesheet" href="./style/tabmenu.css" type="text/css">

<?php
	GenerateHtml::jsJQuery();
	GenerateHtml::jsJsTree();
?>
<script type="text/javascript" src="./jsg/searchbox.js"></script>
<script type="text/javascript" src="./jsg/tabmenu.js"></script>

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

			<ul id="tab_menu">
				<li><a href="#tab1" class="current">tab1</a></li>
				<li><a href="#tab2" class="">tab2</a></li>
			</ul>

			<div id="tab_contents">

				<div id="tab1" class="divtab">
<?php
	GenerateHtml::partCommunityTree(MyCoord::$kmlUrls, $kmlxmls);
?>
				</div>

				<div id="tab2" class="divtab">
					<div class="side">
						<a id="btn_classify" onClick="buttonClick()">判定開始(10箇所毎)</a>
					</div>
					<div class="sideend">
						<a href="https://raw.githubusercontent.com/kon104/tzuka/master/welfare/data/emergency-station.csv" target="_blank">sample</a>
					</div>
					<div>
						<textarea name="textarea" id="txtarea" cols="28" rows="5" placeholder="number,address">1,兵庫県宝塚市宮の町１０−３
2,兵庫県宝塚市武庫川町7-23</textarea>
					</div>
					<div>
						<table id="tbl_classify">
							<tr><th>#</th><th>住所</th><th>判定</th><tr>
						</table>
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
	var kmlUrls = [];
<?php
	foreach($kmlxmls as $index => $kml) {
		printf("\t\tkmlUrls['%d'] = \"%s\";\n", $index, $kml['url']);
	}
?>

	for(var key in kmlUrls)
	{
		var layer = new google.maps.KmlLayer(kmlUrls[key], {
			clickable: false,
			suppressInfoWindows: true,
			preserveViewport: false,
		});
//		layer.setMap(map);
		kmlLayers[key] = {
			layer: layer,
			polygon: null,
			visible: false
		};
	}

	// create class of polygon from KML
<?php	foreach($kmlxmls as $i => $kml) { ?>
	kmlLayers['<?php echo $i; ?>'].polygon = new google.maps.Polygon({
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
		placeMarker(map, kmlLayers, markers, e.latLng, null);
	});
}

function placeMarker(map, layers, markers, latlng, caption)
{
	var pinWithin = false;
	for(var key in layers) {
		if (pinWithin == true ) {
			break;
		}
		if (layers[key].visible == false) {
			continue;
		}
		pinWithin = google.maps.geometry.poly.containsLocation(latlng, layers[key].polygon);
	}

	var pinColor = null;
	if (pinWithin == true) {
		pinColor = '6495ed';
	} else {
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

	return pinWithin;
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
		if (items[0] == 'kmlroot') continue;
		checks[num] = num;
	}
	for(var key in kmlLayers) {
		if (kmlLayers[key].visible === true) {
			if (typeof checks[key] === "undefined") {
				kmlLayers[key].layer.setMap(null);
				kmlLayers[key].visible = false;
			}
		} else {
			if (typeof checks[key] !== "undefined") {
				kmlLayers[key].layer.setMap(map);
				kmlLayers[key].visible = true;
			}
		}
	}
});

function buttonClick()
{
	// clear all rows in table
	var table = document.getElementById("tbl_classify");
	while(table.rows[1]) table.deleteRow(1);

	// clear all marker in map
	for(var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	}

	var geocoder = new google.maps.Geocoder();

	var str =  document.getElementById("txtarea").value;
	var lines = str.split(/\r\n|\r|\n/);

	// There is a limit until 10 to call count of G's api per second.
	for(var idx = 0; (idx < lines.length) && (idx < 10); idx++) {

		var items = lines[idx].split(',');
		var number = items[0];
		var address = items[1];
		var row = table.insertRow(-1);
		(function(){
			var num = number;
			var adr = address;

			var cell1 = row.insertCell(-1);
			var cell2 = row.insertCell(-1);
			var cell3 = row.insertCell(-1);
			cell1.innerHTML = num;
			cell2.innerHTML = adr;

			geocoder.geocode({'address': adr}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var pinWithin = placeMarker(map, kmlLayers, markers, results[0].geometry.location, "#" + num);
					cell3.innerHTML = pinWithin;
				} else {
					console.log('Geocode was not successful for the following reason: ' + status);
					cell3.innerHTML = "-";
				}
			});
		})();
	}

}

</script>

<script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initMap&key=<?php echo APPID_GMAP; ?>"></script>

</body>
</html>
