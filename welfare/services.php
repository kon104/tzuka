<?php

	require_once("./MyCurl.class.inc");
	require_once("./GenerateHtml.class.inc");

	define("FONT_CHK", "<i class=\"fa fa-check-square-o\" aria-hidden=\"true\">%s</i>&nbsp;");


	$datasrc = array();
	$datasrc[] = array('高齢者向けサービス' =>
			"https://raw.githubusercontent.com/kon104/tzuka/master/open-data/sample/welfare-dept/govt-services.csv");
	$datasrc[] = array('検診サービス' =>
			"http://lab-tkonuma-101.ssk.ynwm.yahoo.co.jp/users/tkonuma/tzuka/welfare/medical-exam.csv");

	$appoint = $_GET['a'];

	$csvUrls = array();
	$pagetitle = "";
	if (is_null($appoint) === true) {
		foreach($datasrc as $num => $src) {
			$csvUrls[$num] = array_values($src)[0];
		}
		$pagetitle = "サービス全般";
	} else {
		$csvUrls[$appoint] = array_values($datasrc[$appoint])[0];
		$pagetitle = reset(array_keys($datasrc[$appoint]));
	}

	$results = MyCurl::execMulti($csvUrls);

	$services = array();
	foreach($results as $key => $response) {
		$rows = explode("\r\n", $response['body']);
		unset($rows[0]);
		$services = array_merge($services, $rows);
	}

	$services = array_values($services);

	foreach($services as $idx => $service) {
		$items = str_getcsv($service);
		$services[$idx] = $items;
	}

	$sort_4049 = array();
	$sort_5059 = array();
	$sort_6064 = array();
	$sort_6569 = array();
	$sort_7074 = array();
	$sort_75xx = array();
	$sort_name  = array();

	foreach($services as $idx => $items) {
		$sort_4049[$idx] = $items[1];
		$sort_5059[$idx] = $items[2];
		$sort_6064[$idx] = $items[3];
		$sort_6569[$idx] = $items[4];
		$sort_7074[$idx] = $items[5];
		$sort_75xx[$idx] = $items[6];
		$sort_name[$idx] = $items[0];
	}
	$aaa = array_multisort(
		$sort_75xx, SORT_ASC,
		$sort_7074, SORT_ASC,
		$sort_6569, SORT_ASC,
		$sort_6064, SORT_ASC,
		$sort_5059, SORT_ASC,
		$sort_4049, SORT_ASC,
		$sort_name, SORT_DESC, SORT_STRING,
		$services
	);

	$ages = array();
	foreach($services as $idx => $items) {

		foreach($items as $j => $item) {
			$item = str_replace("\n", "<br/>", $item);
			if ($item === "") {
				$item = "---";
			} else if ($j === 20) {
				$item = sprintf("<a href=\"%s\">詳細ページあり</a>", $item);
			}
			$items[$j] = $item;
		}
		$services[$idx] = $items;

		$age = array();
		if ($items[1] === "1") $age[] = "\"age40\"";
		if ($items[2] === "1") $age[] = "\"age50\"";
		if ($items[3] === "1") $age[] = "\"age60\"";
		if ($items[4] === "1") $age[] = "\"age65\"";
		if ($items[5] === "1") $age[] = "\"age70\"";
		if ($items[6] === "1") $age[] = "\"age75\"";
		$ages[$idx] = "[" . implode(",", $age) . "]";
	}

	$default = array();
	$default['age'] = 60;
	$default['filter'] = "age60";

	$myurl = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

?>
<html>
<head>
<meta http-equiv="Content-Type" Content="text/html;charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
	GenerateHtml::cssBootStrap();
?>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
<?php
	GenerateHtml::jsJQuery();
	GenerateHtml::jsBootStrap();
?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>

<style>
body {
	padding-top: 10px;
	background-color: #FFFFAA;
}

.container-fluid
{
	margin-right: auto;
	margin-left: auto;
	max-width: 900px;
}

#age_title {
	text-align: center;
}

#age_chart {
	text-align: center;
}

#service_count {
	text-align: center;
}

#qrcode {
	text-align: center;
}

#qrcode div {
	padding: 5 0px;
}

#list_filter ul {
	padding: 0px;
}
#list_filter li {
	display: inline;
	padding: 0 5px;
}

#list_service a {
	text-decoration: none;
}

#list_service th {
	white-space: nowrap;
}

</style>

</head>
<body>

<div id="header">
<!--
	<nav class="navbar navbar-inverse navbar-fixed-top">
	<nav class="navbar fixed-top navbar-expand-sm navbar-dark bg-dark">
-->
	<nav class="navbar fixed-top navbar-dark bg-primary2" style="background: coral;">
<!--
	<div class="container-fluid">
-->
	<div><h3><?php echo $pagetitle; ?></h3></div>
<!--
	</div>
-->
	</nav>
</div>

<div class="container-fluid">

	<div class="row">
		<div class="col-sm-5">

<div id="age_title"><h2>あなたの年齢は？</h2></div>
<div id="age_chart">
	<input type="text" class="dial"
		value="<?php echo $default['age']; ?>"
		data-min="35"
		data-max="80"
		data-width="300"
		data-height="235"
		data-angleOffset="-115"
		data-angleArc="230"
		data-fgColor="mediumorchid"
		data-linecap="round"
	>
</div>
<div id="service_count"><h5></h5></div>

<div id="qrcode" class="d-none d-sm-block">
	<div><img src="http://chart.apis.google.com/chart?chs=150x150&cht=qr&chl=<?php echo urlencode($myurl); ?>"></div>
	<div><h6>スマートフォンで<br />アクセス</h6></div>
</div>

		</div>
		<div class="col-sm-7">

<!--
<div id="list_filter">
	<ul class="filters">
		<li data-filter="all" class="active alpha">全て</li>
		<li data-filter="age40" class="alpha">40 〜 49歳</li>
		<li data-filter="age50" class="alpha">50 〜 59歳</li>
		<li data-filter="age60" class="alpha">60 〜 64歳</li>
		<li data-filter="age65" class="alpha">65 〜 69歳</li>
		<li data-filter="age70" class="alpha">70 〜 74歳</li>
		<li data-filter="age75" class="alpha">75歳以上</li>
	</ul>
</div>
-->

<div id="list_service" class="boxes">
<?php
	foreach($services as $idx => $items) {
		echo "\t<div class=\"card mb-2\" data-groups='$ages[$idx]'>\n";

		echo "\t<div class=\"card-header\"><a data-toggle=\"collapse\" data-parent=\"#list_service\" href=\"#service$idx\"><h4>$items[0]</h4></a></div>\n";

		echo "\t<div id=\"service$idx\" class=\"collapse\">\n";
		echo "\t<div class=\"card-body\">\n";

		echo "<table class=\"table table-bordered table-hover\">\n";
		echo "<tr><th class=\"table-info\">年齢要件</th><td>";
		if ($items[1] === "1") printf(FONT_CHK, "40-49歳");
		if ($items[2] === "1") printf(FONT_CHK, "50-59歳");
		if ($items[3] === "1") printf(FONT_CHK, "60-64歳");
		if ($items[4] === "1") printf(FONT_CHK, "65-69歳");
		if ($items[5] === "1") printf(FONT_CHK, "70-74歳");
		if ($items[6] === "1") printf(FONT_CHK, "75歳以上");
		echo "</td></tr>\n";
		echo "<tr><th class=\"table-info\">居住要件</th><td>$items[8]</td></tr>\n";
		echo "<tr><th class=\"table-info\">所得要件</th><td>$items[10]</td></tr>\n";
		echo "<tr><th class=\"table-info\">介護要件</th><td>$items[11]</td></tr>\n";
		echo "<tr><th class=\"table-info\">障害要件</th><td>$items[12]</td></tr>\n";
		echo "<tr><th class=\"table-info\">その他<br />要件</th><td>$items[14]</td></tr>\n";
		echo "<tr><th class=\"table-info\">窓口</th><td>$items[16]</td></tr>\n";
		echo "<tr><th class=\"table-info\">内容</th><td>$items[17]</td></tr>\n";
		echo "<tr><th class=\"table-info\">自己負担</th><td>$items[18]</td></tr>\n";
		echo "<tr><th class=\"table-info\">備考</th><td>$items[19]</td></tr>\n";
		echo "<tr><th class=\"table-info\">詳細情報</th><td>$items[20]</td></tr>\n";
		echo "</table>\n";

		echo "\t</div>\n";
		echo "\t</div>\n";

		echo "\t</div>\n";
	}
?>
</div>

		</div>
	</div>
</div>

<script>

function showAllItems(boxes)
{
	boxes.removeClass('is-animated')
		.fadeOut().promise().done(function() {
			boxes.addClass('is-animated').fadeIn();

			var count = boxes.parent().find('.is-animated').length;
			showItemCount(count);
	});
}

function showFilterItems(boxes, filter)
{

	boxes.removeClass('is-animated')
		.fadeOut().promise().done(function() {
			boxes.filter('[data-groups *= "' + filter + '"]')
				.addClass('is-animated').fadeIn();

			var count = boxes.parent().find('.is-animated').length;
			showItemCount(count);
	});
}

function showItemCount(count)
{
	console.log("###" + count);
	$('#service_count h5').text(count + "件 見つかりました");
}

$(function() {

	'use strict';

	var filters = $('.filters [data-filter]'),
	    boxes = $('.boxes [data-groups]');

	showFilterItems(boxes, "<?php echo $default['filter']; ?>");

	filters.on('click', function(e) {
		e.preventDefault();
		var self = $(this);

//		filters.removeClass('active');
//		self.addClass('active');

		var filter = self.attr('data-filter');

		if (filter == 'all') {
			showAllItems(boxes);
		} else {
			showFilterItems(boxes, filter);
		}
	});

	$(".dial").knob({
		'release' : function(v) {
			console.log(v);

			var filter = "age0";
			if (v >= 75) filter = "age75"; 
			else if (v >= 70) filter = "age70";
			else if (v >= 65) filter = "age65";
			else if (v >= 60) filter = "age60";
			else if (v >= 50) filter = "age50";
			else if (v >= 40) filter = "age40";

		    var boxes = $('.boxes [data-groups]');
			showFilterItems(boxes, filter);
		}
	});
});

</script>

</body>
</html>
