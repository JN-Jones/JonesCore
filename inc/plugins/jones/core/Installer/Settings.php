<?php

class JB_Installer_Settings extends JB_Installer_Base
{
	/**
	 * {@inheritdoc}
	 */
	static function install($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/settings.php";

		// Settings Group
		if(!empty($settingsgroup))
		{
			$group = array(
				"title" 		=> $settingsgroup['title'],
				"description"	=> $settingsgroup['description'],
				"name"			=> $codename,
				"isdefault"		=> "0",
			);
			$gid = $db->insert_query("settinggroups", dbe($group));
		}

		// We need a gid to insert settings
		if(!isset($gid))
		{
			if(!function_exists("{$codename}_get_gid"))
				die("Wasn't able to determine the gid");

			$func = "{$codename}_get_gid";
			$gid = $func();
		}

		// Settings
		if(!empty($settings))
		{
			foreach($settings as $disporder => $setting)
			{
				$setting['disporder'] = $disporder;
				$setting['gid'] = $gid;
				$db->insert_query("settings", dbe($setting));
			}
		}

		// Don't forget to rebuild the settings!
		rebuild_settings();
	}

	/**
	 * {@inheritdoc}
	 */
	static function update($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/settings.php";

		// Settings Group
		$query = $db->simple_select("settinggroups", "gid", "name='".dbe($codename)."'");
		if(!empty($settingsgroup) && $db->num_rows($query) == 0)
		{
			$group = array(
				"title" 		=> $settingsgroup['title'],
				"description"	=> $settingsgroup['description'],
				"name"			=> $codename,
				"isdefault"		=> "0",
			);
			$gid = $db->insert_query("settinggroups", dbe($group));
		}
		else if($db->num_rows($query) == 1)
		{
			// Probably needed later
			$gid = $db->fetch_field($query, "gid");
		}

		// We need a gid to insert settings
		if(!isset($gid))
		{
			if(!function_exists("{$codename}_get_gid"))
				die("Wasn't able to determine the gid");

			$func = "{$codename}_get_gid";
			$gid = $func();
		}

		// Settings
		if(!empty($settings))
		{
			foreach($settings as $disporder => $setting)
			{
				$query = $db->simple_select("settings", "name", "name='".dbe($setting['name'])."'");
				if($db->num_rows($query) == 0)
				{
					$setting['disporder'] = $disporder;
					$setting['gid'] = $gid;
					$db->insert_query("settings", dbe($setting));
				}
			}
		}

		// Rebuild the settings just in case we changed something somewhere
		rebuild_settings();
	}

	/**
	 * {@inheritdoc}
	 */
	static function uninstall($codename)
	{
		global $db;

		// Try fetchnig the gid
		$query = $db->simple_select("settinggroups", "gid", "name='".dbe($codename)."'");
		if($db->num_rows($query) > 0)
		{
			// Lucky us, everything is in this group, delete it!
			$gid = $db->fetch_field($query, "gid");
			$db->delete_query("settinggroups", "gid='{$gid}'");
			$db->delete_query("settings", "gid='{$gid}'");
		}
		else
		{
			// We didn't have luck - need to loop through every setting and delete them
			require JB_Packages::i()->getPath($codename)."install/settings.php";
			if(!empty($settings))
			{
				foreach($settings as $setting)
				{
					$db->delete_query("settings", "name='".dbe($setting['name'])."'");
				}
			}
		}

		// We need to rebuild the settings again!
		rebuild_settings();
	}

	/**
	 * {@inheritdoc}
	 */
	static function isNeeded($codename)
	{
		return file_exists(JB_Packages::i()->getPath($codename)."install/settings.php");
	}
}
