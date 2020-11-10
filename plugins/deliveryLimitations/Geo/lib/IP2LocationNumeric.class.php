<?php

require_once LIB_PATH . '/Extension/deliveryLimitations/DeliveryLimitations.php';

class Plugins_DeliveryLimitations_IP2Location_Numeric extends Plugins_DeliveryLimitations
{
	function __construct()
	{
		$this->aOperations = [
			'==' => $GLOBALS['strEqualTo'],
			'!=' => $GLOBALS['strDifferentFrom'],
			'lt' => $GLOBALS['strLessThan'],
			'le' => 'is less then or equal to',
			'gt' => $GLOBALS['strGreaterThan'],
			'ge' => 'is greater then or equal to',
		];
	}

	function isAllowed()
	{
		return true;
	}
}
