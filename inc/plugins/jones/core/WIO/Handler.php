<?php

class JB_WIO_Handler
{
	private static $handlers = null;

	public static function buildArray($user_activity)
	{
		global $parameters, $user;
		$split_loc = explode(".php", $user_activity['location']);
		if($split_loc[0] == $user['location']) {
			$filename = '';
		} else {
			$filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
		}

		foreach(static::getHandlers() as $handler)
		{
			if(!$handler::handles($filename, $parameters['action']))
				continue;

			$user_activity['activity'] = $filename;
			$user_activity[$filename]['action'] = $parameters['action'];
			$user_activity[$filename]['add'] = $handler::getParamsFor($filename, $parameters['action']);
		}

		return $user_activity;
	}

	public static function buildLink($array)
	{
		global $lang;

		$user_activity = $array['user_activity'];
		$filename = $user_activity['activity'];

		foreach(static::getHandlers() as $handler)
		{
			if(!$handler::handles($filename, $user_activity[$filename]['action']))
				continue;

			$action = $handler::getActionFor($filename, $user_activity[$filename]['action']);

			$l = "{$filename}_{$action}";
			if(method_exists($handler, "build".ucfirst($action)))
			{
				$me = "build".ucfirst($action);
				$link = $handler::$me($user_activity[$filename]['add'], $user_activity[$filename]['action']);
			}
			else if(isset($lang->$l))
				$link = $lang->$l;
			else
				$link = $l;

			$array['location_name'] = $link;          	
		}
		
		return $array;
	}

	private static function getHandlers()
	{
		if(static::$handlers !== null)
			return static::$handlers;

		global $cache;

		$jb_plugins = $cache->read("jb_plugins");
		$active = $cache->read("plugins");
		$active = $active['active'];

		static::$handlers = array();

		foreach(array_keys($jb_plugins) as $codename)
		{
			// Only activated plugins!
			if(!in_array($codename, $active))
				continue;

			$handler = JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_WIO_Handler";
			if(class_exists($handler))
			{
				$handler::init();
				static::$handlers[$codename] = $handler;
			}
		}

		return static::$handlers;
	}
}