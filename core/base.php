<?php
namespace dmyers2004\mini_mvc_package;

use dmyers2004\mini_mvc_package\core\container;

class base {
	protected $c;
	protected $data = [];

	public function __construct(container &$container) {
		$this->c = $container;

		if (method_exists($this,'init')) {
			$this->init();
		}
	}

} /* end base */