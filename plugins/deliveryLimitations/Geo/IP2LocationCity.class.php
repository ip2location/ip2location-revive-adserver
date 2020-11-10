<?php


require_once __DIR__ . '/lib/IP2LocationCommaSeparatedData.class.php';

class Plugins_DeliveryLimitations_Geo_IP2LocationCity extends Plugins_DeliveryLimitations_IP2Location_CommaSeparatedData
{
	function __construct()
	{
		parent::__construct();
		$this->columnName = 'geo_IP2Location_city';
		$this->nameEnglish = 'IP2Location - City';
	}

	function init($data)
	{
		parent::init($data);
		if (is_array($this->data)) {
			$this->data = $this->_flattenData($this->data);
		}
	}

	function displayData()
	{
		$this->data = $this->_expandData($this->data);
		$tabindex = &$GLOBALS['tabindex'];

		// The region plugin is slightly different since we need to allow for multiple regions in different countries
		echo "
            <table border='0' cellpadding='2'>
                <tr>
                    <th>Country&nbsp;&nbsp;</th>
                    <td>
                        <select name='acl[{$this->executionorder}][data][]' {$disabled}>";
		foreach ($this->res as $countryCode => $countryName) {
			if (count($countryName) === 1) {
				continue;
			}
			$selected = ($this->data[0] == $countryCode) ? 'selected="selected"' : '';
			echo "<option value='{$countryCode}' {$selected}>{$countryName[0]}</option>";
		}
		echo "
                        </select>
                    &nbsp;<input type='image' name='action[none]' src='" . OX::assetPath() . "/images/{$GLOBALS['phpAds_TextDirection']}/go_blue.gif' border='0' align='absmiddle' alt='{$GLOBALS['strSave']}'></td>
                </tr>";

		if (!empty($this->data[0])) {
			// A country has been selected, show city list for this country...
			// Note: Since a disabled field does not pass it's value through, we need to pass the selected country in...
			echo '<tr><th>' . $this->translate('City(s)') . "</th><td><div class='box' style= 'width: 325px;'>";
			$aRegions = $this->res[$this->data[0]];
			unset($aRegions[0]);
			$aSelectedRegions = $this->data;
			unset($aSelectedRegions[0]);
			foreach ($aRegions as $sCode => $sName) {
				echo "<div class='boxrow'>";
				echo "<input tabindex='" . ($tabindex++) . "' ";
				echo "type='checkbox' id='c_{$this->executionorder}_{$sCode}' name='acl[{$this->executionorder}][data][]' value='{$sCode}'" . (in_array($sCode, $aSelectedRegions) ? ' CHECKED' : '') . ">{$sName}</div>";
			}
			echo '</div></td></tr>';
		}
		echo '
            </table>
        ';
		$this->data = $this->_flattenData($this->data);
	}

	function _flattenData($data = null)
	{
		if (null === $data) {
			$data = $this->data;
		}
		if (is_array($data)) {
			$country = $data[0];
			unset($data[0]);

			return $country . '|' . implode(',', $data);
		}

		return $data;
	}

	function _expandData($data = null)
	{
		if (null === $data) {
			$data = $this->data;
		}
		if (!is_array($data)) {
			$aData = strlen($data) ? explode('|', $data) : [];
			$aRegions = MAX_limitationsGetAFromS($aData[1]);

			return array_merge([$aData[0]], $aRegions);
		}

		return $data;
	}

	function compile()
	{
		return $this->compileData($this->_preCompile($this->data));
	}

	function _preCompile($sData)
	{
		$aData = $this->_expandData($sData);
		$aData = MAX_limitationsGetPreprocessedArray($aData);

		return $this->_flattenData($aData);
	}
}
