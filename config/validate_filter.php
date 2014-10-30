<?php
/*
Additional PHP functions include

*prep
trim
base64_encode
base64_decode
md5
strtolower
strtouppper
ucwords
strtotime
ucfirst
lcfirst
ltrim
rtrim

*/

$config['if_empty'] = function($validation,&$inp, $default) {
	$inp = (empty($inp)) ? $default : $inp;
};

$config['filter'] = function($validation,&$inp, $strip=NULL) {
	$inp = str_replace(str_split($strip),'',$inp);
};

$config['filter_except'] = function($validation,&$inp, $except='') {
	$inp = preg_replace("/[^".preg_quote($except, "/")."]/", '', $inp);
};

/* transpose characters filter_replace[1,A,2,B] - replace numbers with letters */
$config['filter_replace'] = function($validation,&$inp, $options=NULL) {
	/* built the key value pair */
	$items = explode(',',$options);

	$idx = 0;
	$keys = [];
	$values = [];

	foreach ($items as $item) {
		$idx++;
		if ($idx % 2) {
			$keys[] = $item;
		} else {
			$values[] = $item;
		}
	}

	if (count($keys) > 0 && count($values) > 0) {
		$inp = str_replace($keys,$values,$inp);
	}
};

/* filters uri/url and removes any extra trailing / */
$config['filter_uri'] = function($validation,&$inp) {
	$inp = '/'.trim(trim(strtolower($inp)),'/');
	$inp = preg_replace("#^/^[0-9a-z_*/]*$#",'',$inp);
};

$config['filter_url_safe'] = function($validation,&$inp) {
	$strip = '~`!@$^()* {}[]|\;"\'<>,';
	$inp = str_replace(str_split($strip),'',$inp);
};

/* Filters - remember you can combine these in the mapping etc... */
$config['filter_int'] = function($validation,&$inp) {
	$pos = strpos($inp,'.');

	if ($pos !== FALSE) {
		$inp = substr($inp,0,$pos);
	}

	$inp = preg_replace('/[^\-\+0-9]+/','',$inp);

	$prefix = ($inp[0] == '-' || $inp[0] == '+') ? $inp[0] : '';

	$inp = $prefix.preg_replace('/[^0-9]+/','',$inp);
};

$config['filter_float'] = function($validation,&$inp) {
	$inp = preg_replace('/[^\-\+0-9.]+/','',$inp);

	$prefix = ($inp[0] == '-' || $inp[0] == '+') ? $inp[0] : '';

	$inp = $prefix.preg_replace('/[^0-9.]+/','',$inp);
};

$config['filter_bol'] = function($validation,&$inp) {
	$bols = [1,'1','y','on','yes','t','true',TRUE,0,'0','n','off','no','f','false',FALSE];

	if (is_string($inp)) {
		$inp = strtolower($inp);
	}

	$inp = (in_array($inp,$bols,TRUE)) ? $inp : NULL;
};

$config['filter_bol2int'] = function($validation,&$inp) {
	$true_array_filter = array(1,'1','y','on','yes','t','true',TRUE);

	if (is_string($inp)) {
		$inp = strtolower($inp);
	}

	$inp = (in_array($inp,$true_array_filter,TRUE)) ? 1 : 0;
};

$config['filter_bol2bol'] = function($validation,&$inp) {
	$true_array_filter = array(1,'1','y','on','yes','t','true',TRUE);

	if (is_string($inp)) {
		$inp = strtolower($inp);
	}

	$inp = (in_array($inp,$true_array_filter,TRUE)) ? TRUE : FALSE;
};

/* This will Strip tags, Line Feeds and anything else below 32 and above 127 - good for input type=text */
$config['filter_input'] = function($validation,&$inp, $length=NULL) {
	$inp = filter_string_36b376d20b046503c21763f4a04a7887($inp,FALSE,$length);
};

/* This will Strip tags and anything below 32 EXCEPT linefeeds and above 127 */
$config['filter_str'] = function($validation,&$inp, $length=NULL) {
	$inp = filter_string_36b376d20b046503c21763f4a04a7887($inp,TRUE,$length);
};

/* just a wrapper for filter_str -- make it look like a textarea filter */
$config['filter_textarea'] = function($validation,&$inp, $length=NULL) {
	$inp = filter_string_36b376d20b046503c21763f4a04a7887($inp,TRUE,$length);
};

$config['filter_filename'] = function($validation,&$inp) {
	$inp = preg_replace('/[^\x20-\x7F]/','',$inp);
	$inp = str_replace(str_split('~`!@$^()* {}[]|\;"\'<>,'),' ',$inp);
};

$config['filter_email'] = function($validation,&$inp) {
	$strip = '~!#$%^&*()+=`[]{}:";\'<>,/?|';

	$inp = str_replace(str_split($strip),'',filter_var($inp,FILTER_SANITIZE_EMAIL));

	$pos = strpos($inp,'@');

	if ($pos !== FALSE) {
		$inp = substr($inp,0,$pos + 1).str_replace('@','',substr($inp,$pos + 1));
	}
};

$config['filter_phone'] = function($validation,&$inp) {
	$inp = trim($inp);
	$inp = preg_replace('/[^0-9x]+/',' ',$inp);
	$inp = preg_replace('/ {2,}/', ' ', $inp);
	$inp = preg_replace('/[^\x20-\x7F]/','',$inp);
};

$config['filter_hex'] = function($validation,&$inp) {
	$inp = preg_replace('/[^0-9a-f]+/','',strtolower(trim($inp)));
};

$config['filter_strtotime'] = function($validation,&$inp) {
	$inp = preg_replace('/[^\x20-\x7F]/','',trim($inp));
	$inp = strtotime($inp);
};

$config['filter_length'] = function($validation,&$inp,$length=NULL) {
	$length = (is_numeric($length)) ? $length : 255;	
	$inp = substr($inp,0,$length);
};

function filter_string_36b376d20b046503c21763f4a04a7887(&$inp,$tabfeed=FALSE,$length=NULL,$extra=NULL) {
	if (!empty($inp)) {
		$a = [chr(9),chr(10),chr(13)];
		$b = ['##chr9##','##chr10##','##chr13##'];

		if ($tabfeed) {
			$inp = str_replace($a,$b,$inp);
		}

		$inp = preg_replace('/[^\x20-\x7F]/','',$inp);

		if ($tabfeed) {
			$inp = str_replace($b,$a,$inp);
		}

		if ($extra) {
			$inp = str_replace(str_split($extra),'',$inp);
		}

		if ($length) {
			$length = (is_numeric($length)) ? $length : 255;
			
			$inp = substr($inp,0,$length);
		}
	}

	return $inp;
}
