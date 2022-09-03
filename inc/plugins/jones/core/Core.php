<?php

class JB_Core
{
	/**
	 * Our version!
	 *
	 * @var string
	 */
	private static $version = "1.0.5";

	/**
	 * @var JB_Core
	 */
	private static $instance = null;

	/**
	 * @return JB_Core
	 */
	public static function getInstance()
	{
		if(static::$instance === null)
			static::$instance = new self();

		return static::$instance;
	}

	/**
	 * Short for "getInstance"
	 *
	 * @return JB_Core
	 */
	public static function i()
	{
		return static::getInstance();
	}

	private function __construct()
	{
		// Define some path constants
		define('JB_PLUGINS', MYBB_ROOT."inc/plugins/");
		define('JB_PATH', JB_PLUGINS."jones/");
		define('JB_INCLUDES', JB_PATH."core/Includes/");

		// We need to require our packages manager manually as the autoloader needs it
		require_once JB_PATH."core/Packages.php";

		// Register our autoloader
		spl_autoload_register(array($this, 'loadClass'));

		// Include our short helper functions
		require_once JB_INCLUDES."helpers.php";

		// Initialize our MyAlerts bridge
		JB_Alerts::init();

		// Update function
		global $plugins;
		$plugins->add_hook("admin_config_plugins_begin", array($this, "doUpgrade"));
		// Version check
		$plugins->add_hook("admin_config_plugins_plugin_list", array($this, "checkVersion"));
		// Importing a new theme
		$plugins->add_hook("admin_style_themes_import_commit", array($this, "updateTheme"));
		// Building our WIO list
		$plugins->add_hook("fetch_wol_activity_end", array("JB_WIO_Handler", "buildArray"));
		$plugins->add_hook("build_friendly_wol_location_end", array("JB_WIO_Handler", "buildLink"));
	}

	/**
	 * @param array $info
	 * @param bool  $overwrite
	 *
	 * @return array
	 */
	public function getInfo(array $info, $overwrite=true)
	{
		global $cache;

		// Do we need an update?
		$jb_plugins = $cache->read('jb_plugins');
		$version = $jb_plugins[$info['codename']];
		$install = "{$info['codename']}_is_installed";
		if((!function_exists($install) || $install()) && (empty($version) || version_compare($version, $info['version'], "<")))
		{
			// This plugin needs an update!
			$info['description'] .= "<br /><b>".JB_Lang::get('update_plugin')."</b> <a href=\"index.php?module=config-plugins&action=jb_update&plugin={$info['codename']}\">".JB_Lang::get('run')."</a>";
		}

		// Deprecated warning for all plugins / core
		$info['description'] .= "<br /><br /><b>Note: This plugin is not maintained anymore and shouldn't be used!!</b>";

		if($overwrite === true)
		{
			// Insert some usefull information, eg overwrite author/website to make sure they're the same and display update notifications
			$generalInfo = array(
				"website"		=> "http://jonesblog.de/",
				"author"		=> "Jones",
				"authorsite"	=> "http://jonesblog.de/",
			);

			return array_merge($info, $generalInfo);
		}
		return $info;
	}

	/**
	 * @param string $codename
	 * @param int    $core_minimum
	 * @param int    $mybb_minimum
	 * @param string $php_minimum
	 */
	public function install($codename, $core_minimum = false, $mybb_minimum = false, $php_minimum = "5.3")
	{
		global $cache, $mybb;

		if($core_minimum !== false)
		{
			if(version_compare(static::$version, $core_minimum, "<"))
			{
				// Core is too old
				flash_message(JB_Lang::get("requirement_core"), 'error');
				admin_redirect('index.php?module=config-plugins');
			}
		}

		if($mybb_minimum !== false)
		{
			if($mybb->version_code < $mybb_minimum)
			{
				// MyBB is too old
				flash_message(JB_Lang::get("requirement_mybb"), 'error');
				admin_redirect('index.php?module=config-plugins');
			}
		}

		if(version_compare(PHP_VERSION, $php_minimum, "<"))
		{
			// PHP is too old
			flash_message(JB_Lang::get("requirement_php"), 'error');
			admin_redirect('index.php?module=config-plugins');
		}

		// Test what's needed and run it then
		if(JB_Installer_Templates::isNeeded($codename))
			JB_Installer_Templates::install($codename);

		if(JB_Installer_Stylesheets::isNeeded($codename))
			JB_Installer_Stylesheets::install($codename);

		if(JB_Installer_Settings::isNeeded($codename))
			JB_Installer_Settings::install($codename);

		if(JB_Installer_Tasks::isNeeded($codename))
			JB_Installer_Tasks::install($codename);

		if(JB_Installer_Database::isNeeded($codename))
			JB_Installer_Database::install($codename);

    	if(JB_Installer_Alerts::isNeeded($codename))
			JB_Installer_Alerts::install($codename);

		// Update our versions cache
		$info = $codename."_info";
		$info = $info();
		$jb_plugins = $cache->read('jb_plugins');
		$jb_plugins[$codename] = $info['version'];
		$cache->update('jb_plugins', $jb_plugins);
	}

	/**
	 * @param string $codename
	 */
	public function uninstall($codename)
	{
		global $cache;

		// Set our installer classes up and revert them
		if(JB_Installer_Templates::isNeeded($codename))
			JB_Installer_Templates::uninstall($codename);

		if(JB_Installer_Stylesheets::isNeeded($codename))
			JB_Installer_Stylesheets::uninstall($codename);

		if(JB_Installer_Settings::isNeeded($codename))
			JB_Installer_Settings::uninstall($codename);

		if(JB_Installer_Tasks::isNeeded($codename))
			JB_Installer_Tasks::uninstall($codename);

		if(JB_Installer_Database::isNeeded($codename))
			JB_Installer_Database::uninstall($codename);

		if(JB_Installer_Alerts::isNeeded($codename))
			JB_Installer_Alerts::uninstall($codename);

		// Unset our cache
		$jb_plugins = $cache->read('jb_plugins');
		unset($jb_plugins[$codename]);
		$cache->update('jb_plugins', $jb_plugins);
	}

	/**
	 * @param string $codename
	 */
	public function activate($codename)
	{
		if(JB_Activate_Templates::isNeeded($codename))
			JB_Activate_Templates::activate($codename);

		if(JB_Activate_Tasks::isNeeded($codename))
			JB_Activate_Tasks::activate($codename);

		if(JB_Activate_Alerts::isNeeded($codename))
			JB_Activate_Alerts::activate($codename);
	}

	/**
	 * @param string $codename
	 */
	public function deactivate($codename)
	{
		if(JB_Activate_Templates::isNeeded($codename))
			JB_Activate_Templates::deactivate($codename);

		if(JB_Activate_Tasks::isNeeded($codename))
			JB_Activate_Tasks::deactivate($codename);

		if(JB_Activate_Alerts::isNeeded($codename))
			JB_Activate_Alerts::deactivate($codename);
	}

	public function doUpgrade()
	{
		global $mybb, $cache;

		// Doesn't really belong here, but better than adding a new hook
		if($mybb->input['action'] == "jb_info")
		{
			$this->showInfo();
			return;
		}

		if($mybb->input['action'] != "jb_update")
			return;

		$codename = $mybb->get_input('plugin');

		if($codename == "core")
		{
			// We're updating the core; jb_update_core is defined in "jones/core/include.php"
			jb_update_core();

			flash_message(JB_Lang::get("updated_core"), 'success');
			admin_redirect('index.php?module=config-plugins');
		}

		$jb_plugins = $cache->read('jb_plugins');
		$from_version = $jb_plugins[$codename];

		$version_dir = JB_Packages::i()->getPath($codename)."version";

		if(is_dir($version_dir))
		{
			/** @var JB_Version_Manager $version_manager */
			$version_manager = JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_Version_Manager";
			$version_manager::run($from_version);
		}

		if(JB_Installer_Templates::isNeeded($codename))
			JB_Installer_Templates::update($codename);

		if(JB_Installer_Stylesheets::isNeeded($codename))
			JB_Installer_Stylesheets::update($codename);

		if(JB_Installer_Settings::isNeeded($codename))
			JB_Installer_Settings::update($codename);

		if(JB_Installer_Tasks::isNeeded($codename))
			JB_Installer_Tasks::update($codename);

		if(JB_Installer_Database::isNeeded($codename))
			JB_Installer_Database::update($codename);

		if(JB_Installer_Alerts::isNeeded($codename))
			JB_Installer_Alerts::update($codename);

		require_once JB_PLUGINS."{$codename}.php";
		$info = $codename."_info";
		$info = $info();
		$jb_plugins[$codename] = $info['version'];
		$cache->update('jb_plugins', $jb_plugins);

		flash_message(JB_Lang::get("updated_plugin"), 'success');
		admin_redirect('index.php?module=config-plugins');
	}

	private function showInfo()
	{
		global $cache, $page, $mybb;

		$jb_plugins = $cache->read('jb_plugins');
		$plugins = "";
		foreach($jb_plugins as $codename => $version)
		{
			$info = "{$codename}_info";
			$info = $info();
			$plugins .= "<li>{$info['name']} ({$info['author']}) {$version}</li>";
		}

		$dev = "";
		if(defined("USE_DEVELOPMENT") && USE_DEVELOPMENT === true)
			$dev = " (Development Version)";

		$page->output_header("Jones Core not installed");

		$table = new Table;
		$table->construct_header("Info");
		$table->construct_cell("JonesCore ".static::$version."{$dev} running on MyBB {$mybb->version} with PHP ".PHP_VERSION." and the following plugins:<br /><ul>{$plugins}</ul>");
		$table->construct_row();
		$table->output("Jones Core Info");

		$page->output_footer();
	}

	public function checkVersion()
	{
		// Disabled version check as not maintained anymore
		/*$l = "/version.php?type=core";
		if(defined("USE_DEVELOPMENT") && USE_DEVELOPMENT === true)
			$l .= "&dev=1";
		$version = $this->call($l);

		if(version_compare($version, static::$version, ">"))
		{
			// Update baby!
			echo "<div class=\"alert\">".JB_Lang::get("update_core")." <a href=\"index.php?module=config-plugins&action=jb_update&plugin=core\">".JB_Lang::get("run")."</a></div>";
		}*/
	}

	public function updateTheme()
	{
		global $theme_id, $cache, $mybb, $db;

		// No templates were imported - ignore this!
		if(!$mybb->input['import_templates'])
			return;

		// We need the template set id instead of the theme id
		$query = $db->simple_select("themes", "properties", "tid={$theme_id}");
		$props = unserialize($db->fetch_field($query, "properties"));
		$sid = $props['templateset'];

		$jb_plugins = $cache->read("jb_plugins");
		$active = $cache->read("plugins");
		$active = $active['active'];

		require_once MYBB_ROOT."inc/adminfunctions_templates.php";

		foreach(array_keys($jb_plugins) as $codename)
		{
			// Only activated plugins!
			if(!in_array($codename, $active))
				continue;

			// No template edits? Nice!
			if(!file_exists(JB_Packages::i()->getPath($codename)."install/template_edits.php"))
				continue;

			require_once JB_Packages::i()->getPath($codename)."install/template_edits.php";
	
			// Run them!
			if(!empty($edits))
			{	
				foreach($edits as $template => $edit)
				{
					foreach($edit as $find => $replace)
					{
						find_replace_templatesets($template, "#".preg_quote($find)."#i", $replace, 1, $sid);
					}
				}
			}
		}
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 */
	public function call($url)
	{
		// Disabled as not maintained anymore
		return "";
	}

	// Autoloader functions

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function loadClass($name = '')
	{
		$name = (string) $name;
		
		if ($file = $this->findFile($name)) {
			require_once $file;
			return true;
		}
		
		return false;
	}

	/**
	 * @param string $class
	 *
	 * @return bool|string
	 */
	public function findFile($class = '')
	{
		$classParts = explode('_', $class);
		$prefix = array_shift($classParts);
		$vendor = JB_Packages::i()->getVendorForPrefix($prefix);
		if($vendor === false)
			return false;

		$package = $classParts[0];
		$extra = "";
		if(strtolower($prefix) == "jb" && !is_dir(JB_PATH.strtolower($package)))
		{
			$package = "core";
		}
		else
		{
			$package = strtolower($package);

			array_shift($classParts);

			$el = count($classParts);
			$last = $classParts[$el-2];

			if(strtolower($last) != "version" && strtolower($last) != "wio")
				$extra = "classes/";
		}

		$className = array_pop($classParts);

		$path = JB_Packages::i()->getPath($package).$extra.implode("/", $classParts)."/".$className.".php";

		if(file_exists($path))
			return $path;

		return false;
	}
}
