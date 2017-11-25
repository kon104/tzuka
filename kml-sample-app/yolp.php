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
<?php
	GenerateHtml::jsJQuery();
	GenerateHtml::jsJsTree();
	GenerateHtml::jsBootStrap();
?>
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3">
<?php
	GenerateHtml::partCommunityTree($kmlxmls);
?>
		</div>
		<div class="col-sm-9">
			<div id="map"></div>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8" src="https://map.yahooapis.jp/js/V1/jsapi?appid=dj00aiZpPXNxSnVwYngxM1luNCZzPWNvbnN1bWVyc2VjcmV0Jng9YWU-"></script>

<script type="text/javascript">

var ymap;
var kmlLayers = new Array();

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

	for(var i =0; i < kmlLayers.length; i++) {
		if (kmlLayers[i].visible === true) {
			if (typeof checks[i] === "undefined") {
				ymap.removeLayer(kmlLayers[i].layer);
				kmlLayers[i].visible = false;
			}
		} else {
			if (typeof checks[i] !== "undefined") {
				ymap.addLayer(kmlLayers[i].layer);
				kmlLayers[i].layer.execute();
				kmlLayers[i].visible = true;
			}
		}
	}

});

window.onload = function(){

	ymap = new Y.Map("map");

	ymap.setConfigure('doubleClickZoom', true);
	ymap.setConfigure('scrollWheelZoom', true);
	ymap.setConfigure('continuousZoom', true);
	ymap.setConfigure('weatherOverlay', true);

//	ymap.addControl(new Y.CenterMarkControl());
	ymap.addControl(new Y.LayerSetControl());
	ymap.addControl(new Y.ScaleControl());
	ymap.addControl(new Y.SliderZoomControlHorizontal());
	ymap.addControl(new Y.SearchControl());

	ymap.drawMap(new Y.LatLng(<?php printf("%.14f, %.14f", MyCoord::$center['lat'], MyCoord::$center['lng']); ?>), 16, Y.LayerSetId.NORMAL);

	var kmlUrls = [
<?php
	foreach($kmlxmls as $kml) {
		printf("\t\t\"%s\",\n", $kml['url']);
	}
?>
	];

	for(var i = 0; i < kmlUrls.length; i++)
	{
		var layer = new Y.GeoXmlLayer(kmlUrls[i]);
		ymap.addLayer(layer);
		layer.execute();
		kmlLayers.push({
			layer: layer,
			visible: true
		});
	}

}

</script>

</body>
</html>
