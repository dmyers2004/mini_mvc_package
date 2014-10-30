<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class app extends base {
	protected $controller;

	/* setup a few basic items */
	public function init() {
		/* set our timezone */
		date_default_timezone_set($this->c->config->application('timezone','UTC'));

		/* setup our error display */
		error_reporting($this->c->config->application('error_reporting',0));
		ini_set('display_errors',$this->c->config->application('display_errors',0));
	}
	
	/* get & set the controller object */
	public function controller(&$obj=NULL) {
		$return = $this;
		
		if ($obj == NULL) {
			$return = $this->controller;
		} else {
			$this->controller = $obj;
		}	
		
		return $return;
	}

} /* end application */