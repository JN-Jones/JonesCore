<?php

class JB_WIO_Handler
{
	private static $handlers = null;

	public static buildArray()
	{
		global $parameters;
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

	public static buildLink()
	{
		global $lang;

		foreach(static::getHandlers() as $handler)
		{
			if(!$handler::handles($user_activity['activity'], $user_activity[$filename]['action']))
				continue;

			$action = $handler::getActionFor($user_activity['activity'], $user_activity[$filename]['action']);

			if(method_exists($handler, "build".ucfirst($action)))
			{
				$me = "build".ucfirst($action);
				$link = $handler::$me;
			}
			else if(isset($lang->$action))
				$link = $lang->$action;
			else
				$link = $action;

			$array['location_name'] = $lang->todo_wol;          	
		}
		
		return $array;
	}

	private static getHandlers()
	{
		if(static::$handlers !== null)
			return static::$handlers;

		global $cache;

		$jb_plugins = $cache->read("jb_plugins");
		$active = $cache->read("plugins");
		$active = $active['active'];

		foreach(array_keys($jb_plugins) as $codename)
		{
			// Only activated plugins!
			if(!in_array($codename, $active))
				continue;

			$handler = "JB_{$codename}_WIO_Handler";
			if(class_exists($handler))
			{
				$handler::init();
				static::$handlers[$codename] = $handler;
			}
		}

		return static::$handlers;
	}
}