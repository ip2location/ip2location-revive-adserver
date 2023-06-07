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

		echo "<td><textarea name='acl[{$this->executionorder}][data]' style='width: 100%' rows='5' tabindex='" . ($tabindex++) . "'>" . htmlspecialchars(isset($this->data) ? implode(',', $this->data) : '') . "</textarea><br>Enter multple ZIP codes and separate with commas (,).</td>";
		echo '</tr>';

		echo '</table>';
	}
}
