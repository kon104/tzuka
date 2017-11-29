<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Goutte\Client();

$urls = array(
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/kenko/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/kenshin/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/koreisha/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/kaigohoken/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/shogaisha/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/shinshia/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/chiikifukushi/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/fukushikanren/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/seikatsushien/',
	'http://www.city.takarazuka.hyogo.jp/kenkofukushi/jisatsuyobo/',
);

printf("%s\t%s\n", "URL", "タイトル");
foreach($urls as $url) {
	echo $url . "\n";
	browse($client, $url);
}

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
		printf("%s\t%s", $url, $title);

		$dt = $content->filter('dt');
		if (count($dt) > 0) {
			$fields = array();
			$dt->each(function ($node) use (&$fields) {
//				echo $node->text() . "\n";
				$text = $node->text();
				$fields[$text] = $text;
			});
			echo "\t" . implode("\t", $fields);
		}

		echo "\n";
	}
}
