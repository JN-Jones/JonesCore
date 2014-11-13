<?php

abstract class JB_Activate_Base
{
	abstract static function activate($codename);
	abstract static function deactivate($codename);
	abstract static function isNeeded($codename);
}