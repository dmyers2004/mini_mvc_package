<?php
/* load composer autoloader */
$loader = require __DIR__.'/../vendor/autoload.php';

/* What environment is this server running in? */
define('ENV',isset($_SERVER['ENV']) ? $_SERVER['ENV'] : 'cli');
define('ROOT',realpath(__DIR__.'/../'));

$packages = [
	''=>'application/',
	'packages\\'=>'packages/',
];

// setup our composer packages and include path for controllers, config, views
foreach ($packages as $name=>$path) {
	// composer PSR4 autoload
	$loader->addpsr4($name, ROOT.'/'.$path);

	// controller, view, config (include style) autoload
	set_include_path(get_include_path().PATH_SEPARATOR.ROOT.'/'.$path);
}

$c = new \dmyers\mvc\container();

$c->config = $c->shared(function($c) { return new \dmyers\mvc\config($c); });
$c->app = $c->shared(function($c) { return new \dmyers\mvc\app($c); });
$c->event = $c->shared(function($c) { return new \dmyers\mvc\event($c); });

$c->router = $c->shared(function($c) { return new \dmyers\mvc\router($c); });

$c->input = $c->shared(function($c) { return new \dmyers\mvc\input($c); });
$c->output = $c->shared(function($c) { return new \dmyers\mvc\output($c); });

$c->log = $c->shared(function($c) { return new \dmyers\mvc\log($c); });
$c->session = $c->shared(function($c) { return new \dmyers\mvc\session($c); });
$c->view = $c->shared(function($c) { return new \dmyers\mvc\view($c); });
$c->validate = $c->shared(function($c) { return new \dmyers\mvc\validate($c); });

\dmyers\mvc\exceptionHandler::load($c);
set_exception_handler(['\dmyers\mvc\exceptionHandler','handleException']);

/* route and respond */
$c->router->route()->output->display();