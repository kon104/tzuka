<?php

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
//    height: 220px;
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
		data-max="120"
		data-angleOffset="-125"
		data-angleArc="250"
		data-fgColor="mediumorchid"
		data-linecap="round"
	>
</div>

<script>
	$(".dial").knob({
		'release' : function(v) {
			console.log(v);
		}
	});
</script>

<hr />

<div>

<ul id="btn">
    <li data-group="all" class="active alpha">ALL</li>
    <li data-group="red" class="alpha">RED</li>
    <li data-group="blue" class="alpha">BLUE</li>
    <li data-group="green" class="alpha">GREEN</li>
    <li data-group="yellow" class="alpha">YELLOW</li>

	<li data-group="age40" class="alpha">40-49</li>
	<li data-group="age50" class="alpha">50-59</li>
	<li data-group="age60" class="alpha">60-69</li>
	<li data-group="age70" class="alpha">70-74</li>
	<li data-group="age75" class="alpha">75-</li>
</ul>
<ul id="animationList">
    <li data-groups='["age70","age75"]'><div>バス・タクシー運賃の一部助成</div></li>


    <li data-groups='["red"]'><span class="red">RED</span></li>
    <li data-groups='["yellow","red"]'><span class="yellow">YELLOW / RED</span></li>
    <li data-groups='["blue"]'><span class="blue">BLUE</span></li>
    <li data-groups='["green"]'><span class="green">GREEN</span></li>
    <li data-groups='["green"]'><span class="green">GREEN</span></li>
    <li data-groups='["yellow"]'><span class="yellow">YELLOW</span></li>
    <li data-groups='["blue"]'><span class="blue">BLUE</span></li>
    <li data-groups='["red"]'><span class="red">RED</span></li>
    <li data-groups='["red"]'><span class="red">RED</span></li>
    <li data-groups='["blue"]'><span class="blue">BLUE</span></li>
    <li data-groups='["yellow"]'><span class="yellow">YELLOW</span></li>
    <li data-groups='["green"]'><span class="green">GREEN</span></li>
</ul>

</div>

<script>
    $(function() {
        $('#btn li').on('click', function() {
console.log("Start");
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
