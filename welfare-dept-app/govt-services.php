<?php

require_once("./MyCurl.class.inc");

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

<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
<script type="text/javascript" src="http://www.n--log.net/demo/js/jquery.shuffle.min.js"></script>

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
 
#animationList {
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
</style>

</head>
<body>

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


<hr />

<div>

<ul id="btn">
    <li data-group="all" class="active alpha">ALL</li>
	<li data-group="age40" class="alpha">40-49</li>
	<li data-group="age50" class="alpha">50-59</li>
	<li data-group="age60" class="alpha">60-64</li>
	<li data-group="age65" class="alpha">65-69</li>
	<li data-group="age70" class="alpha">70-74</li>
	<li data-group="age75" class="alpha">75-</li>
</ul>

<ul id="animationList">
<?php
	foreach($services as $idx => $items) {
		echo "\t<li data-groups='$ages[$idx]'><div>$items[0]</div></li>\n";
	}
?>
</ul>

</div>

<script>
$(function() {
	$(".dial").knob({
		'release' : function(v) {
			console.log(v);

			var $age = "age0";
			if (v >= 75) $age = "age75"; 
			else if (v >= 70) $age = "age70";
			else if (v >= 65) $age = "age65";
			else if (v >= 60) $age = "age60";
			else if (v >= 50) $age = "age50";
			else if (v >= 40) $age = "age40";

			var $grid = $('#animationList');
			$grid.shuffle($age);
		}
	});
});
</script>

<script>
    $(function() {
        $('#btn li').on('click', function() {
            var $this = $(this),
                $grid = $('#animationList');
                 
            $('#btn .active').removeClass('active');
            $this.addClass('active');
            $grid.shuffle($this.data('group'));
//            $grid.shuffle("age70");
        });
        $('#animationList').shuffle({
            group: 'all',
            speed: 700,
            easing: 'ease-in-out'
        });
    });
</script>



</body>
</html>
