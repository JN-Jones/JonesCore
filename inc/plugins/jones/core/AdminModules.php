<?php

class JB_AdminModules
{
	/**
	 * @var array
	 */
	private static $menu_cache = array();

	/**
	 * @param string $codename
	 * @param string $module
	 * @param string $action
	 * @param string $method
	 */
	public static function loadModule($codename=false, $module=false, $action=false, $method="")
	{
		global $mybb, $page, $lang, $errors;

		if($codename === false)
			$codename = $page->active_action;

		if($module === false)
			$module = $page->active_module;

		if($action === false)
			$action = $mybb->get_input('action');

		// Empty is index
		if(empty($action))
			$action = "index";

		$path = JB_Packages::i()->getPath($codename)."modules/admin/{$module}/";

		// Unknown module - blank page
		if(!file_exists($path."{$action}.php"))
			return;

		if($method != "get" && $method != "post")
			$method = $mybb->request_method;

		// Require our nice module classes
		require_once $path."{$action}.php";

		// And activate them
		$classname = "Module_".ucfirst($action);
		$mc = new $classname();

		if(!($mc instanceof JB_Module_Base))
			die("Module {$classname} is not a subclass of \"JB_Module_Base\"");

		// Let's figure out what to do
		// Something we need to do for post and get?
		$mc->start();

		// If we have a post method and we're posting -> run it
		if($method == "post" && $mc->post)
			$mc->post();
		// Either we don't have a post method or we're not posting
		else
			$mc->get();

		// Do we need to cleanup something?
		$mc->finish();
	}

	/**
	 * @param string $module
	 * @param string $id
	 * @param string $file
	 * @param bool   $permissions
	 * @param bool   $myplugins
	 * @param string $active
	 * @param bool   $withMenu
	 */
	public static function addModule($module, $id, $file, $permissions=true, $myplugins=true, $active=false, $withMenu=true)
	{
		global $plugins, $config;

		if(!defined("IN_ADMINCP"))
			return;

		if($active === false)
			$active = $id;

		static::$menu_cache[$module][$id] = array(
			"file"		=> $file,
			"active"	=> $active
		);

		if($myplugins === true)
		{
			global $cache;
			if(!isset($pluginlist))
				$pluginlist = $cache->read("plugins");
			if(!is_array($pluginlist['active']) || !in_array("myplugins", $pluginlist['active']))
				$myplugins = false;
		}

		if($myplugins === true)
		{
			$plugins->add_hook("myplugins_actions", array("JB_AdminModules", "myplugins_actions"));
			if($permissions === true)
				$plugins->add_hook("myplugins_permission", array("JB_AdminModules", "permissions"));
		}
		else
		{
			if($withMenu === true)
				$plugins->add_hook("admin_{$module}_menu", array("JB_AdminModules", "menu"));
			$plugins->add_hook("admin_{$module}_action_handler", array("JB_AdminModules", "actions"));
			if($permissions === true)
				$plugins->add_hook("admin_{$module}_permissions", array("JB_AdminModules", "permissions"));
		}
	}

	/**
	 * @param array $actions
	 *
	 * @return array mixed
	 */
	public static function myplugins_actions(array $actions)
	{
		global $page, $lang, $info;

		foreach(static::$menu_cache as $m => $t)
		{
			foreach($t as $id => $info)
			{
				if(!isset($lang->$id))
					$lang->load($id, false, true);

				$actions[$id] = array(
					"active"	=> $info['active'],
					"file"		=> "../{$m}/".$info['file']
				);

				$sub_menu = array();
				$sub_menu['10'] = array("id" => $id, "title" => $lang->$id, "link" => "index.php?module=myplugins-{$id}");
				$sidebar = new SidebarItem($lang->$id);
				$sidebar->add_menu_items($sub_menu, $actions[$info]['active']);

				$page->sidebar .= $sidebar->get_markup();
			}
		}

		return $actions;
	}

	/**
	 * @param array $actions
	 *
	 * @return array
	 */
	public static function actions(array $actions)
	{
		global $plugins, $lang;

		preg_match("#admin_([a-z]*)_action_handler#i", $plugins->current_hook, $match);
		$module = $match[1];
		if(!isset(static::$menu_cache[$module]))
			return $actions;

		foreach(static::$menu_cache[$module] as $id => $info)
		{
			$actions[$id] = array(
				"active"	=> $info['active'],
				"file"		=> $info['file']
			);
		}

		return $actions;
	}

	/**
	 * @param array $sub_menu
	 *
	 * @return array
	 */
	public static function menu(array $sub_menu)
	{
		global $plugins, $lang;

		preg_match("#admin_([a-z]*)_menu#i", $plugins->current_hook, $match);
		$module = $match[1];
		if(!isset(static::$menu_cache[$module]))
			return $sub_menu;

		foreach(static::$menu_cache[$module] as $id => $info)
		{
			if(!isset($lang->$id))
				$lang->load($id, false, true);

			$sub_menu[] = array("id" => $id, "title" => $lang->$id, "link" => "index.php?module={$module}-{$id}");
		}

		return $sub_menu;
	}

	/**
	 * @param array $admin_permissions
	 *
	 * @return array
	 */
	public static function permissions(array $admin_permissions)
	{
		global $plugins, $lang;

		$module = false;
		if($plugins->current_hook != "myplugins_permission")
		{
			preg_match("#admin_([a-z]*)_permissions#i", $plugins->current_hook, $match);
			$module = $match[1];
			if(!isset(static::$menu_cache[$module]))
				return $admin_permissions;
		}

		foreach(static::$menu_cache as $m => $t)
		{
			if($module !== false && $m != $module)
				continue;

			foreach($t as $id => $info)
			{
				if(!isset($lang->$id))
					$lang->load($id, false, true);

				$l = "{$id}_permission";
				$admin_permissions[$id] = $lang->$l;
			}
		}
	
		return $admin_permissions;
	}
}
