#!/usr/bin/env php
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

date_default_timezone_set('America/New_York');

$from_root = realpath(__DIR__);
$to_root = realpath(__DIR__.'/../../../../');

recurse_copy($from_root.'/application',$to_root.'/application');
recurse_copy($from_root.'/public',$to_root.'/public');

rename($to_root.'/public/htaccess',$to_root.'/public/.htaccess');

// add hash key
$content = file_get_contents($to_root.'/application/config/application.php');
$content = str_replace('#hash#',md5(uniqid('',TRUE)),$content);
file_put_contents($to_root.'/application/config/application.php',$content);

@mkdir($to_root.'/packages');
@mkdir($to_root.'/var');
@mkdir($to_root.'/var/logs',0777);
@mkdir($to_root.'/var/cache',0777);
@mkdir($to_root.'/var/misc',0777);

@mkdir($to_root.'/public/assets',0777);
@mkdir($to_root.'/public/assets/css',0777);
@mkdir($to_root.'/public/assets/images',0777);
@mkdir($to_root.'/public/assets/js',0777);
@mkdir($to_root.'/public/assets/vendor',0777);

echo 'Complete'.chr(10);

function recurse_copy($src, $dst) {
	$dir = opendir($src);
	
	@mkdir($dst);
	
	while(false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src.'/'.$file)) {
				recurse_copy($src.'/'.$file,$dst.'/'.$file);
			} else {
				copy($src.'/'.$file,$dst.'/'.$file);
			}
		}
	}
	
	closedir($dir);
}
