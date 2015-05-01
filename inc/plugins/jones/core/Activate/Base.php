<?php

abstract class JB_Activate_Base
{
	/**
	 * @param string $codename
	 */
	abstract static function activate($codename);
	/**
	 * @param string $codename
	 */
	abstract static function deactivate($codename);
	/**
	 * @param string $codename
	 *
	 * @return bool
	 */
	abstract static function isNeeded($codename);
}
