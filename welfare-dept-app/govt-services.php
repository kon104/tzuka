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

		$age = array();
		if ($items[1] === "1") $age[] = "\"age40\"";
		if ($items[2] === "1") $age[] = "\"age50\"";
		if ($items[3] === "1") $age[] = "\"age60\"";
		if ($items[4] === "1") $age[] = "\"age65\"";
		if ($items[5] === "1") $age[] = "\"age70\"";
		if ($items[6] === "1") $age[] = "\"age75\"";
		$ages[$idx] = "[" . implode(",", $age) . "]";

		$services[$idx] = $items;
	}


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
<!--
<script type="text/javascript" src="http://www.n--log.net/demo/js/jquery.shuffle.min.js"></script>
-->

<style>
/*
#btn {
    overflow: hidden;
    margin-bottom: 40px;
}
 
#btn li {
    float: left;
    margin: 10px;
    cursor: pointer;
}
*/
 
/*
#animationList {
	list-style: none;
//    overflow: hidden;
}

#animationList li {
    width: 500px;
    height: 100px;
//    padding: 10px;
//    float: left;
//    color: #fff;
}
 
#animationList li span {
//    display: block;
//    width: 180px;
//    height: 180px;
//    padding: 20px;
}
*/
</style>

</head>
<body>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-5">

<div>あなたの年齢は？</div>
<div>
	<input type="text" class="dial" value="60"
		data-min="30"
		data-max="80"
		data-angleOffset="-125"
		data-angleArc="250"
		data-fgColor="mediumorchid"
		data-linecap="round"
	>
</div>

		</div>
		<div class="col-sm-7">

<div>
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
<div>
	<ul class="boxes">
<?php
	foreach($services as $idx => $items) {
		echo "\t<li data-groups='$ages[$idx]'><div>$items[0]</div></li>\n";
	}
?>
	</ul>
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
	});
}

function showFilterItems(boxes, filter)
{
	boxes.removeClass('is-animated')
		.fadeOut().promise().done(function() {
			boxes.filter('[data-groups *= "' + filter + '"]')
				.addClass('is-animated').fadeIn();
	});
}

$(function() {

	'use strict';

	var filters = $('.filters [data-filter]'),
	    boxes = $('.boxes [data-groups]');

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
