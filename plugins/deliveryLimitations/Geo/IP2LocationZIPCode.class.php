<?php


require_once __DIR__ . '/lib/IP2LocationCommaSeparatedData.class.php';

class Plugins_DeliveryLimitations_Geo_IP2LocationZIPCode extends Plugins_DeliveryLimitations_IP2Location_CommaSeparatedData
{
	function __construct()
	{
		parent::__construct();
		$this->columnName = 'geo_IP2Location_zipcode';
		$this->nameEnglish = 'IP2Location - ZIP Code';
	}

	function displayArrayData()
	{
		$tabindex = &$GLOBALS['tabindex'];

		echo "<table width='300' cellpadding='0' cellspacing='0' border='0'>";
		echo '<tr>';

		echo "<td><input type='text' size='40' name='acl[{$this->executionorder}][data]' value=\"" . htmlspecialchars(isset($this->data) ? $this->data['0'] : '') . "\" tabindex='" . ($tabindex++) . "'></td>";
		echo '</tr>';

		echo '</table>';
	}
}
