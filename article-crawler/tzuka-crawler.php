<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Goutte\Client();
$url = 'http://www.city.takarazuka.hyogo.jp/kenkofukushi/koreisha/';

browse($client, $url);

function browse($client, $url)
{
	$crawler = $client->request('GET', $url);

	$content = $crawler->filter('div#content');
	$idnumber = $content->filter('span.idnumber');

	if (count($idnumber) === 0) {
		// 記事一覧
//		printf("%s\t%s\t%s\n", 'list', $url, '');
		$links = array();
		$content->filter('a')->each(function ($node) use (&$links) {
			$link = $node->attr('href');
			$link = preg_replace('/index\.html$/', '', $link);
			$links[] = $link;
		});
		foreach($links as $i => $link) {
			$goto = $url . $link;
			browse($client, $goto);
		}

	} else {
		// 末端の記事
		$title = $content->filter('h1')->text();
//		printf("%s\t%s\t%s\n", 'art', $url, $title);

		$dt = $content->filter('dt');
		if (count($dt) > 0) {
			$dt->each(function ($node) {
				echo $node->text() . "\n";
			});
		}

	}
}
