<?php

abstract class JB_Installer_Base
{
	/**
	 * @param string $codename
	 */
	abstract static function install($codename);
	/**
	 * @param string $codename
	 */
	abstract static function uninstall($codename);
	/**
	 * @param string $codename
	 */
	abstract static function update($codename);
	/**
	 * @param string $codename
	 *
	 * @return bool
	 */
	abstract static function isNeeded($codename);
}
