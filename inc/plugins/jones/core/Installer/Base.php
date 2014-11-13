<?php

abstract class JB_Installer_Base
{
	abstract static function install($codename);
	abstract static function uninstall($codename);
	abstract static function isNeeded($codename);
}