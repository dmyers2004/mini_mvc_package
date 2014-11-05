<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class app extends base {
	protected $controller = NULL;
	protected $start_up_path = NULL;
	protected $new_paths = [];

	/* setup a few basic items */
	public function init() {
		/* set our timezone */
		date_default_timezone_set($this->c->config->application('timezone','UTC'));

		/* setup our error display */
		error_reporting($this->c->config->application('error_reporting',0));
		ini_set('display_errors',$this->c->config->application('display_errors',0));

		$this->start_up_path = get_include_path();
	}

	/* get & set the controller object */
	public function controller() {
		return $this->controller;
	}

	public function route($uri=NULL) {
		$this->controller = $this->c->router->route($uri);

		return $this;
	}

	public function output() {
		$this->c->output->display();
	}

	public function add_path($path) {
		/* is it there? */
		if (!is_dir($path)) {
			throw new \Exception('Path "'.$path.'" Not Found Error',812);
		}

		array_unshift($this->new_paths,$path);

		set_include_path($this->start_up_path.PATH_SEPARATOR.implode(PATH_SEPARATOR,$this->new_paths));
	}

	public function remove_path($path) {
		$this->new_paths = array_diff($this->new_paths,$path);

		set_include_path($this->start_up_path.PATH_SEPARATOR.implode(PATH_SEPARATOR,$this->new_paths));
	}

} /* end application */