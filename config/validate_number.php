<?php 
/*
Additional PHP functions include

*validate
empty
is_array
is_bool
is_double
is_float
is_int
is_integer
is_long
is_null
is_numeric
is_real
is_scalar
is_string
isset

*/

$config['is_array'] = function($validate,$str) {
	$validate->set_message('The %s field must contain only numeric characters.');

	return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
};


$config['numeric'] = function($validate,$str) {
	$validate->set_message('The %s field must contain only numeric characters.');

	return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
};

$config['integer'] = function($validate,$str) {
	$validate->set_message('The %s field must contain an integer.');

	return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
};

$config['decimal'] = function($validate,$str) {
	$validate->set_message('The %s field must contain a decimal number.');

	return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
};

$config['greater_than'] = function($validate,$str, $min) {
	$validate->set_message('The %s field must contain a number greater than %s.');

	return is_numeric($str) ? ($str > $min) : FALSE;
};

$config['greater_than_equal_to'] = function($validate,$str, $min) {
	$validate->set_message('The %s field must contain a number greater than or equal to %s.');

	return is_numeric($str) ? ($str >= $min) : FALSE;
};

$config['less_than'] = function($validate,$str, $max) {
	$validate->set_message('The %s field must contain a number less than %s.');

	return is_numeric($str) ? ($str < $max) : FALSE;
};

$config['less_than_equal_to'] = function($validate,$str, $max) {
	$validate->set_message('The %s field must contain a number less than or equal to %s.');

	return is_numeric($str) ? ($str <= $max) : FALSE;
};

$config['is_natural'] = function($validate,$str) {
	$validate->set_message('The %s field must only contain digits.');

	return ctype_digit((string) $str);
};

$config['is_natural_no_zero'] = function($validate,$str) {
	$validate->set_message('The %s field must only contain digits and must be greater than zero.');

	return ($str != 0 && ctype_digit((string) $str));
};

$config['dollars'] = function($validate,$field, $options = NULL) {
	$validate->set_message('The %s Out of Range.');

	return (bool)preg_match('#^\$?\d+(\.(\d{2}))?$#', $field);
};

$config['percent'] = function($validate,$field, $options = NULL) {
	$validate->set_message('The %s Out of Range.');

	return (bool)preg_match('#^\s*(\d{0,2})(\.?(\d*))?\s*\%?$#', $field);
};

$config['zip'] = function($validate,$field, $options = NULL) {
	$validate->set_message('The %s is invalid.');

	return (bool)preg_match('#^\d{5}$|^\d{5}-\d{4}$#', $field);
};

$config['phone'] = function($validate,$field, $options = NULL) {
	$validate->set_message('The %s is invalid.');

	return (bool)preg_match('/^\(?[\d]{3}\)?[\s-]?[\d]{3}[\s-]?[\d]{4}$/', $field);
};

$config['is_between'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must be between '.$lo.' &amp; '.$hi);

	list($lo,$hi) = explode(',',$options,2);

	return (bool)($field <= $hi && $field >= $lo);
};

$config['is_outside'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must not be between '.$lo.' &amp; '.$hi);

	list($lo,$hi) = explode(',',$options,2);

	return (bool)($field > $hi || $field < $lo);
};

$config['even'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s is not a even number.');

	return ((int)$field % 2 === 0);
};

$config['odd'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s is not a odd number.');

	return ((int)$field % 2 !== 0);
};

$config['int'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s is not a integer.');

	return is_numeric($field) && (int)$field == $field;
};

$config['float'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s is not a floating number.');

	return is_float(filter_var($field, FILTER_VALIDATE_FLOAT));
};

$config['version'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s is not a valid version number.');

  return (bool)preg_match('/^[0-9]+\.[0-9]+\.[0-9]+([+-][^+-][0-9A-Za-z-.]*)?$/',$field);
};

$config['credit_card'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s is not a valid credit card number.');

	$field = preg_replace('([^0-9])', '', $field);

	$sum = 0;
	$input = strrev($field);

	for ($i = 0; $i < strlen($field); $i++) {
		$current = substr($field, $i, 1);

		if ($i % 2 == 1) {
			$current *= 2;
			if ($current > 9) {
				$firstDigit = $current % 10;
				$secondDigit = ($current - $firstDigit) / 10;
				$current = $firstDigit + $secondDigit;
			}
		}

		$sum += $current;
	}

	return (bool)($sum % 10 == 0);
};
