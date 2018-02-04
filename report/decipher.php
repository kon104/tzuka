<?php

	$url = "https://open-data.pref.shizuoka.jp/mum2z1exw-151/?action=common_download_main&upload_id=1919";
	$data = file_get_contents($url);
	$data = explode("\r\n", $data);

	header('Content-Type: text/html;  charset=Shift_JIS');

?>
<head>
<link rel='stylesheet' href='./decipher.css' type='text/css'>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-113471219-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-113471219-1');
</script>
</head>
<?php
	echo "<body><table>\n";
	foreach($data as $row) {
		$items = str_getcsv($row);
		echo "<tr>";
		foreach($items as $item) {
			echo "<td>$item</td>";
		}
		printf("<td><a href='https://maps.google.com/maps?q=%s,%s'>map</a></td>", $items[6], $items[7]);
		echo "</tr>\n";
	}
	echo "</table>\n</body>";
