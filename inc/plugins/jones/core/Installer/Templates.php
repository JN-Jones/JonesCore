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
			$version = JB_Helpers::createFourDigitVersion(JB_Helpers::getVersion($codename));
			foreach($templates as $template)
			{
				$template['sid'] = "-2"; // Master Theme
				$template['title'] = $db->escape_string($template['title']);
				$template['template'] = $db->escape_string($template['template']);
				$template['version'] = (int)$version;
				$db->insert_query("templates", $template);
			}
		}
	}

	static function update($codename)
	{
		global $db;

		require_once JB_PATH."{$codename}/install/templates.php";

		// Reset the template group
		$db->delete_query("templategroups", "prefix='{$codename}'");
		if(!empty($templateset))
		{
			$templategroup = array(
				"prefix"	=> $codename,
				"title"		=> $templateset,
			);
			$db->insert_query("templategroups", $templategroup);
		}
	
		// Templates
		if(!empty($templates))
		{
			$version = JB_Helpers::createFourDigitVersion(JB_Helpers::getVersion($codename));
			foreach($templates as $template)
			{
				$query = $db->simple_select("templates", "tid", "sid='-2' AND title='".$db->escape_string($template['title'])."'");
				$template['sid'] = "-2"; // Master Theme
				$template['template'] = $db->escape_string($template['template']);
				$template['title'] = $db->escape_string($template['title']);
				$template['version'] = (int)$version;

				$oldtemp = $db->fetch_array($query);
				if($oldtemp['tid'])
				{
					$update_array = array(
						'template' => $template['template'],
						'version' => $version,
						'dateline' => TIME_NOW
					);
					$db->update_query("templates", $update_array, "title='".$db->escape_string($templatename)."' AND sid='-2'");
				}
				else
				{
					$db->insert_query("templates", $template);
				}
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
				$db->delete_query("templates", "title='".$template['title']."'");
			}
		}
	}

	static function isNeeded($codename)
	{
		return file_exists(JB_PATH."{$codename}/install/templates.php");
	}
}