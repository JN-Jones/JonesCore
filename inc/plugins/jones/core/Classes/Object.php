<?php

abstract class JB_Classes_Object
{
	// The "real" data
	protected $data = array();
	// An array of errors which the validation produced
	protected $errors = array();

	// Should return a new object with the $data
	public static function create($data)
	{
		return new static($data);
	}

	// Save our data
	public function __construct($data)
	{
		// -1 is our "not existant"
		if(empty($data['id']))
			$data['id'] = -1;

		static::runHook("construct", $data);

		$this->data = $data;
	}

	public abstract function validate($hard=true);

	// Error functions
	public function getErrors()
	{
		return $this->errors;
	}
	public function getInlineErrors()
	{
		return inline_error($this->errors);
	}

	// Magic PHP methods to use our $data array
	// TODO: possible plugin hooks?
	public function __get($key)
	{
		return $this->data[$key];
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	// Should be overwritten by those packages which support plugin hooks
	public function runHook($name, &$arguments="") {}
}