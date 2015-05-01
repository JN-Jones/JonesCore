<?php

class JB_Packages
{
	/**
	 * vendor => prefix
	 *
	 * @var array
	 */
	private $vendors = array();

	/**
	 * codename => prefix
	 *
	 * @var array
	 */
	private $plugins = array();

	/**
	 * @var JB_Packages
	 */
	private static $instance = null;

	/**
	 * @return JB_Packages
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
	 * @return JB_Packages
	 */
	public static function i()
	{
		return static::getInstance();
	}

	/**
	 * Register a new vendor with a given prefix
	 *
	 * @param string $prefix
	 * @param string $vendor
	 */
	public function registerVendor($prefix, $vendor)
	{
		if(isset($this->vendors[$vendor]) && $this->vendors[$vendor] != $prefix)
			die("{$vendor} is already registered with prefix {$this->vendors[$vendor]}");
		$this->vendors[$vendor] = $prefix;
	}

	/**
	 * Register a new plugin with the codename for a registered vendor
	 *
	 * @param string $vendor
	 * @param string $codename
	 */
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

	/**
	 * Register a vendor with prefix and directly add a plugin
	 *
	 * @param string $prefix
	 * @param string $vendor
	 * @param string $codename
	 */
	public function register($prefix, $vendor, $codename)
	{
		$this->registerVendor($prefix, $vendor);
		$this->registerPlugin($vendor, $codename);
	}

	/**
	 * @param string $codename
	 *
	 * @return string Returns "JB" if nothing is found
	 */
	public function getPrefixForCodename($codename)
	{
		$codename = strtolower($codename);
		if(isset($this->plugins[$codename]))
			return $this->plugins[$codename];
		return "JB";
	}

	/**
	 * @param string $vendor
	 *
	 * @return bool|string Returns false on failure
	 */
	public function getPrefixForVendor($vendor)
	{
		if(isset($this->vendors[$vendor]))
			return $this->vendors[$vendor];
		if(strtolower($vendor) == "jones")
			return "JB";
		return false;
	}

	/**
	 * @param string $prefix
	 *
	 * @return bool|string Returns false on failure
	 */
	public function getVendorForPrefix($prefix)
	{
		if(strtolower($prefix) == "jb")
			return "jones";

		foreach($this->vendors as $vendor => $pr)
		{
			if($pr == $prefix)
				return $vendor;
		}
		return false;
	}

	/**
	 * @param string $codename
	 *
	 * @return bool|string Returns false on failure
	 */
	public function getVendorForCodename($codename)
	{
		$prefix = $this->getPrefixForCodename($codename);
		return $this->getVendorForPrefix($prefix);
	}

	/**
	 * @param string $vendor
	 *
	 * @return array
	 */
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

	/**
	 * @param string $codename
	 *
	 * @return string
	 */
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
