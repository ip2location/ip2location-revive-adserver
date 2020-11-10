<?php

function Plugin_geoTargeting_IP2LocationGeotargetingAPI_IP2LocationGeotargetingAPI_Delivery_getGeoInfo($useCookie = true)
{
	$conf = $GLOBALS['_MAX']['CONF'];

	//echo isset($_COOKIE[$conf['var']['viewerGeo']]);
	// Try and read the data from the geo cookie...

	if ($useCookie && isset($_COOKIE[$conf['var']['viewerGeo']])) {
		$result = _unpackCookie($_COOKIE[$conf['var']['viewerGeo']]);

		if ($result !== false) {
			return $result;
		}
	}
	if (isset($GLOBALS['_MAX']['GEO_IP'])) {
		$ip = $GLOBALS['_MAX']['GEO_IP'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	//$ip = '117.218.90.209';

	try {
		$result = getGeo($ip);

		// Store this information in the cookie for later use
		if ($useCookie && (!empty($result))) {
			MAX_cookieAdd($conf['var']['viewerGeo'], _packCookie($result));
		}
	} catch (Exception $e) {
		OX_Delivery_logMessage('IP2LocationAPI - ' . $e->getMessage(), 4);

		$result = false;
	}

	return $result;
}

function getGeo($ipAddress)
{
	$conf = $GLOBALS['_MAX']['CONF'];
	$aUserDetails = [];

	if (!isset($conf['IP2LocationGeotargetingAPI']['dblocation'])) {
		return false;
	}

	if (file_exists($conf['IP2LocationGeotargetingAPI']['dblocation']) && @fopen($conf['IP2LocationGeotargetingAPI']['dblocation'], 'rb')) {
		require_once __DIR__ . '/lib/IP2Location.php';

		$loc = new \IP2Location\Database($conf['IP2LocationGeotargetingAPI']['dblocation'], \IP2Location\Database::FILE_IO);
		$record = $loc->lookup($ipAddress, \IP2Location\Database::ALL);

		$aUserDetails = [
			'ip_countryCode' => $record['countryCode'],
			'ip_regionName'  => $record['regionName'],
			'ip_cityName'    => $record['cityName'],
			'ip_isp'         => $record['isp'],
			'ip_domainName'  => $record['domainName'],
			'ip_zipCode'     => $record['zipCode'],
			'ip_timeZone'    => $record['timeZone'],
			'ip_areaCode'    => $record['areaCode'],
			'ip_elevation'   => $record['elevation'],
			'ip_usageType'   => $record['usageType'],
		];
	}

	if (file_exists($conf['IP2LocationGeotargetingAPI']['pxlocation']) && @fopen($conf['IP2LocationGeotargetingAPI']['pxlocation'], 'rb')) {
		require_once __DIR__ . '/lib/IP2Proxy.php';

		$db = new \IP2Proxy\Database();
		$db->open($conf['IP2LocationGeotargetingAPI']['pxlocation'], \IP2Proxy\Database::FILE_IO);
		$records = $db->getAll($ipAddress);

		$aUserDetails = [
			'ip_countryCode' => $records['countryCode'],
			'ip_regionName'  => $records['regionName'],
			'ip_cityName'    => $records['cityName'],
			'ip_isProxy'     => $records['isProxy'],
			'ip_proxyType'   => $records['proxyType'],
			'ip_isp'         => $records['isp'],
		];
	}

	if (!$aUserDetails && $conf['IP2LocationGeotargetingAPI']['key']) {
		if (($json = json_decode(file_get_contents('https://api.ip2location.com/v2/?key=' . $conf['IP2LocationGeotargetingAPI']['key'] . '&ip=' . $ipAddress . '&format=json&package=WS3'))) !== null) {
			$aUserDetails = [
				'ip_countryCode' => $json->country_code,
				'ip_regionName'  => $json->region_name,
				'ip_cityName'    => $json->city_name,
				'ip_isp'         => $json->isp,
			];
		}
	}

	if ($aUserDetails) {
		return $aUserDetails;
	}

	throw new Exception('HTTP Error ' . $headers['http_code']);
}

function _unpackCookie($string = '')
{
	$aGeoInfo = [
		'ip_countryCode' => '',
		'ip_regionName'  => '',
		'ip_cityName'    => '',
		'ip_isp'         => '',
		'ip_domainName'  => '',
		'ip_zipCode'     => '',
		'ip_timeZone'    => '',
		'ip_areaCode'    => '',
		'ip_elevation'   => '',
		'ip_usageType'   => '',
		'ip_isProxy'     => '',
		'ip_proxyType'   => '',
	];

	$aPieces = explode('|', $string);
	if (count($aPieces) == count($aGeoInfo)) {
		$i = 0;
		foreach (array_keys($aGeoInfo) as $key) {
			if (!empty($aPieces[$i])) {
				$aGeoInfo[$key] = $aPieces[$i];
			} else {
				unset($aGeoInfo[$key]);
			}
			++$i;
		}
	} else {
		return false;
	}

	return empty($aGeoInfo) ? false : $aGeoInfo;
}

function _packCookie($data = [])
{
	$aGeoInfo = [
		'ip_countryCode' => '',
		'ip_regionName'  => '',
		'ip_cityName'    => '',
		'ip_isp'         => '',
		'ip_domainName'  => '',
		'ip_zipCode'     => '',
		'ip_timeZone'    => '',
		'ip_areaCode'    => '',
		'ip_elevation'   => '',
		'ip_usageType'   => '',
		'ip_isProxy'     => '',
		'ip_proxyType'   => '',
	];

	return implode('|', array_merge($aGeoInfo, $data));
}
