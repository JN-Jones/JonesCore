<?php

class JB_Activate_Tasks extends JB_Activate_Base
{
	static function activate($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			$updated = array();
			foreach($alerts as $alert)
			{
				$type = $GLOBALS['mybbstuff_myalerts_alert_type_manager']->getByCode("JB_{$codename}_{$alert}");
				$type->setEnabled(true);
				$updated[] = $type;
			}
			$GLOBALS['mybbstuff_myalerts_alert_type_manager']->updateAlertTypes($updated);
		}
	}

	static function deactivate($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			$updated = array();
			foreach($alerts as $alert)
			{
				$type = $GLOBALS['mybbstuff_myalerts_alert_type_manager']->getByCode("JB_{$codename}_{$alert}");
				$type->setEnabled(false);
				$updated[] = $type;
			}
			$GLOBALS['mybbstuff_myalerts_alert_type_manager']->updateAlertTypes($updated);
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