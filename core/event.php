<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class event extends base {

	public function register($name, $callback) {
		if (is_callable($callback)) {
			$key = uniqid('callable',TRUE);
		} elseif (is_array($callback)) {	
			$key = get_class($callback[0]).'/'.$callback[1];
		} else {
			$key = $callback;
		}

		$this->data[strtolower($name)][strtolower($key)] = $callback;

		return $this;
	}

	public function trigger($name, $data=NULL) {
		$name = strtolower($name);

		if ($this->has_event($name)) {
			foreach ($this->data[$name] as $listener) {
				if (is_callable($listener)) {
					$responds = call_user_func($listener, $data);

					if ($responds != NULL) {
						$data = $responds;
					}
				}
			}
		}

		return $data;
	}

	public function unregister($name, $callback) {
		$key = strtolower((is_array($callback)) ? get_class($callback[0]).'/'.$callback[1] : $callback);
		$name = strtolower($name);

		unset($this->data[$name][$key]);

		return $this;
	}

	public function has_event($name) {
		return (isset($this->data[$name]) && count($this->data[$name]) > 0);
	}

} /* end of events */