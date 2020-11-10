<?php

require_once __DIR__ . '/IP2LocationGeotargetingAPI.delivery.php';

class Plugins_Geotargeting_IP2LocationGeotargetingAPI_IP2LocationGeotargetingAPI extends OX_Component
{
	function getName()
	{
		return 'IP2Location API';
	}

	function getGeoInfo()
	{
		return Plugin_geoTargeting_IP2LocationGeotargetingAPI_IP2LocationGeotargetingAPI_Delivery_getGeoInfo(false);
	}
}
