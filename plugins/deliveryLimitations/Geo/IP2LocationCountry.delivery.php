<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationCountry($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if ($op != '=~' && $op != '!~') {
		// Provide backwards compatibility
		$op = '=~';
	}

	if (empty($aParams['ip_countryCode'])) {
		return false;
	}

	//print_r($aParams);
	return MAX_limitationsMatchArrayClientGeo('ip_countryCode', $limitation, $op, $aParams);
}
