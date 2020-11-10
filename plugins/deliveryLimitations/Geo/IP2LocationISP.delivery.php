<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationISP($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_isp'])) {
		return false;
	}

	return MAX_limitationsMatchStringClientGeo('ip_isp', $limitation, $op, $aParams);
}
