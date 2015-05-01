<?php

abstract class JB_WIO_Base
{
	/**
	 * An array of file/action to handle by this class
	 * array("file" => array("action" => "todo"))
	 * Where todo is either a language string (using $lang->todo to retrieve)
	 * or the name of a static function which is called (eg when "todo" the function "buildTodo" is called)
	 *
	 * @var array
	 */
	protected static $handle = array();

	/**
	 * Use this function if you want to load language vars or things like that
	 */
	public static function init() {}

	/**
	 * @param string $filename
	 * @param string $action
	 *
	 * @return bool
	 */
	public static function handles($filename, $action="")
	{
		if(empty($action))
			$action = "index";
		return isset(static::$handle[$filename][$action]);
	}

	/**
	 * @param string $filename
	 * @param string $action
	 *
	 * @return array
	 */
	public static function getParamsFor($filename, $action="")
	{
		return array();
	}

	/**
	 * @param string $filename
	 * @param string $action
	 *
	 * @return string
	 */
	public static function getActionFor($filename, $action="")
	{
		if(empty($action))
			$action = "index";
		return static::$handle[$filename][$action];
	}
}
