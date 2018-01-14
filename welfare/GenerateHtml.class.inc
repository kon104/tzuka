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

	// {{{ public static function partCommunityTree($kmlurls, $kmlxmls)
	public static function partCommunityTree($kmlurls, $kmlxmls)
	{
		$area_count = 0;

		echo "<div id=\"kml_tree\">\n<ul>\n";

		foreach($kmlurls as $name => $areas) {
			$area_count++;
			printf("<li id=\"kmlroot_%d\" data-jstree='{\"opened\": true, \"selected\": false}'>%s\n", $area_count, $name);
			echo "\n\t\t\t<ul>\n";
			foreach($areas as $index => $url) {
				$serialnum = $area_count * 100 + $index;
				printf("\t\t\t\t<li id=\"kml_%s\" data-jstree='{\"icon\": \"jstree-file\"}'>%s</li>\n", $serialnum, $kmlxmls[$serialnum]['xml']->Placemark->name);
			}
			echo "\t\t\t</ul>";
			echo "</li>\n";
		}

		echo "</ul>\n</div>\n";
		echo "\n";
	}
	// }}}

}
