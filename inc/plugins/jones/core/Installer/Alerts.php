<?php

class JB_Installer_Alerts extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db, $cache;

		require JB_Packages::i()->getPath($codename)."install/alerts.php";

		if(!empty($alerts))
		{
			// Calling createInstance to make sure we have an instance
			$manager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);

			foreach($alerts as $alert)
			{
				// Don't enable it - will be done on activation
				$alertType = new MybbStuff_MyAlerts_Entity_AlertType();
				$alertType = $alertType->setCode(JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_{$alert}")->setEnabled(false);
				$manager->add($alertType);
			}
		}
	}

	static function update($codename)
	{
		require JB_Packages::i()->getPath($codename)."install/alerts.php";

		if(!empty($alerts))
		{
			global $db, $cache;
			$pls = $cache->read("plugins");
			$activated = in_array($codename, $pls['active']);

			// Calling createInstance to make sure we have an instance
			$manager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);

			foreach($alerts as $alert)
			{
				$test = $manager->getByCode(JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_{$alert}");
				if($test === null)
				{
					$alertType = new MybbStuff_MyAlerts_Entity_AlertType();
					$alertType = $alertType->setCode(JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_{$alert}")->setEnabled($activated);
					$manager->add($alertType);
				}
			}
		}
	}

	static function uninstall($codename)
	{
		global $db, $cache;

		require JB_Packages::i()->getPath($codename)."install/alerts.php";

		if(!empty($alerts))
		{
			// Calling createInstance to make sure we have an instance
			$manager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);

			foreach($alerts as $alert)
			{
				$manager->deleteByCode(JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_{$alert}");
			}
		}
	}

	static function isNeeded($codename)
	{
		// Only needed when MyAlerts is installed
		if(!JB_Alerts::isInstalled())
			return false;

		return file_exists(JB_Packages::i()->getPath($codename)."install/alerts.php");
	}
}