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
		$this->data['application'] = array_replace_recursive($defaults,(array)$this->item('application'));
	}

	/* give me everything! */
	public function all() {
		return $this->data;
	}

	/* these are NOT saved between requests */
	public function set($filename,$field) {
		$this->data[$filename][$field];

		return $this;
	}

	public function item($filename,$field=NULL,$default=NULL) {
		$env_value = ENV;

		if (!isset($this->data[$filename])) {
			/* exact path match? */
			if ($filename{0} == '/') {
				$combined = $this->get_config($filename);
			} else {
				$base_config = $env_config = [];

				if ($config_filename = stream_resolve_include_path('config/'.$filename.'.php')) {
					$base_config = $this->get_config($config_filename);
				}

				if ($env_value) {
					if ($config_filename = stream_resolve_include_path('config/'.$env_value.'/'.$filename.'.php')) {
						$env_config = $this->get_config($config_filename);
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

	protected function get_config($filename) {
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