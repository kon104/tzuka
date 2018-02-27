<?php

class GenerateHtml
{

	// {{{ public static function jsJQuery()
	public static function jsJQuery()
	{
echo <<<EOT
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function cssJsTree()
	public static function cssJsTree()
	{
echo <<<EOT
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function jsJsTree()
	public static function jsJsTree()
	{
echo <<<EOT
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function cssBootStrap()
	public static function cssBootStrap()
	{
echo <<<EOT
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
  integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb"
  crossorigin="anonymous">
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function cssBootStrap3()
	public static function cssBootStrap3()
	{
echo <<<EOT
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function jsBootStrap()
	public static function jsBootStrap()
	{
echo <<<EOT
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"
  integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh"
  crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
  integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
  crossorigin="anonymous"></script>
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function jsBootStrap3()
	public static function jsBootStrap3()
	{
echo <<<EOT
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
EOT;
		echo "\n";
	}
	// }}}

	// {{{ public static function jsGoogleAnalytics()
	public static function jsGoogleAnalytics()
	{
echo <<<EOT
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-113471219-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-113471219-1');
</script>
EOT;
		echo "\n";
	}
	// }}}

	// {{{ private static function writeTreeItems($kmlurls, $kmlxmls, &$tier, &$increased, &$area_count)
	private static function writeTreeItems($kmlurls, $kmlxmls, &$tier, &$increased, &$area_count)
	{
		foreach($kmlurls as $key => $value) {
			if (gettype($value) === "array") {
				if ($increased !== true) {
					$area_count++;
					$increased = true;
				}
				$tier++;
				printf("\t<li id=\"kmlinner_%d_%d\" data-jstree='{\"opened\": true, \"selected\": false}'>%s\n", $area_count, $tier, $key);
				echo "\t\t\t<ul>\n";
				self::writeTreeItems($value, $kmlxmls, $tier, $increased, $area_count);
				echo "\t\t\t</ul>";
				echo "</li>\n";
				$tier--;
			} else {
				$increased = false;
				$serial = $area_count * 100 + $key + 1;
				$xml = $kmlxmls[$serial]['xml'];
				$name = "読込失敗(判定利用不可)";
				if ($xml !== null) {
					$name = $xml->Placemark->name;
				}
				printf("\t\t\t\t<li id=\"kml_%s\" data-jstree='{\"icon\": \"jstree-file\"}'>%s</li>\n", $serial, $name);
			}
		}
	}
	// }}}

	// {{{ public static function partCommunityTree($kmlurls, $kmlxmls)
	public static function partCommunityTree($kmlurls, $kmlxmls)
	{

		echo "<div id=\"kml_tree\">\n<ul>\n";
		$tier = 0;
		$increased = false;
		$area_count = 0;

		self::writeTreeItems($kmlurls, $kmlxmls, $tier, $increased, $area_count);

		echo "</ul>\n</div>\n";
		echo "\n";
	}
	// }}}

}
