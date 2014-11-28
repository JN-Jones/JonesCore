<?php

class JB_Activate_Alerts extends JB_Activate_Base
{
	static function activate($codename)
	{
		global $db, $cache;

		require JB_PATH."{$codename}/install/alerts.php";

		{
			$manager = $GLOBALS['mybbstuff_myalerts_alert_type_manager'];
			if($manager == null)
				$manager = new MybbStuff_MyAlerts_AlertTypeManager($db, $cache);

			$updated = array();
			foreach($alerts as $alert)
			{
				$type = $manager->getByCode("JB_{$codename}_{$alert}");
				$type->setEnabled(true);
				$updated[] = $type;
			}
			$manager->updateAlertTypes($updated);
		}
	}

	static function deactivate($codename)
	{
		global $db, $cache;

		require JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			$manager = $GLOBALS['mybbstuff_myalerts_alert_type_manager'];
			if($manager == null)
				$manager = new MybbStuff_MyAlerts_AlertTypeManager($db, $cache);

			$updated = array();
			foreach($alerts as $alert)
			{
				$type = $manager->getByCode("JB_{$codename}_{$alert}");
				$type->setEnabled(false);
				$updated[] = $type;
			}
			$manager->updateAlertTypes($updated);
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