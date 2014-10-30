<?php 

$config['max_width'] = function($validate,$file,$width = 0) {
	$validate->set_message('Width is greater than %s.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[0] <= $width);
};

$config['max_height'] = function($validate,$file,$height = 0) {
	$validate->set_message('Height is greater than %s.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[1] <= $height);
};

$config['min_width'] = function($validate,$file,$width = 0) {
	$validate->set_message('Width is less than %s.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[0] <= $width);
};

$config['min_height'] = function($validate,$file,$height = 0) {
	$validate->set_message('Height is less than %s.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[1] <= $width);
};

$config['exact_width'] = function($validate,$file,$width = 0) {
	$validate->set_message('Width must be %s.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[0] == $width);
};

$config['exact_height'] = function($validate,$file,$height = 0) {
	$validate->set_message('Height must be %s.');

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[1] == $height);
};

$config['max_dim'] = function($validate,$file,$dim='') {
	$dim = explode(',',$dim);
	
	$validate->set_message('The width & height cannot be greater than '.$dim[0].'x'.$dim[1]);

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[0] < $dim[0] && $size[1] < $dim[1]);
};

$config['min_dim'] = function($validate,$file,$dim='') {
	$dim = explode(',',$dim);

	$validate->set_message('The width & height cannot be less than '.$dim[0].'x'.$dim[1]);

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[0] > $dim[0] && $size[1] > $dim[1]);
};

$config['exact_dim'] = function($validate,$file,$dim='') {
	$dim = explode(',',$dim);

	$validate->set_message('The width & height must be '.$dim[0].'x'.$dim[1]);

	if (!file_exists($file)) {
		$validate->set_message('File Not Found.');

		return FALSE;
	}

	$size = get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file);

	return (bool)($size[0] == $dim[0] && $size[1] == $dim[1]);
};

function get_image_dimension_37cf227882a2e173c20ed47dd4a1e965($file_name) {
	if (function_exists('getimagesize')) {
		$d	= @getimagesize($file_name);
		return $d;
	}

	Throw new \exception('Get Image Size Function Not Supported',809);
};
