<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationAreaCode($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_areaCode'])) {
		return false;
	}

	return MAX_limitationsMatchStringClientGeo('ip_areaCode', $limitation, $op, $aParams);
}
