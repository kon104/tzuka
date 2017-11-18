<?php

	$center = array('lat' => 34.81123119501047, 'lng' => 135.35563945770264);

?>
<html>
<head>
<meta http-equiv="Content-Type" Content="text/html;charset=UTF-8">

<!-- jquery/bootstrap/mine -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<link rel="stylesheet" href="./style/map.css" type="text/css">

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3">
			<div id="kml_tree">
				<ul>
					<li  id="kml_root" data-jstree='{"opened": true, "selected": true}'>111
					<ul>
						<li id="kml_0" data-jstree='{"icon": "jstree-file"}'>花のみち 第一地区</li>
						<li id="kml_1" data-jstree='{"icon": "jstree-file"}'>花のみち 第二地区</li>
						<li id="kml_2" data-jstree='{"icon": "jstree-file"}'>花のみち 第三地区</li>
						<li id="kml_3" data-jstree='{"icon": "jstree-file"}'>花のみち 第四地区</li>
						<li id="kml_4" data-jstree='{"icon": "jstree-file"}'>米谷自治会</li>
					</ul></li>
				</ul>
			</div>
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

	ymap.drawMap(new Y.LatLng(<? php echo $center['lat'] . ', ' . $center['lng']; ?>, 16, Y.LayerSetId.NORMAL);

	var kmlUrls = [
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-1st.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-2nd.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-3rd.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-4th.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/yoneya.kml"
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
