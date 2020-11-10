<?php

$className = 'postscript_install_IP2LocationGeotargetingAPI';

require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once LIB_PATH . '/Plugin/PluginManager.php';

class postscript_install_IP2LocationGeotargetingAPI
{
	function execute()
	{
		$oPluginManager = new OX_PluginManager();
		$oPluginManager->disablePackage('openXMaxMindGeoIP');
		$oPluginManager->disablePackage('openXMaxMindModGeoIP');

		$oSettings = new OA_Admin_Settings();
		$oSettings->settingChange('geotargeting', 'type', 'geoTargeting:IP2LocationGeotargetingAPI:IP2LocationGeotargetingAPI');
		$oSettings->settingChange('geotargeting', 'showUnavailable', false);

		return $oSettings->writeConfigChange();
	}
}
