<?php

class JB_Packages
{
	// vendor => prefix
	private $vendors = array();
	// codename => prefix
	private $plugins = array();

	// Singleton
	private static $instance = null;
	public static function getInstance()
	{
		if(static::$instance === null)
			static::$instance = new self();

		return static::$instance;
	}
	// Short it a bit
	public static function i()
	{
		return static::getInstance();
	}


	public function registerVendor($prefix, $vendor)
	{
		if(isset($this->vendors[$vendor]) && $this->vendors[$vendor] != $prefix)
			die("{$vendor} is already registered with prefix {$this->vendors[$vendor]}");
		$this->vendors[$vendor] = $prefix;
	}

	public function registerPlugin($vendor, $codename)
	{
		$codename = strtolower($codename);
		$prefix = $this->getPrefixForVendor($vendor);
		if($prefix === false)
			die("Vendor {$vendor} isn't registered!");
		if(isset($this->plugins[$codename]) && $this->plugins[$codename] != $prefix)
			die("Plugin {$codename} is already registered for Vendor ".$this->getVendorForPrefix($prefix));
		$this->plugins[$codename] = $prefix;
	}

	public function register($prefix, $vendor, $codename)
	{
		$this->registerVendor($prefix, $vendor);
		$this->registerPlugin($vendor, $codename);
	}

	public function getPrefixForCodename($codename)
	{
		$codename = strtolower($codename);
		if(isset($this->plugins[$codename]))
			return $this->plugins[$codename];
		return "JB";
	}

	public function getPrefixForVendor($vendor)
	{
		if(isset($this->vendors[$vendor]))
			return $this->vendors[$vendor];
		if(strtolower($vendor) == "jones")
			return "JB";
		return false;
	}

	public function getVendorForPrefix($prefix)
	{
		if(strtolower($prefix) == "jb")
			return "Jones";

    	foreach($this->vendors as $vendor => $pr)
		{
			if($pr == $prefix)
				return $vendor;
		}
		return false;
	}

	public function getVendorForCodename($codename)
	{
		$prefix = $this->getPrefixForCodename($codename);
		return $this->getVendorForPrefix($prefix);
	}

	public function getPlugins($vendor)
	{
		$prefix = $this->getPrefixForVendor($vendor);
		$plugins = array();
		foreach($this->plugins as $codename => $pr)
		{
			if($pr == $prefix)
				$plugins[] = $codename;
		}
		return $plugins;
	}

	public function getPath($codename)
	{
		$codename = strtolower($codename);
		$vendor = $this->getVendorForCodename($codename);
		$path = JB_PLUGINS."{$vendor}/{$codename}/";
		if(!is_dir($path))
			die("{$codename} doesn't follow the correct dir structure. There should be a dir in 'inc/plugins/{$vendor}/{$codename}'");
		return $path;
	}
}