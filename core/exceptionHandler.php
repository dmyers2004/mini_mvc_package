<?php
namespace dmyers\mvc;

/*
800 $config variable not found
801 config file not found
802 container value not found
803 controller file not found
804 method not found
805 could not validate against
806 validation forgery detected
807 view file not found
808 access denied
809 validation error
810 cache error
811 theme error
812 application path error
813 page plugin not found
*/

class exceptionHandler {
	protected static $attached = [];
	protected static $container;

	public static function attach($number,$function) {
		self::$attached[$number] = $function;
	}

	public static function handleException(\Exception $exception) {
		$num = $exception->getCode();

		if (array_key_exists($num,self::$attached)) {
			$closure = self::$attached[$num];
			$closure($exception,self::$container);
		} else {
			echo('<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Syntax Error</title></head><body><code>
				Version: PHP '.phpversion().'<br>
				Memory: '.floor(memory_get_peak_usage()/1024).'K of '.ini_get('memory_limit').' used<br>
				Error Code: '.$num.'<br>
				Error Message: '.$exception->getMessage().'<br>
				File: '.$exception->getFile().'<br>
				Line: '.$exception->getLine().'<br>
				</code></body></html>');
			exit(1);
		}
	}

	public static function init(container &$container) {
		self::$container = $container;

		$configs = $container->config->exception();

		foreach ($configs as $num=>$function) {
			self::$attached[$num] = $function;
		}
	}

} /* end exception */