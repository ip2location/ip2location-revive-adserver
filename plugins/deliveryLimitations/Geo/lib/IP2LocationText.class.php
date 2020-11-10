<?php

require_once LIB_PATH . '/Extension/deliveryLimitations/DeliveryLimitations.php';

class Plugins_DeliveryLimitations_IP2Location_Text extends Plugins_DeliveryLimitations
{
	function __construct()
	{
		parent::__construct();
	}

	function isAllowed()
	{
		return true;
	}
}
