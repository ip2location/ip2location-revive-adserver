<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationDistrict($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_district'])) {
		return false;
	}

	return MAX_limitationsMatchStringClientGeo('ip_district', $limitation, $op, $aParams);
}
