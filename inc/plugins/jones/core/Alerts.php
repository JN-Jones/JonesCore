<?php

class JB_Alerts
{
	private static $installed = null;
	private static $activated = null;
	private static $types = null;

	public static function init()
	{
		if(!static::isInstalled())
		{
			// Not installed so add our nice hook to add our alerts when installing
			global $plugins;
			$plugins->add_hook("myalerts_install", array("JB_Alerts", "onInstall"));
		}

		// Nothing to do if MyAlerts is deactivated
		if(!static::isActivated()
			return;

		// Loop through all types and register the correct formatter
		foreach(static::getTypes() as $codename => $types)
		{
			foreach($types as $type)
			{
				// TODO: Register formatter
			}
		}
	}

	public static function trigger($codename, $alert, $to)
	{
		$name = "jb_{$codename}_{$alert}";

		if(!is_array($to))
			$to = array($to);

		foreach($to as $id)
		{
			$alert = new MybbStuff_MyAlerts_Entity_Alert::make($to, $name);
			$GLOBALS['mybbstuff_myalerts_alert_manager']->addAlert($alert);
		}
	}

	public static getTypes()
	{
		if(static::$types !== null)
			return static::$types;

		global $cache;

		$jb_plugins = $cache->read("jb_plugins");
		$active = $cache->read("plugins");
		$active = $active['active'];

		foreach(array_keys($jb_plugins) as $codename)
		{
			// Only activated plugins!
			if(!in_array($codename, $active))
				continue;

			if(!file_exists(JB_PATH."{$codename}/install/alerts.php"))
				continue;

			require_once JB_PATH."{$codename}/install/alerts.php";

			if(!empty($alerts))
			{
				static::$types[$codename] = $alerts;
			}
		}
	}

	public static onInstall()
	{
		global $cache;

		$jb_plugins = $cache->read("jb_plugins");

		foreach(array_keys($jb_plugins) as $codename)
		{
			if(JB_Installer_Alerts::isNeeded($codename))
				JB_Installer_Alerts::install($codename);
		}
	}

	public static isInstalled()
	{
		if(static::$installed !== null)
			return static::$installed;

		// File not uploaded? -> Not installed
		if(!file_exists(MYBB_ROOT." inc/plugins/myalerts.php"))
		{
			static::$installed = false;
			return false;
		}

		require_once MYBB_ROOT."inc/plugins_myalerts.php";

		$func = "myalerts_is_installed";

		// Trying to fool us with a wrong file?
		if(!function_exists($func))
		{
			static::$installed = false;
			return false;
		}

		static::$installed = $func();
		return static::$installed;
	}

	public static function isActivated()
	{
		if(static::$activated !== null)
			return static::$activated;

		// Not installed? -> Not activated!
		if(!static::isInstalled())
		{
			static::$activated = false;
			return false;
		}

		global $cache;

		$plugins = $cache->read("plugins");
		$active = $plugins["active"];

		static::$activated = false;
		if(in_array("myalerts", $active))
			static::$activated = true;

		return static::$activated;
	}
}