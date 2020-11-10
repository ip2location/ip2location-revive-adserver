<?php


require_once __DIR__ . '/lib/IP2LocationNumeric.class.php';

class Plugins_DeliveryLimitations_Geo_IP2LocationElevation extends Plugins_DeliveryLimitations_IP2Location_Numeric
{
	function __construct()
	{
		parent::__construct();
		$this->columnName = 'geo_IP2Location_elevation';
		$this->nameEnglish = 'IP2Location - Elevation&nbsp;&nbsp;';
	}

	function displayData()
	{
		global $tabindex;
		echo "<input type='text' size='40' name='acl[{$this->executionorder}][data]' value=\"" . htmlspecialchars(isset($this->data) ? $this->data : '') . "\" tabindex='" . ($tabindex++) . "'> meter(s)";
	}
}
