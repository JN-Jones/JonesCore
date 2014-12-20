<?php

class JB_Installer_Templates extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db;

		require_once JB_Packages::i()->getPath($codename)."install/templates.php";

		if(!empty($templateset))
		{
			// Template Group
			$templategroup = array(
				"prefix"	=> $codename,
				"title"		=> $templateset,
			);
			$db->insert_query("templategroups", dbe($templategroup));
		}
	
		// Templates
		if(!empty($templates))
		{
			$version = JB_Helpers::createFourDigitVersion(JB_Helpers::getVersion($codename));
			foreach($templates as $template)
			{
				$template['sid'] = "-2"; // Master Theme
				$template['version'] = $version;
				$db->insert_query("templates", dbe($template));
			}
		}
	}

	static function update($codename)
	{
		global $db;

		require_once JB_Packages::i()->getPath($codename)."install/templates.php";

		// Reset the template group
		$db->delete_query("templategroups", "prefix='".dbe($codename)."'");
		if(!empty($templateset))
		{
			$templategroup = array(
				"prefix"	=> $codename,
				"title"		=> $templateset,
			);
			$db->insert_query("templategroups", dbe($templategroup));
		}
	
		// Templates
		if(!empty($templates))
		{
			$version = JB_Helpers::createFourDigitVersion(JB_Helpers::getVersion($codename));
			foreach($templates as $template)
			{
				$query = $db->simple_select("templates", "tid", "sid='-2' AND title='".dbe($template['title'])."'");
				$template['sid'] = "-2"; // Master Theme
				$template['version'] = $version;

				$oldtemp = $db->fetch_array($query);
				if($oldtemp['tid'])
				{
					$update_array = array(
						'template' => $template['template'],
						'version' => $version,
						'dateline' => TIME_NOW
					);
					$db->update_query("templates", dbe($update_array), "title='".dbe($templatename)."' AND sid='-2'");
				}
				else
				{
					$db->insert_query("templates", dbe($template));
				}
			}
		}
	}

	static function uninstall($codename)
	{
		global $db;

		require_once JB_Packages::i()->getPath($codename)."install/templates.php";

		// Template Group
		$db->delete_query("templategroups", "prefix='".dbe($codename)."'");

		// Templates
		if(!empty($templates))
		{
			foreach($templates as $template)
			{
				$db->delete_query("templates", "title='".dbe($template['title'])."'");
			}
		}
	}

	static function isNeeded($codename)
	{
		return file_exists(JB_Packages::i()->getPath($codename)."install/templates.php");
	}
}