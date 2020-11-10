<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationUsageType($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_usageType'])) {
		return false;
	}

	return MAX_limitationsMatchArrayClientGeo('ip_usageType', $limitation, $op, $aParams);
}
