<?php


require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationDomain($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if ($op != '=~' && $op != '!~') {
		// Provide backwards compatibility
		$op = '=~';
	}

	return MAX_limitationsMatchStringClientGeo('ip_domainName', $limitation, $op, $aParams);
}
