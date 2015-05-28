<?php

class JB_Lang
{
	/**
	 * @var array
	 */
	private static $jlang = null;

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public static function get($name)
	{
		global $lang;

		$l = $lang->language;

		if(empty(static::$jlang))
		{
			// English is our fallback and is always loaded
		    require_once JB_INCLUDES."languages/english.php";
			if($l != "english" && file_exists(JB_INCLUDES."languages/{$l}.php"))
			    require_once JB_INCLUDES."languages/{$l}.php";

			static::$jlang = $jb_lang;
		}

		if($l != "english" && isset(static::$jlang["{$l}_{$name}"]))
			return static::$jlang["{$l}_{$name}"];
		else if(isset(static::$jlang["english_{$name}"]))
			return static::$jlang["english_{$name}"];
		else
			return $name;
	}
}
