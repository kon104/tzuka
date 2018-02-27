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
<?php
	GenerateHtml::jsJQuery();
	GenerateHtml::jsJsTree();
?>

<?php
	GenerateHtml::jsGoogleAnalytics();
?>

<style>

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
<?php
	GenerateHtml::partCommunityTree(MyCoord::$kmlUrls, $kmlxmls);
?>
		</div>
	</div>
	<div id="main">
		<div id="map"></div>
	</div>
</div>

<script type="text/javascript" charset="utf-8" src="https://map.yahooapis.jp/js/V1/jsapi?appid=<?php echo APPID_YOLP; ?>"></script>

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
		if (items[0] == 'kmlroot') continue;
		checks[num] = num;
	}

	for(var key in kmlLayers) {
		if (kmlLayers[key].visible === true) {
			if (typeof checks[key] === "undefined") {
				ymap.removeLayer(kmlLayers[key].layer);
				kmlLayers[key].visible = false;
			}
		} else {
			if (typeof checks[key] !== "undefined") {
				ymap.addLayer(kmlLayers[key].layer);
				kmlLayers[key].layer.execute();
				kmlLayers[key].visible = true;
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

	var kmlUrls = [];
<?php
	foreach($kmlxmls as $index => $kml) {
		printf("\tkmlUrls['%d'] = \"%s\";\n", $index, $kml['url']);
	}
?>

	for(var key in kmlUrls) {
		var layer = new Y.GeoXmlLayer(kmlUrls[key]);
//		ymap.addLayer(layer);
		layer.execute();
		kmlLayers[key] = {
			layer: layer,
			visible: false
		};
	}

}

</script>

</body>
</html>
