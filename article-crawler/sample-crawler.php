<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Goutte\Client();

$url = 'http://stocks.finance.yahoo.co.jp/us/profile/AAPL';
$crawler = $client->request('GET', $url);
 
$target = 'table.boardFinCom tr';
//	$target = 'table.boardFinCom';
$crawler->filter($target)->each(function($node) {
	echo "---------->\n";
/*
	echo "<dt>" . $node->filter('th')->text() . "</dt><br>\n";
	echo "<dd>" . $node->filter('td')->text() . "</dd><br>\n";
*/
	echo $node->html();
});


