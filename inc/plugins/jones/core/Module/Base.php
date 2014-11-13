<?php

abstract class JB_Module_Base
{
	protected $loader = null;
	public $post = true;

	public function __construct($loader)
	{
		$this->loader = $loader;
	}

	public function start() {}
	public function post() {}
	abstract public function get();
	public function finish() {}
}