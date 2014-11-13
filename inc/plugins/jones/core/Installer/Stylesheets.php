<?php

class JB_Installer_Stylesheets extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db, $mybb;

		require_once JB_PATH."{$codename}/install/stylesheets.php";

		// Settings
		if(!empty($stylesheets))
		{
			require_once MYBB_ROOT.$mybb->config['admin_dir']."/inc/functions_themes.php";

			$dstyle = array(
				"name"			=> "{$codename}.css",
				"tid"			=> 1, // Master Theme
				"attachedto"	=> "",
				"lastmodified"	=> TIME_NOW,
			);

			foreach($stylesheets as $stylesheet)
			{
				if(!is_array($stylesheet))
				    $stylesheet['stylesheet'] = $stylesheet;

				// Needed to cache the stylesheet
				$orig = $stylesheet['stylesheet'];
				$stylesheet['stylesheet'] = $db->escape_string($orig);

				$stylesheet = array_merge($stylesheet, $dstyle);

				if(empty($stylesheet['cachefile']))
				    $stylesheet['cachefile'] = $stylesheet['name'];

				$db->insert_query("themestylesheets", $stylesheetarray);

				// Cache stylesheet
				cache_stylesheet($stylesheet['tid'], $stylesheet['cachefile'], $orig);
			}
			// Rebuild lists
			update_theme_stylesheet_list(1, false, true);
		}
	}

	static function uninstall($codename)
	{
		global $db, $mybb;

		require_once JB_PATH."{$codename}/install/stylesheets.php";

		// Settings
		if(!empty($stylesheets))
		{
			require_once MYBB_ROOT.$mybb->config['admin_dir']."/inc/functions_themes.php";

			$names = array();

			foreach($stylesheets as $stylesheet)
			{
				if(!is_array($stylesheet))
				    $names[] = $db->escape_string("{$codename}.css");
				else
					$names[] = $db->escape_string($stylesheet['name']);
			}

			$names = array_unique($names);

			// Try to delete the cache files
			$query = $db->simple_select("themestylesheets", "tid,name", "name IN ('".implode("','", $names)."')");
			while($stylesheet = $db->fetch_array($query))
			{
				@unlink(MYBB_ROOT."cache/themes/{$stylesheet['tid']}_{$stylesheet['name']}");
				@unlink(MYBB_ROOT."cache/themes/theme{$stylesheet['tid']}/{$stylesheet['name']}");
			}

			// Now delete the originals
			$db->delete_query("themestylesheets", "name IN ('".implode("','", $names)."')");

			// Rebuild lists
			update_theme_stylesheet_list(1, false, true);
		}
	}

	static function isNeeded($codename)
	{
		return file_exists(JB_PATH."{$codename}/install/stylesheets.php");
	}
}