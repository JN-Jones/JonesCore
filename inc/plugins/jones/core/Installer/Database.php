<?php

class JB_Installer_Database extends JB_Installer_Base
{
	static function install($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/tables.php";

		// MyBB's way of creating tables - no need to modify it
		if(!empty($tables))
		{
			foreach($tables as $table)
			{
				$table = preg_replace('#mybb_(\S+?)([\s\.,\(]|$)#', TABLE_PREFIX.'\\1\\2', $table);
				$table = preg_replace('#;$#', $db->build_create_table_collation().";", $table);
				preg_match('#CREATE TABLE (\S+)(\s?|\(?)\(#i', $table, $match);
				if($match[1])
				{
					$db->drop_table($match[1], false, false);
				}
				$db->query($table);
			}
		}

		// Adding new columns
		if(!empty($columns))
		{
			foreach($columns as $table => $column)
			{
				foreach($column as $name => $def)
				{
					$db->add_column($table, $name, $def);
				}
			}
		}
	}

	static function update($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/tables.php";

		// MyBB's way of creating tables - no need to modify it
		if(!empty($tables))
		{
			foreach($tables as $table)
			{
				$table = preg_replace('#mybb_(\S+?)([\s\.,\(]|$)#', TABLE_PREFIX.'\\1\\2', $table);
				$table = preg_replace('#;$#', $db->build_create_table_collation().";", $table);
				preg_match('#CREATE TABLE (\S+)(\s?|\(?)\(#i', $table, $match);
				if($match[1])
				{
					$name = substr($match[1], strlen(TABLE_PREFIX));
					if(!$db->table_exists($name))
						$db->query($table);
				}
			}
		}

		// Adding new columns
		if(!empty($columns))
		{
			foreach($columns as $table => $column)
			{
				foreach($column as $name => $def)
				{
					if(!$db->field_exists($name, $table))
						$db->add_column($table, $name, $def);
				}
			}
		}
	}

	static function uninstall($codename)
	{
		global $db;

		require JB_Packages::i()->getPath($codename)."install/tables.php";

		if(!empty($tables))
		{
			foreach($tables as $table)
			{
				$table = preg_replace('#mybb_(\S+?)([\s\.,\(]|$)#', TABLE_PREFIX.'\\1\\2', $table);
				$table = preg_replace('#;$#', $db->build_create_table_collation().";", $table);
				preg_match('#CREATE TABLE (\S+)(\s?|\(?)\(#i', $table, $match);
				if($match[1])
				{
					$db->drop_table($match[1], false, false);
				}
			}
		}

		if(!empty($columns))
		{
			foreach($columns as $table => $column)
			{
				foreach(array_keys($column) as $name)
				{
					$db->drop_column($table, $name);
				}
			}
		}
	}

	static function isNeeded($codename)
	{
		return file_exists(JB_Packages::i()->getPath($codename)."install/tables.php");
	}
}