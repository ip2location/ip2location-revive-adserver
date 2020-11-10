<?php


require_once __DIR__ . '/lib/IP2LocationNumeric.delivery.php';

function MAX_checkGeo_IP2LocationElevation($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_elevation'])) {
		return false;
	}

	return IP2Location_limitationMatchNumeric('ip_elevation', $limitation, $op, $aParams);
}
