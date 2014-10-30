<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class cache extends base {
	public $cache_path;
	public $cache_ttl;
	public $cache_ext;

	protected function init() {
		$this->cache_path = rtrim($this->c->config->cache('path',NULL),'/');

		if (!is_dir($this->cache_path)) {
			throw new \Exception('Cache Path "'.$this->cache_path.'" Not Found.',810);
		}

		$this->cache_ttl = $this->c->config->cache('ttl',60);
		$this->cache_ext = $this->c->config->cache('ext','.cache');
	}

	/* give me everything! */
	public function __toString() {
		return $this->data;
	}

	public function __call($name,$arguments) {
		if (!isset($arguments[0])) {
			$data = $this->get($name);
		} elseif ($arguments[0] === '') {
			$data = $this->delete($name);
		} else {
			$data = $this->set($name,$arguments[0]);
		}

		return $data;
	}

	/* clean entire cache */
	public function clean() {
		$cache_files = glob($this->cache_path.'/*.cache');

		foreach ($cache_files as $file) {
			@unlink($file);
		}

		$this->data = [];

		return $this;
	}

	protected function delete($key) {
		/* delete it from the request cache */
		unset($this->data[$key]);

		/* delete it from the file system */
		@unlink($this->filename($key));

		return $this;
	}

	protected function set($key,$data) {
		if (is_a($data,'Closure')) {
			$data = $data();
		}

		$temp_file = tempnam('/tmp','cache-file');
		
		file_put_contents($temp_file,serialize($data));

		/* atomic action */
		rename($temp_file,$this->filename($key));

		return $this->data[$key] = $data;
	}

	protected function get($key) {
		/* has it already been loaded on this page request? */
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}

		$filename = $this->filename($key);
		
		$data = NULL;

		if (file_exists($filename)) {
			if (time() - filemtime($filename) < ($this->cache_ttl + mt_rand(0,10))) {
				$this->data[$key] = unserialize(file_get_contents($filename));
	
				$data = $this->data[$key];
			} else {
				@unlink($filename);

				unset($this->data[$key]);
			}
		}

		return $data;
	}

	protected function filename($key) {
		return $this->cache_path.'/'.md5($key).$this->cache_ext;
	}

} /* end cache */