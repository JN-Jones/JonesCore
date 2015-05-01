<?php

// Shortcuts for the escape functions - hopefully nobody else likes short names :D

/**
 * @param string $string
 *
 * @return int|string
 */
function e($string)
{
	return JB_Helpers::escapeOutput($string);
}

/**
 * @param array|int|string $el
 *
 * @return array|int|string
 */
function dbe($el)
{
	if(is_array($el))
		return JB_Helpers::escapeDatabaseArray($el);
	return JB_Helpers::escapeDatabase($el);
}

/**
 * @param string $codename
 *
 * @return string
 */
function path($codename)
{
	return JB_Packages::i()->getPath($codename);
}

/**
 * @param string $codename
 *
 * @return string
 */
function prefix($codename)
{
	return JB_Packages::i()->getPrefixForCodename($codename);
}

/**
 * @param mixed
 */
function d()
{
	echo "<pre>";
	array_map('var_dump', func_get_args());
	echo "</pre>";
}

/**
 * @param mixed
 */
function dd()
{
	echo "<pre>";
	array_map('var_dump', func_get_args());
	echo "</pre>";
	die();

}
