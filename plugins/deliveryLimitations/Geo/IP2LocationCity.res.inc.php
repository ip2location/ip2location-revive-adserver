<?php
/*
 *
 *
	[AD] => Array
		(
			[0] => Andorra
			[02] => Canillo
			[03] => Encamp
			[04] => La Massana
			[05] => Ordino
			[06] => Sant Julia de Loria
			[07] => Andorra la Vella
			[08] => Escaldes-Engordany
		)

	[AO] => Array
		(
			[0] => Angola
			[01] => Benguela
			[02] => Bie
			[03] => Cabinda
			[04] => Cuando Cubango
			[05] => Cuanza Norte
			[06] => Cuanza Sul
			[07] => Cunene
			[08] => Huambo
			[09] => Huila
			[12] => Malanje
			[14] => Moxico
			[15] => Uige
			[16] => Zaire
			[17] => Lunda Norte
			[18] => Lunda Sul
			[19] => Bengo
			[20] => Luanda
		)
*/

if (!isset($GLOBALS['_MAX']['_GEOCACHE']['IP2Location_city'])) {
	$pathPlugins = __DIR__ . '/data/';
	$fp = fopen($pathPlugins . 'country-region-city.csv', 'r');

	$res1 = [];
	while ($row = fgetcsv($fp, 1024, ',')) {
		if ($row[0] != '-' && $row[3] != '-' && $row[0] != '' && $row[3] != '') {
			if (!isset($res1[$row[0]])) {
				$res1[$row[0]] = [ucfirst($row[1])];
			}
			$region = strtolower($row[3]);
			$res1[$row[0]][$region] = ucfirst($row[3]);
		}
	}

	foreach ($res1 as $key => $value) {
		asort($value);
		$res[$key] = $value;
	}

	uasort($res, function ($a, $b) {
		return strcmp($a[0], $b[0]);
	});

	$GLOBALS['_MAX']['_GEOCACHE']['IP2Location_city'] = $res;
} else {
	$res = $GLOBALS['_MAX']['_GEOCACHE']['IP2Location_city'];
}
