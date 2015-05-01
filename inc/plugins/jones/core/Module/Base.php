<?php

abstract class JB_Module_Base
{
	/**
	 * @var JB_Modules|JB_AdminModules
	 */
	protected $loader = null;
	/**
	 * @var bool
	 */
	public $post = true;

	/**
	 * @param JB_Modules|JB_AdminModules $loader
	 */
	public function __construct($loader=null)
	{
		$this->loader = $loader;
	}

	/**
	 * @return string|void
	 */
	public function start() {}
	/**
	 * @return string|void
	 */
	public function post() {}
	/**
	 * @return string|void
	 */
	abstract public function get();
	/**
	 * @return string|void
	 */
	public function finish() {}
}
