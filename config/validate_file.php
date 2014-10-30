<?php

$config['file_size_max'] = function($validation,$file, $bytes = 0) {
	$validate->set_message('File %s size is greater than '.$bytes.' bytes');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = filesize($file);

	return (bool)($size > $bytes);
};

$config['file_size_min'] = function($validation,$file, $bytes = 0) {
	$validate->set_message('File %s size is less than '.$bytes.' bytes');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = filesize($file);

	return (bool)($size > $bytes);
};

$config['is_image_file'] = function($validation,$file) {
	$valid = ['gif','jpeg','jpg','png'];

	$validate->set_message('The %s is not a valid file.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));

	return (bool)in_array($ext,$valid,TRUE);
};

$config['is_file'] = function($validation,$field, $options = NULL) {
	$validate->set_message('The %s is not a valid file.');

	return (bool)is_file($field);
};

$config['is_dir'] = function($validation,$field, $options = NULL) {
	$validate->set_message('The %s is not a valid directory.');

	return (bool)is_dir($field);
};

$config['filename'] = function($validation,$field, $options = NULL) {
	$validate->set_message('The %s is not a valid file name.');

	return (bool)preg_match("/^[0-9a-zA-Z_\-. ]+$/i", $field);
};

$config['foldername'] = function($validation,$field, $options = NULL) {
	$validate->set_message('The %s is not a valid folder name.');

	return (bool)preg_match("/^([a-zA-Z0-9_\- ])+$/i", $field);
};

$config['readable'] = function($validation,$field, $options = NULL) {
	$validate->set_message('The %s is not a readable.');

	return (bool)(is_string($field) && is_readable($field));
};

$config['writable'] = function($validation,$field, $options = NULL) {
	$validate->set_message('The %s is not a writable.');

	return (bool)(is_string($field) && is_writable($field));
};

$config['allowed_types'] = function($validation,$file, $options = NULL) {
	$validate->set_message('%s must contain one of the allowed file extensions.');

	// allowed_type[png,gif,jpg,jpeg]
	$types = ($options) ? $options : '';

	$type = (array) explode(',', $types);

	$ext = strtolower(pathinfo($file,PATHINFO_EXTENSION));

	return (bool)(in_array($ext, $type, TRUE));
};

$config['symbolic_link'] = function($validation,$file, $options = NULL) {
	$validate->set_message('The %s is not a symbolic link.');

	return (bool)(is_string($file) && is_link($file));
};
