<?php

class JB_Installer_Templates extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/templates.php";

		if(!empty($templateset))
		{
			// Template Group
			$templategroup = array(
				"prefix"	=> $codename,
				"title"		=> $templateset,
			);
			$db->insert_query("templategroups", $templategroup);
		}
	
		// Templates
		if(!empty($templates))
		{
			foreach($templates as $template)
			{
				$template['sid'] = "-2"; // Master Theme
				$template['template'] = $db->escape_string($template['template']);
				$db->insert_query("templates", $template);
			}
		}
	}

	static function uninstall($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/templates.php";

		// Template Group
		$db->delete_query("templategroups", "prefix='{$codename}'");

		// Templates
		if(!empty($templates))
		{
			foreach($templates as $template)
			{
				$db->delete_query("templates", "title='{$template['title']}'");
			}
		}
	}

	static function isNeeded($codename)
	{
		return file_exists(JB_PATH."{$codename}/install/templates.php");
	}
}