<?php

class JB_Installer_Tasks extends JB_Installer_Base
{
	/**
	 * {@inheritdoc}
	 */
	static function install($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/tasks.php";

		if(!empty($tasks))
		{
			require_once MYBB_ROOT."inc/functions_task.php";

			$dtask = array(
				"title"			=> $codename,
				"file"			=> $codename,
				"description"	=> "",
				"minute"		=> "0,30",
				"hour"			=> "*",
				"day"			=> "*",
				"weekday"		=> "*",
				"month"			=> "*",
				"enabled"		=> "0", // Should be done on activating
				"logging"		=> "1",
			);

			foreach($tasks as $task)
			{
				$task = array_merge($dtask, $task);
				$task['nextrun'] = fetch_next_run($task);
				$db->insert_query("tasks", dbe($task));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function update($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/tasks.php";

		if(!empty($tasks))
		{
			require_once MYBB_ROOT."inc/functions_task.php";

			$dtask = array(
				"file"		=> $codename,
				"minute"	=> "0,30",
				"hour"		=> "*",
				"day"		=> "*",
				"weekday"	=> "*",
				"month"		=> "*",
				"enabled"	=> "0", // Should be done on activating
				"logging"	=> "1",
			);

			foreach($tasks as $task)
			{
				$task = array_merge($dtask, $task);

				$query = $db->simple_select("tasks", "file", "file='".dbe($task['file'])."'");
				if($db->num_rows($query) == 0)
				{
					$task['nextrun'] = fetch_next_run($task);
					$db->insert_query("tasks", dbe($task));
				}
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function uninstall($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/tasks.php";

		if(!empty($tasks))
		{
			foreach($tasks as $task)
			{
				if(isset($task['file']))
					$db->delete_query("tasks", "file='".dbe($task['file'])."'");
				else
					$db->delete_query("tasks", "file='".dbe($codename)."'");
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function isNeeded($codename)
	{
		return file_exists(JB_Packages::i()->getPath($codename)."install/tasks.php");
	}
}
