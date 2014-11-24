<?php

class JB_Installer_Alerts extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			foreach($alerts as $alert)
			{
				// Don't enable it - will be done on activation
				$alertType = new MybbStuff_MyAlerts_Entity_AlertType()->setCode("JB_{$codename}_{$alert}")->setEnabled(false);
				$GLOBALS['mybbstuff_myalerts_alert_type_manager']->registerAlertType($alertType);
			}
		}
	}

	static function uninstall($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			foreach($alerts as $alert)
			{
				$GLOBALS['mybbstuff_myalerts_alert_type_manager']->removeAlertTypeByCode("JB_{$codename}_{$alert}");
			}
		}
	}

	static function isNeeded($codename)
	{
		// Only needed when MyAlerts is installed
		if(!JB_Alerts::isInstalled())
			return false;

		return file_exists(JB_PATH."{$codename}/install/alerts.php");
	}
}