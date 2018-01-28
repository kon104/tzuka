<?php

	$url = "https://open-data.pref.shizuoka.jp/mum2z1exw-151/?action=common_download_main&upload_id=1919";
	$data = file_get_contents($url);
	$data = explode("\r\n", $data);

	header('Content-Type: text/html;  charset=Shift_JIS');

	echo "<head><link rel='stylesheet' href='./decipher.css' type='text/css'></head>\n";
	echo "<body><table>\n";
	foreach($data as $row) {
		$items = str_getcsv($row);
		echo "<tr>";
		foreach($items as $item) {
			echo "<td>$item</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n</body>";
