<?php

abstract class JB_Classes_Object
{
	/**
	 * The "real" data
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * An array of errors which the validation produced
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Should return a new object with the $data
	 *
	 * @param array $data
	 *
	 * @return static
	 */
	public static function create($data)
	{
		return new static($data);
	}

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		// -1 is our "not existant"
		if(empty($data['id']))
			$data['id'] = -1;

		static::runHook("construct", $data);

		$this->data = $data;
	}

	/**
	 * @param bool $hard
	 *
	 * @return bool
	 */
	public abstract function validate($hard=true);

	// Error functions

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @return string
	 */
	public function getInlineErrors()
	{
		return inline_error($this->errors);
	}

	// Magic PHP methods to use our $data array
	// TODO: possible plugin hooks?

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->data[$key];
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * @param string $key
	 */
	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Should be overwritten by those packages which support plugin hooks
	 *
	 * @param string $name
	 * @param mixed  $arguments
	 *
	 * @return mixed
	 */
	public function runHook($name, &$arguments="") {}
}
