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
				'ip_category'    => $records['category'],
				'ip_district'    => $records['district'],
			];
		}
	}

	// Try IP2Proxy database independently to populate proxy fields.
	// Requires IP2Proxy.php v3.x — update via: composer require ip2location/ip2proxy-php ^3
	$pxLocation = $conf['IP2LocationGeotargetingAPI']['pxlocation'] ?? null;
	if ($pxLocation && file_exists($pxLocation) && is_readable($pxLocation)) {
		require_once __DIR__ . '/lib/IP2Proxy.php';
		try {
			$db = new \IP2Proxy\Database($pxLocation, \IP2Proxy\Database::FILE_IO);
			$records = $db->lookup($ipAddress, \IP2Proxy\Database::ALL);
			if ($records && isset($records['isProxy'])) {
				$aUserDetails['ip_isProxy']   = $records['isProxy'];
				$aUserDetails['ip_proxyType'] = $records['proxyType'] ?? '';
				// Fill geo fields from proxy DB only when BIN DB did not provide them
				if (empty($aUserDetails['ip_countryCode']) && ($records['countryCode'] ?? '?') !== '?') {
					$aUserDetails['ip_countryCode'] = $records['countryCode'];
					$aUserDetails['ip_regionName']  = $records['regionName'] ?? '';
					$aUserDetails['ip_cityName']     = $records['cityName'] ?? '';
					$aUserDetails['ip_isp']          = $records['isp'] ?? '';
				}
			}
		} catch (\Exception $e) {
			OX_Delivery_logMessage('IP2LocationAPI (proxy) - ' . $e->getMessage(), 4);
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
					'ip_category'    => $json->category ?? '',
					'ip_district'    => $json->district ?? '',
				];
			}
		}
	}

	if (!empty($aUserDetails)) {
		return $aUserDetails;
	}

	throw new Exception('Unable to retrieve geolocation data for the IP address.');
}

function _cookieHmac($payload)
{
	$conf = $GLOBALS['_MAX']['CONF'];
	$secret = $conf['database']['password'] ?? '';
	return hash_hmac('sha256', $payload, 'ip2loc.' . $secret);
}

function _unpackCookie($cookieString = '')
{
	$parts = explode('.', $cookieString, 2);
	if (count($parts) !== 2) {
		return false;
	}
	[$payload, $mac] = $parts;
	if (!hash_equals(_cookieHmac($payload), $mac)) {
		return false;
	}

	$decodedData = base64_decode($payload);
	if ($decodedData === false) {
		return false;
	}

	$geoInfo = json_decode($decodedData, true);

	return (json_last_error() === JSON_ERROR_NONE) ? $geoInfo : false;
}

function _packCookie($data = [])
{
	$payload = base64_encode(json_encode($data));
	return $payload . '.' . _cookieHmac($payload);
}

function _fetchJsonFromApi($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		curl_close($ch);
		return null;
	}

	curl_close($ch);
	$json = json_decode($response);

	return (json_last_error() === JSON_ERROR_NONE) ? $json : null;
}
