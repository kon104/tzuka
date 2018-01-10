<?php

	require_once("./MyCurl.class.inc");
	require_once("./GenerateHtml.class.inc");

	$csvUrls = array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/open-data/sample/welfare-dept/govt-services.csv"
	);
	$results = MyCurl::execMulti($csvUrls);


	$services = explode("\r\n", $results[0]['body']);
	unset($services[0]);
	$services = array_values($services);

	$ages = array();
	foreach($services as $idx => $service) {
		$items = str_getcsv($service);

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

?>
<html>
<head>
<meta http-equiv="Content-Type" Content="text/html;charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
	GenerateHtml::cssBootStrap();
?>
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

#age_title {
	text-align: center;
}

#age_chart {
	text-align: center;
}

#service_count {
	text-align: center;
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

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-6">

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

		</div>
		<div class="col-sm-6">

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

//		echo "\t<div class=\"card-header\"><h4><a data-toggle=\"collapse\" data-parent=\"#list_service\" href=\"#service$idx\">$items[0]</a></h4></div>\n";
		echo "\t<div class=\"card-header\"><a data-toggle=\"collapse\" data-parent=\"#list_service\" href=\"#service$idx\"><h4>$items[0]</h4></a></div>\n";

		echo "\t<div id=\"service$idx\" class=\"collapse\">\n";
		echo "\t<div class=\"card-body\">\n";

		echo "<table class=\"table table-bordered table-hover\">\n";
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
