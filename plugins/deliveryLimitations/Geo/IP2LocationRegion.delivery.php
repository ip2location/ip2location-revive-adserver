<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationRegion($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}
	if ($op != '=~' && $op != '!~') {
		// Provide backwards compatibility
		$op = '=~';
	}

	$aLimitation = explode('|', $limitation);
	$sCountry = $aLimitation[0];
	$sRegions = $aLimitation[1];

	if ($aParams && $aParams['ip_regionName'] && $aParams['ip_countryCode']) {
		return MAX_limitationsMatchStringValue($aParams['ip_countryCode'], $sCountry, '==')
			&& MAX_limitationsMatchArrayValue($aParams['ip_regionName'], $sRegions, $op);
	}

	return false; // Do not show the ad if user has no data about region and country.
}
