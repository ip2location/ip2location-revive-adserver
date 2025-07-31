<?php

function Plugin_geoTargeting_IP2LocationGeotargetingAPI_IP2LocationGeotargetingAPI_Delivery_getGeoInfo($useCookie = true)
{
	$conf = $GLOBALS['_MAX']['CONF'];
	$viewerGeoCookieName = $conf['var']['viewerGeo'];

	if ($useCookie && isset($_COOKIE[$viewerGeoCookieName])) {
		$result = _unpackCookie($_COOKIE[$viewerGeoCookieName]);
		if ($result !== false) {
			return $result;
		}
	}

	try {
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$result = getGeo($ipAddress);

		if ($useCookie && !empty($result)) {
			$cookieValue = _packCookie($result);
			MAX_cookieAdd($viewerGeoCookieName, $cookieValue);
		}

		return $result;
	} catch (Exception $e) {
		OX_Delivery_logMessage('IP2LocationAPI - ' . $e->getMessage(), 4);
		return false;
	}
}

function getGeo($ipAddress)
{
	$conf = $GLOBALS['_MAX']['CONF'];
	$aUserDetails = [];

	if (isset($_SERVER['DEV_MODE']) && $_SERVER['DEV_MODE'] === 'true') {
		$ipAddress = '8.8.8.8'; // Use a test IP in development mode
	}

	if (!filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
		throw new Exception('Invalid or private IP address provided.');
	}

	// Try local IP2Location database first
	$dbLocation = $conf['IP2LocationGeotargetingAPI']['dblocation'] ?? null;
	if ($dbLocation && file_exists($dbLocation) && is_readable($dbLocation)) {
		require_once __DIR__ . '/lib/IP2Location.php';
		$loc = new \IP2Location\Database($dbLocation, \IP2Location\Database::FILE_IO);
		$records = $loc->lookup($ipAddress, \IP2Location\Database::ALL);

		if ($records && $records['countryCode'] !== '?') {
			$aUserDetails = [
				'ip_countryCode' => $records['countryCode'],
				'ip_regionName'  => $records['regionName'],
				'ip_cityName'    => $records['cityName'],
				'ip_isp'         => $records['isp'],
				'ip_domainName'  => $records['domainName'],
				'ip_zipCode'     => $records['zipCode'],
				'ip_timeZone'    => $records['timeZone'],
				'ip_areaCode'    => $records['areaCode'],
				'ip_elevation'   => $records['elevation'],
				'ip_usageType'   => $records['usageType'],
			];
		}
	}

	// If not found, try local IP2Proxy database
	$pxLocation = $conf['IP2LocationGeotargetingAPI']['pxlocation'] ?? null;
	if (empty($aUserDetails) && $pxLocation && file_exists($pxLocation) && is_readable($pxLocation)) {
		require_once __DIR__ . '/lib/IP2Proxy.php';
		$db = new \IP2Proxy\Database();
		if ($db->open($pxLocation, \IP2Proxy\Database::FILE_IO)) {
			$records = $db->getAll($ipAddress);
			if ($records && $records['countryCode'] !== '?') {
				$aUserDetails = [
					'ip_countryCode' => $records['countryCode'],
					'ip_regionName'  => $records['regionName'],
					'ip_cityName'    => $records['cityName'],
					'ip_isProxy'     => $records['isProxy'],
					'ip_proxyType'   => $records['proxyType'],
					'ip_isp'         => $records['isp'],
				];
			}
		}
	}

	// If still not found, try web APIs
	if (empty($aUserDetails)) {
		$apiKey = $conf['IP2LocationGeotargetingAPI']['key'] ?? null;
		$ioKey = $conf['IP2LocationGeotargetingAPI']['iokey'] ?? null;

		if ($apiKey) {
			$url = 'https://api.ip2location.com/v2/?key=' . $apiKey . '&ip=' . $ipAddress . '&format=json&package=WS3';
			$json = _fetchJsonFromApi($url);
			if ($json && !isset($json->errors)) {
				$aUserDetails = [
					'ip_countryCode' => $json->country_code ?? '',
					'ip_regionName'  => $json->region_name ?? '',
					'ip_cityName'    => $json->city_name ?? '',
					'ip_isp'         => $json->isp ?? '',
				];
			}
		} elseif ($ioKey) {
			$url = 'https://api.ip2location.io/?key=' . $ioKey . '&ip=' . $ipAddress . '&format=json';
			$json = _fetchJsonFromApi($url);
			if ($json && !isset($json->error)) {
				$aUserDetails = [
					'ip_countryCode' => $json->country_code ?? '',
					'ip_regionName'  => $json->region_name ?? '',
					'ip_cityName'    => $json->city_name ?? '',
					'ip_isp'         => $json->isp ?? '',
					'ip_domainName'  => $json->domain ?? '',
					'ip_zipCode'     => $json->zip_code ?? '',
					'ip_timeZone'    => $json->time_zone ?? '',
					'ip_areaCode'    => $json->area_code ?? '',
					'ip_elevation'   => $json->elevation ?? '',
					'ip_usageType'   => $json->usage_type ?? '',
				];
			}
		}
	}

	if (!empty($aUserDetails)) {
		return $aUserDetails;
	}

	throw new Exception('Unable to retrieve geolocation data for the IP address.');
}

function _unpackCookie($cookieString = '')
{
	$decodedData = base64_decode($cookieString);
	if ($decodedData === false) {
		return false;
	}

	$geoInfo = json_decode($decodedData, true);

	return (json_last_error() === JSON_ERROR_NONE) ? $geoInfo : false;
}

function _packCookie($data = [])
{
	return base64_encode(json_encode($data));
}

function _fetchJsonFromApi($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);

	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		curl_close($ch);
		return null;
	}

	curl_close($ch);
	$json = json_decode($response);

	return (json_last_error() === JSON_ERROR_NONE) ? $json : null;
}
