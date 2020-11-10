<?php


require_once __DIR__ . '/lib/IP2LocationCommaSeparatedData.class.php';

class Plugins_DeliveryLimitations_Geo_IP2LocationTimezone extends Plugins_DeliveryLimitations_IP2Location_CommaSeparatedData
{
	function __construct()
	{
		parent::__construct();
		$this->columnName = 'geo_IP2Location_time_zone';
		$this->nameEnglish = 'IP2Location - TimeZone&nbsp;&nbsp;';
	}

	function displayArrayData()
	{
		$tabindex = &$GLOBALS['tabindex'];
		echo "<div class='box' style='width: 385px'>";
		foreach ($this->res as $code => $name) {
			$name = htmlspecialchars($name);
			echo "<div class='boxrow'>";
			echo "<input tabindex='" . ($tabindex++) . "' ";
			echo "type='checkbox' id='c_{$this->executionorder}_{$code}' name='acl[{$this->executionorder}][data][]' value='{$code}'" . (in_array($code, $this->data) ? ' CHECKED' : '') . ">{$name}</div>";
		}
		echo '</div>';
	}
}
