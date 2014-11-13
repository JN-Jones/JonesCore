<?php

abstract class JB_Version_Manager
{
	protected static $codename = "";
	protected static $versions = array();

	public static function run($from)
	{
		$codename = static::$codename;
    	foreach(static::$versions as $version)
		{
			if(version_compare($version, $from, ">"))
			{
		    	$version = str_replace(array(".", " ", "_"), "", $version);
				$updater = "JB_{$codename}_Version_V{$version}";

    			if(class_exists($updater))
					$updater::execute();
			}
		}
	}
}