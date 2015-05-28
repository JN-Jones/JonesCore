<?php

abstract class JB_Classes_StorableObject extends JB_Classes_Object implements JB_Classes_Interfaces_Storable
{
	/**
	 * Cache our objects
	 *
	 * @var array
	 */
	static protected $cache = array();

	/**
	 * Whether we use timestamps which needs to be touched
	 *
	 * @var bool
	 */
	static protected $timestamps = false;

	/**
	 * Whether we need to save the user id or not
	 *
	 * @var bool
	 */
	static protected $user = false;

	/**
	 * The table we're operating on
	 *
	 * @var string
	 */
	static protected $table;

	/**
	 * Our default sql options
	 *
	 * @var array
	 */
	static protected $default_options = array();

	/**
	 * {@inheritdoc}
	 */
	public static function getAll($where='', array $options=array())
	{
		global $db;

		$options = array_merge(static::getDefaultOptions(), $options);

		$entries = array();

		$query = $db->simple_select(static::$table, "*", $where, $options);
		while ($e = $db->fetch_array($query))
			$entries[$e['id']] = static::create($e);

		// Merge our current entries to the cache
		static::$cache = array_merge(static::$cache, $entries);

		return $entries;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getNumber($where='')
	{
		return count(static::getAll($where));
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getByID($id)
	{
		global $db;

		$id = (int)$id;

		if(isset(static::$cache[$id]))
			return static::$cache[$id];

		$class = false;

		$query = $db->simple_select(static::$table, "*", "id='{$id}'");
		if($db->num_rows($query) == 1)
		{
			$article = $db->fetch_array($query);
			$class = static::create($article);
		}

		static::$cache[$id] = $class;

		return $class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save()
	{
		global $db, $mybb;

		// First: Validate
		if(!$this->validate(true))
			return false;

		// Escape everything
		$data = dbe($this->data);

		// Not existant -> insert
		if($this->data['id'] == -1)
		{
			unset($data['id']);
			static::runHook("save", $data);

			if(static::$timestamps)
				$this->data['dateline'] = $data['dateline'] = TIME_NOW;
			if(static::$user && empty($this->data['uid']))
				$this->data['uid'] = $data['uid'] = $mybb->user['uid'];
			$this->data['id'] = $db->insert_query(static::$table, $data);
		}
		// exists -> update
		else
			$db->update_query(static::$table, $data, "id='{$this->data['id']}'");

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete()
	{
		global $db;

		$id = $this->data['id'];

		if(isset(static::$cache[$id]))
			unset(static::$cache[$id]);

		$db->delete_query(static::$table, "id='{$id}'");
	}

	/**
	 * Get the default SQL options
	 *
	 * @return array
	 */
	private function getDefaultOptions()
	{
		$order_dir = "desc";
		$order_by = "id";
		if(static::$timestamps)
			$order_by = "dateline";

		$options = array("order_by" => $order_by, "order_dir" => $order_dir);

		if(!empty(static::$default_options))
			$options = array_merge($options, static::$default_options);

		static::runHook("getDefaultOptions");

		return $options;
	}
}
