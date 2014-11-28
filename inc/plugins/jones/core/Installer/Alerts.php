<?php

class JB_Installer_Alerts extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db, $cache;

		require JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			$manager = $GLOBALS['mybbstuff_myalerts_alert_type_manager'];
			if($manager == null)
				$manager = new MybbStuff_MyAlerts_AlertTypeManager($db, $cache);

			foreach($alerts as $alert)
			{
				// Don't enable it - will be done on activation
				$alertType = (new MybbStuff_MyAlerts_Entity_AlertType())->setCode("JB_{$codename}_{$alert}")->setEnabled(false);
				$manager->add($alertType);
			}
		}
	}

	static function update($codename)
	{
		// TODO!
	}

	static function uninstall($codename)
	{
		global $db, $cache;

		require JB_PATH."{$codename}/install/alerts.php";

		if(!empty($alerts))
		{
			$manager = $GLOBALS['mybbstuff_myalerts_alert_type_manager'];
			if($manager == null)
				$manager = new MybbStuff_MyAlerts_AlertTypeManager($db, $cache);

			foreach($alerts as $alert)
			{
				$manager->deleteByCode("JB_{$codename}_{$alert}");
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