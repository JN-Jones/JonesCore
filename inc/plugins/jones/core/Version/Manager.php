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
				$version = JB_Helpers::createNiceVersion($version);
				$updater = JB_Packages::i()->getPrefixForCodename($codename)."_{$codename}_Version_V{$version}";

				if(class_exists($updater) && is_subclass_of($updater, JB_Version_Base))
					$updater::execute();
			}
		}
	}
}