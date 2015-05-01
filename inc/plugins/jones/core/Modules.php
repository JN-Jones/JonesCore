<?php

class JB_Modules
{
	/**
	 * @var string
	 */
	private $codename;
	/**
	 * @var string
	 */
	private $path;
	/**
	 * @var string
	 */
	private $template;

	/**
	 * @param string $codename
	 * @param string $template
	 */
	public function __construct($codename=false, $template=false)
	{
		if($codename === false)
			$codename = substr(THIS_SCRIPT, 0, -4);

		if(!is_dir(JB_Packages::i()->getPath($codename)."modules/"))
			die($codename." is not modular");

		$this->codename = $codename;
		$this->path = JB_Packages::i()->getPath($codename)."modules/";
		if($template === false)
		    $this->template = $codename;
		else
			$this->template = $template;
		return $this;
	}

	/**
	 * Loads and runs modules
	 *
	 * @param string $module
	 * @param string $method
	 */
	public function loadModule($module=false, $method="")
	{
		global $mybb, $templates, $lang, $headerinclude, $header, $errors, $write, $footer, $colspan, $masterlink, $theme;

		if($module === false)
			$module = $mybb->get_input('action');

		// Empty is index
		if(empty($module))
			$module = "index";

		// Unknown module - blank page
		if(!file_exists($this->path."{$module}.php"))
			return;

		if($method != "get" && $method != "post")
			$method = $mybb->request_method;

		// Require our nice module classes
		require_once $this->path."{$module}.php";

		// And activate them
		$classname = "Module_".ucfirst($module);
		$mc = new $classname($this);

		if(!($mc instanceof JB_Module_Base))
			die("Module {$classname} is not a subclass of \"JB_Module_Base\"");

		// Let's figure out what to do
		// Something we need to do for post and get?
		$mc->start();

		// If we have a post method and we're posting -> run it
		if($method == "post" && $mc->post)
		{
			// First we need to verify our post key
			verify_post_check($mybb->get_input('my_post_key'));

			$content = $mc->post();
		}
		// Either we don't have a post method or we're not posting
		else
			$content = $mc->get();

		// Do we need to cleanup something?
		$mc->finish();

		if(!empty($content))
		{
			$output = eval($templates->render($this->template));
			output_page($output);
		}
	}
}
