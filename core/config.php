<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class config extends base {
	public function init() {
		$defaults = [
			'environment'=>ENV,
			'default_controller'=>'main',
			'default_method'=>'index',
			'error_reporting'=>E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT,
			'display_errors'=>0,
			'timezone'=>'UTC',
			'request_method_format'=>'%c%a%mAction', // %c called method, %a ajax, %m request method
			'request_methods'=>['get'=>'','post'=>'Post','put'=>'Put','delete'=>'Delete','cli'=>'Cli'],
			'server'=>$_SERVER,
			'post'=>$_POST,
			'get'=>$_GET,
			'cookie'=>$_COOKIE,
			'env'=>$_ENV,
			'files'=>$_FILES,
			'request'=>$_REQUEST,
			'session'=>[],
			'put'=>[],
		];

		/* merge loaded over defaults */
		$this->data['application'] = array_replace_recursive($defaults,$this->absolute('application'));
	}

	/* give me everything! */
	public function __toString() {
		return json_encode($this->data);
	}

	public function __call($name,$arguments) {
		$return = $this;

		$arguments[0] = (isset($arguments[0])) ? $arguments[0] : '';
		$arguments[1] = (isset($arguments[1])) ? $arguments[1] : '';

		if (substr($name,0,4) == 'set_') {
			/* these are NOT saved between requests */
			$name = substr($name,4);
			$this->data[$name][$arguments[0]] = $arguments[1];
		} else {
			$return = $this->absolute($name,$arguments[0],$arguments[1]);
		}

		return $return;
	}

	public function absolute($filename,$field=NULL,$default=NULL) {
		$env_value = ENV;

		if (!isset($this->data[$filename])) {
			/* exact path match? */
			if ($filename{0} == '/') {
				$combined = $this->_get_config($filename);
			} else {
				$base_config = $env_config = [];

				if ($config_filename = stream_resolve_include_path('config/'.$filename.'.php')) {
					$base_config = $this->_get_config($config_filename);
				}

				if ($env_value) {
					if ($config_filename = stream_resolve_include_path('config/'.$env_value.'/'.$filename.'.php')) {
						$env_config = $this->_get_config($config_filename);
					}
				}

				$combined = array_replace_recursive($base_config,$env_config);
			}

			$this->data[$filename] = $combined;
		}

		if ($field) {
			return (!isset($this->data[$filename][$field])) ? $default : $this->data[$filename][$field];
		} else {
			return $this->data[$filename];
		}
	} /* end item */

	protected function _get_config($filename) {
		$config = [];

		if (file_exists($filename)) {
			include $filename;

			if (!isset($config)) {
				throw new \Exception('$config variable not found in "'.$filename.'"',800);
			}
		} else {
			throw new \Exception('Config file not found at "'.$filename.'"',801);
		}

		return $config;
	}

} /* end config class */