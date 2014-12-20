<?php

// Shortcuts for the escape functions - hopefully nobody else likes short names :D
function e($string)
{
	return JB_Helpers::escapeOutput($string);
}
function dbe($el)
{
	if(is_array($el))
		return JB_Helpers::escapeDatabaseArray($el);
	return JB_Helpers::escapeDatabase($el);
}
function path($codename)
{
	return JB_Packages::i()->getPath($codename);
}
function prefix($codename)
{
	return JB_Packages::i()->getPrefixForCodename($codename);
}