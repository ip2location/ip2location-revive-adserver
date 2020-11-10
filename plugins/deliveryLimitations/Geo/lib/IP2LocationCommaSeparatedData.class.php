<?php

require_once LIB_PATH . '/Extension/deliveryLimitations/DeliveryLimitationsCommaSeparatedData.php';

class Plugins_DeliveryLimitations_IP2Location_CommaSeparatedData extends Plugins_DeliveryLimitations_CommaSeparatedData
{
	function isAllowed()
	{
		return true;
	}
}
