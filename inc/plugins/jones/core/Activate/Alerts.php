<?php

class JB_Activate_Alerts extends JB_Activate_Base
{
	/**
	 * {@inheritdoc}
	 */
	static function activate($codename)
	{
		global $db, $cache;

		require JB_Packages::i()->getPath($codename)."install/alerts.php";

		if(!empty($alerts))
		{
			// Calling createInstance to make sure we have an instance
			$manager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);

			$updated = array();
			foreach($alerts as $alert)
			{
				$type = $manager->getByCode(JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_{$alert}")->setEnabled(true);
				$updated[] = $type;
			}
			$manager->updateAlertTypes($updated);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function deactivate($codename)
	{
		global $db, $cache;

		require JB_Packages::i()->getPath($codename)."install/alerts.php";

		if(!empty($alerts))
		{
			// Calling createInstance to make sure we have an instance
			$manager = MybbStuff_MyAlerts_AlertTypeManager::createInstance($db, $cache);

			$updated = array();
			foreach($alerts as $alert)
			{
				$type = $manager->getByCode(JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_{$alert}")->setEnabled(false);
				$updated[] = $type;
			}
			$manager->updateAlertTypes($updated);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function isNeeded($codename)
	{
		// Only needed when MyAlerts is installed
		if(!JB_Alerts::isInstalled())
			return false;

		return file_exists(JB_Packages::i()->getPath($codename)."install/alerts.php");
	}
}
