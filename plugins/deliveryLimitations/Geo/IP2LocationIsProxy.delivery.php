<?php

require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function MAX_checkGeo_IP2LocationIsProxy($limitation, $op, $aParams = [])
{
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX']['CLIENT_GEO'];
	}

	if (empty($aParams['ip_isProxy'])) {
		return false;
	}

	return MAX_limitationsMatchArrayClientGeo('ip_isProxy', $limitation, $op, $aParams);
}
