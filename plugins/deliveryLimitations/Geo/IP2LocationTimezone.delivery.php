<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationTimeZone($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_timeZone'])) {
		return false;
	}

	return MAX_limitationsMatchStringClientGeo('ip_timeZone', $limitation, $op, $aParams);
}
