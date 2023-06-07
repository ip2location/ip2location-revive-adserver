<?php


if (!isset($GLOBALS['_MAX']['_GEOCACHE']['IP2Location_region'])) {
	$pathPlugins = __DIR__ . '/data/';
	$fp = fopen($pathPlugins . 'country-region-city.csv', 'r');

	$res1 = [];
	while ($row = fgetcsv($fp, 1024, ',')) {
		if ($row[0] != '-' && $row[2] != '-' && $row[0] != '' && $row[2] != '') {
			if (!isset($res1[$row[0]])) {
				$res1[$row[0]] = [ucfirst($row[1])];
			}
			$region = strtolower($row[2]);
			$res1[$row[0]][$region] = ucfirst($row[2]);
		}
	}

	foreach ($res1 as $key => $value) {
		asort($value);
		$res[$key] = $value;
	}

	uasort($res, function ($a, $b) {
		return strcmp($a[0], $b[0]);
	});

	$GLOBALS['_MAX']['_GEOCACHE']['IP2Location_region'] = $res;
} else {
	$res = $GLOBALS['_MAX']['_GEOCACHE']['IP2Location_region'];
}
