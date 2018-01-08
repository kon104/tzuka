<?php

require_once("./MyCurl.class.inc");

class MyCoord
{

	public static $center = array('lat' => 34.81123119501047, 'lng' => 135.35563945770264);

	private static $kmlUrls = array(
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-1st.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-2nd.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-3rd.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/hananomichi-4th.kml",
		"https://raw.githubusercontent.com/kon104/tzuka/master/kml-sample-data/yoneya.kml"
	);

	public static function fetchKmlXmls()
	{
		$results = MyCurl::execMulti(self::$kmlUrls);

		$kmlxmls = array();
		foreach($results as $response) {

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

			// package to array
			$kmlxmls[] = array(
				'url' => $response['url'],
				'xml' => $xml,
				'coordinates' => $coordinates
			);
		}
		return $kmlxmls;
	}


}

