#!/usr/local/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Goutte\Client();

$urls = array(
	'http://www.city.takarazuka.hyogo.jp/1008153/1002632/',
//	'http://www.city.takarazuka.hyogo.jp/1008153/1002632/1003276.html',

//	'http://www.city.takarazuka.hyogo.jp/1008153/1002632/1003278.html',
);

$pages = array();
foreach($urls as $url) {
	$url = preg_replace('/index\.html$/', '', $url);
	browse($client, $url, $pages);
}

$items = makeCsvItem();

$line = implode(',', $items);
//	$line = mb_convert_encoding($line, 'Shift_JIS', 'UTF-8');
echo $line . "\n";

foreach($pages as $page)
{
	$line = '';
	foreach($items as $item)
	{
		if (isset($page[$item])) {
			$line .= "\"" . $page[$item] . "\"";
		}
		$line .= ",";
	}
//	$line = mb_convert_encoding($line, 'Shift_JIS', 'UTF-8');
	echo $line . "\n";
}

//	print_r($pages);


// {{{ function pageArticle($url, $content)
function pageArticle($url, $content)
{
	$fields = array();

	$title = $content->filter('h1')->text();
	$title = preg_replace('/の紹介$/', '', $title);

	$fields['施設名'] = $title;

	$fields['写真'] = dirname($url) . "/"
			. $content->filter('img')->eq(0)->attr('src');

	$dt = $content->filter('dt');
	$dt_list = array();
	nipout($dt, $dt_list);

	$dd = $content->filter('dd');
	$dd_list = array();
	nipout($dd, $dd_list);

	$list = array_combine($dt_list, $dd_list);

	$fields = array_merge($fields, $list);

//	print_r($fields);
	return $fields;
}
// }}}

// {{{ function nipout($element, &$fields)
function nipout($element, &$fields)
{
	if (count($element) > 0) {
		$element->each(function ($node) use (&$fields) {
			$text = trim($node->text());
			$fields[$text] = $text;
		});
	}
}
// }}}

// {{{ function pageList($client, $url, $content, &$pages)
function pageList($client, $url, $content, &$pages)
{
//	printf("%s\t%s\t%s\n", 'list', $url, '');
	$links = array();
	$content->filter('a')->each(function ($node) use (&$links) {
		$link = $node->attr('href');
		$link = preg_replace('/index\.html$/', '', $link);
		$links[] = $link;
	});
	foreach($links as $i => $link) {
		$goto = $url . $link;
		browse($client, $goto, $pages);
	}
}
// }}}

// {{{ function browse($client, $url, &$pages)
function browse($client, $url, &$pages)
{
	$crawler = $client->request('GET', $url);

	$content = $crawler->filter('div#content');
	$idnumber = $content->filter('span.idnumber');

	if (count($idnumber) === 0) {
		// 記事一覧
		pageList($client, $url, $content, $pages);
	} else {
		// 末端の記事
		$pages[] = pageArticle($url, $content);
	}
}
// }}}

// {{{ function makeCsvItem()
function makeCsvItem()
{
	$items = array(
		'施設名',
		'写真',
		'所在地',
		'電話',
		'構造',
		'敷地面積',
		'延べ面積',
		'竣工年月日',
	);

	return $items;
}
// }}}
