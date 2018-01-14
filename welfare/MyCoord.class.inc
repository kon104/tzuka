<?php

require_once("./MyCurl.class.inc");

class MyCoord
{

	public static $center = array('lat' => 34.79960279193115, 'lng' => 135.35992562770844);

	// {{{ public static $kmlUrls = array()
	public static $kmlUrls = array(
		"伊孑志 A" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a1.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a2.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a3.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a4.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a5.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a6.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-a7.kml",
		),
		"伊孑志 B" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b1.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b2.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b3.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b4.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b5.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b6.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-b7.kml",
		),
		"伊孑志 C" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c1.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c2.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c3.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c4.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c5.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c6.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c7.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c8.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/isoshi-c9.kml",
		),
		"福井町" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/fukui-cho-north.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/fukui-cho-south.kml",
		),
		"亀井町" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/kamei-cho-north.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/kamei-cho-south.kml",
		),
		"第一売布ヶ丘" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g1.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g2.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g3.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g4.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g5.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g6.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g7.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-gaoka-1st-g8.kml",
		),
		"売布" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/mefu-north.kml",
		),
		"中山五月台" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/nakayama-satukidai.kml",
		),
		"小林三丁目" => array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/obayashi-3chome-8.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/obayashi-3chome-9.kml",
		),
	);
	// }}}

	public static function fetchKmlXmls($kmlUrls)
	{

		$area_count = 0;
		$urls = array();
		foreach($kmlUrls as $areas) {
			$area_count++;
			foreach($areas as $idx => $url) {
				$serialnum = $area_count * 100 + $idx;
				$urls[$serialnum] = $url;
			}
		}

		$inputs = array_chunk($urls, 2, true);
		$results = array();
		foreach($inputs as $chunk) {
			$responses = MyCurl::execMulti($chunk);
			$results = $results + $responses;
		}
//		$results = MyCurl::execMulti($kmlUrls);

		$kmlxmls = array();
		foreach($results as $serialnum => $response) {

			if ($response['body'] === false) {
				ob_start();
				var_dump($response);
				$dump = ob_get_contents();
				ob_end_clean();
				error_log($dump);

				$xml = null;
				$coordinates = null;
			} else {
				$xml = new SimpleXMLElement($response['body']);

				// divide to individual value for coordinates
				$value = $xml->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates;
				$coordinates = preg_split("/[\s]+/", trim($value));
				foreach($coordinates as $i => $line) {
					$items = explode(',', $line);
					$record = array(
						'lat' => $items[1],
						'lng' => $items[0],
						'alt' => (count($items) > 2) ? $items[2] : null
					);
					$coordinates[$i] = $record;
				}
			}

			// package to array
			$kmlxmls[$serialnum] = array(
				'url' => $response['url'],
				'xml' => $xml,
				'coordinates' => $coordinates
			);

		}
		return $kmlxmls;
	}

}

