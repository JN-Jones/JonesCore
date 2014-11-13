<?php

// This file is only supposed to do some general checks (eg Core installed)
if(!file_exists(MYBB_ROOT."inc/plugins/jones/core/Core.php"))
    define("JB_CORE_INSTALLED", false);
else
{
	define("JB_CORE_INSTALLED", true);
	// Require the core and get the instance once - mainly to setup the auto loader in that class
	require_once MYBB_ROOT."inc/plugins/jones/core/Core.php";
	JB_Core::i();
}

// Called on installation when the core isn't set up
function jb_install_core()
{
	// We don't want to have any problems guys
	if(JB_CORE_INSTALLED === true)
	    return;

	$auto = jb_download_core();

	// Still nothing here? Poke the user!
	if($auto === false)
	{
		global $page;

		$page->output_header("Jones Core not installed");

		$table = new Table;
		$table->construct_header("Attention");
		$table->construct_cell("Jones Core classes are missing. Please load them from <a href=\"https://github.com/JN-Jones/JonesCore\">GitHub</a> and follow the instractions in the ReadMe. Afterwards you can reload this page.");
		$table->construct_row();
		$table->output("Jones Core not installed");

		$page->output_footer();
		exit;	
	}
}

function jb_update_core()
{
	$auto = jb_download_core();

	if($auto === false)
	{
		global $page;

		$page->output_header("Auto Update failed");

		$table = new Table;
		$table->construct_header("Attention");
		$table->construct_cell("Not able to auto update the core. Please load it from <a href=\"https://github.com/JN-Jones/JonesCore\">GitHub</a> and follow the instractions in the ReadMe.");
		$table->construct_row();
		$table->output("Auto Update failed");

		$page->output_footer();
		exit;
	}
}

// TODO: Grab the latest package from github and unpack it
function jb_download_core()
{
	return false;
}