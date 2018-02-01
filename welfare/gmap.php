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


div.alert {
	background-color:#FFEFEF;
	margin:0 0 1em 0; padding:10px;
	color:#C25338;
	border:1px solid #D4440D;
	line-height:1.5;
	clear:both;
	background-repeat:no-repeat;
	background-position:5px 5px;
}
div.warning {
	background-color:#ffff80;
	border-color:#E5A500;
	color:#CC7600;
}
/*
div.warning span { filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/content/img/css/warning_48.png', sizingMethod='scale'); }
html>body div.warning { background-image:url(/content/img/css/warning_48.png); }
*/
html>body div.warning span { visibility:hidden; }

</style>

</head>
<body>

<div id="container">

	<div id="side">
		<div class="control">

			<ul id="tab_menu">
				<li><a href="#tab1" class="current">区域指定</a></li>
				<li><a href="#tab2" class="">区域内判定</a></li>
			</ul>

			<div id="tab_contents">

				<div id="tab1" class="divtab">
				<div class="alert warning"><span>警告</span>25ポリゴンまで選択可能</div>
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
		zoom: 14
	});

	// set a behavior to search a place
	behaviorSearchBox(map);

	// draw polygons on KML
<?php
	foreach($kmlxmls as $index => $kml) {
		echo "\tkmlLayers[$index] = {\n";
		printf("\t\turl: \"%s\",\n", $kml['url']);
		echo "\t\tlayer: null,\n";
		echo "\t\tpolygon: null,\n";
		echo "\t\tvisible: false\n";
		echo "\t};\n";
	}
?>

	for(var key in kmlLayers) {
		var layer = new google.maps.KmlLayer(kmlLayers[key].url, {
			clickable: false,
			suppressInfoWindows: true,
			preserveViewport: true,
		});
//		layer.setMap(map);
		kmlLayers[key].layer = layer;
	}

	// create class of polygon from KML
<?php	foreach($kmlxmls as $i => $kml) { ?>
	kmlLayers['<?php echo $i; ?>'].polygon = new google.maps.Polygon({
		paths: [
<?php
			if ($kml['coordinates'] !== null) {
				foreach($kml['coordinates'] as $pos) {
					printf("\t\t\tnew google.maps.LatLng(%s , %s),\n", $pos['lat'], $pos['lng']);
				}
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
	for(var idx = 0, count = 0; (idx < lines.length) && (count < 10); idx++) {

		if (lines[idx].indexOf('#') === 0) {
			continue;
		}

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
//					console.log('Geocode was not successful for the following reason: ' + status);
//					cell3.innerHTML = "-";
					cell3.innerHTML = status;
				}
			});
		})();
		count++;
	}

}

</script>

<script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initMap&key=<?php echo APPID_GMAP; ?>"></script>

</body>
</html>
