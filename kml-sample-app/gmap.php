<?php

	require_once("./MyCurl.class.inc");
	require_once("./MyCoord.class.inc");

	$results = MyCurl::execMulti(MyCoord::$kmlUrls);
	$kmlxmls = array();
	foreach($results as $response) {
		$kmlxmls[] = new SimpleXMLElement($response['body']);
	}

?>
<html>
<head>
<style type="text/css"> 
#map {
	width: 100%;
	height: 80%;
}




      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      .pac-container {
        font-family: Roboto;
      }



</style>

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

	for(var i = 0; i < kmlUrls.length; i++)
	{
		var kmlLayer = new google.maps.KmlLayer(kmlUrls[i], {
			clickable: false,
			suppressInfoWindows: true,
			preserveViewport: false,
			map: map
		});
	}

	// create class of polygon from KML
	var kmlPolygons = [];
<?php	foreach($kmlxmls as $xml) { ?>
	kmlPolygons.push(new google.maps.Polygon({
		paths: [
<?php
			$value = $xml->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates;
			$lines = preg_split("/[\s]+/", trim($value));
			foreach($lines as $pos) {
				$lnglat = explode(',', $pos);
				printf("\t\tnew google.maps.LatLng(%s , %s),\n", $lnglat[1], $lnglat[0]);
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
