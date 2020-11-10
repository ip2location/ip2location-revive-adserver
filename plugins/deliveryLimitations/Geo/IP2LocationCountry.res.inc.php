<?php

/*
 *
 *

Array
(
	[0] => country_code
	[1] => country_name
	[2] => region_name
	[3] => city_name
)

Array
(
	[0] => AU
	[1] => AUSTRALIA
	[2] => QUEENSLAND
	[3] => SOUTH BRISBANE
)

Array
(
	[0] => CN
	[1] => CHINA
	[2] => FUJIAN
	[3] => FUZHOU
)

*/

if (!isset($GLOBALS['_MAX']['_GEOCACHE']['IP2Location_country'])) {
	$pathPlugins = __DIR__ . '/data/';
	$fp = fopen($pathPlugins . 'country-region-city.csv', 'r');

	$res = [];

	while ($row = fgetcsv($fp, 1024, ',')) {
		if ($row[0] != '-' && $row[1] != '-' && $row[0] != '' && $row[1] != '') {
			if (!isset($res[$row[0]])) {
				$res[$row[0]] = ucwords($row[1]);
			}
		}
	}

	asort($res);
	fclose($fp);

	$GLOBALS['_MAX']['_GEOCACHE']['IP2Location_country'] = $res;
} else {
	$res = $GLOBALS['_MAX']['_GEOCACHE']['IP2Location_country'];
}
