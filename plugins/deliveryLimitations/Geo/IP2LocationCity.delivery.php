<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationCity($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if ($op != '=~' && $op != '!~') {
		// Provide backwards compatibility
		$op = '=~';
	}

	if (empty($aParams['ip_countryCode']) || (empty($aParams['ip_cityName']))) {
		return false;
	}

	$aLimitation = explode('|', $limitation);
	$sCountry = $aLimitation[0];
	$sCity = $aLimitation[1];

	if ($aParams && $aParams['ip_cityName'] && $aParams['ip_countryCode']) {
		return MAX_limitationsMatchStringValue($aParams['ip_countryCode'], $sCountry, '==')
			&& MAX_limitationsMatchArrayValue($aParams['ip_cityName'], $sCity, $op);
	}

	return false; // Do not show the ad if user has no data about region and country.
}
