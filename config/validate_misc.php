<?php

$config['required'] = function($validation,$str) {
	$validation->set_message('required','The %s field is required.');

	return is_array($str) ? (bool) count($str) : (trim($str) !== '');
};

$config['regex_match'] = function($validation,$str, $regex) {
	$validation->set_message('regex_match','The %s field is not in the correct format.');

	return (bool) preg_match($regex, $str);
};

$config['matches'] = function($validation,$str,$field) {
	$validation->set_message('matches','The %s field does not match the %s field.');

	return isset($validation->_field_data[$field]) ? ($str === $validation->_field_data[$field]) : FALSE;
};

$config['differs'] = function($validation,$str, $field) {
	$validation->set_message('differs','The %s field must differ from the %s field.');

	return ! (isset($validation->_field_data[$field]) && $validation->_field_data[$field] === $str);
};

$config['valid_url'] = function($validation,$str) {
	$validation->set_message('valid_url','The %s field must contain a valid URL.');

	return (filter_var($str, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) !== FALSE);
};

$config['valid_email'] = function($validation,$str) {
	$validation->set_message('valid_email','The %s field must contain a valid email address.');

	return (bool)filter_var(trim($str), FILTER_VALIDATE_EMAIL);
};

$config['valid_emails'] = function($validation,$str) {
	$validation->set_message('valid_emails','The %s field must contain all valid email addresses.');

	if (strpos($str, ',') === FALSE) {
		return (bool)filter_var(trim($str),FILTER_VALIDATE_EMAIL);
	}

	foreach (explode(',', $str) as $email) {
		if (trim($email) !== '' && filter_var(trim($str),FILTER_VALIDATE_EMAIL) === FALSE) {
			return FALSE;
		}
	}

	return TRUE;
};

$config['is_bol'] = function($validation,&$inp) {
	$bols = [1,'1','y','on','yes','t','true',TRUE,0,'0','n','off','no','f','false',FALSE];

	if (is_string($inp)) {
		$inp = strtolower($inp);
	}

	return (bool)(in_array($inp,$bols,TRUE));
};

$config['valid_ip'] = function($validation,$ip, $which = '') {
	$validation->set_message('valid_ip','The %s field must contain a valid IP.');

	//first of all the format of the ip address is matched
	if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip)) {
		$parts = explode('.',$ip);

		//now we need to check each part can range from 0-255
		foreach ($parts as $ip_parts) {
			if (intval($ip_parts) > 255 || intval($ip_parts) < 0) {
				return FALSE; //if number is not within range of 0-255
			} else {
				return FALSE; //if format of ip address doesn't matches
			}
		}
		
		return TRUE;
	}

	return FALSE;
};

$config['is_not'] = function($validation,$field, $options = NULL) {
	$validation->set_message('is_not', '%s is not valid.');

	return ($field != $options);
};

$config['matches_pattern'] = function($validation,$field, $options = NULL) {
	$pattern = ($options) ? $options : '';

	$validation->set_message('matches_pattern', 'The %s does not match the required pattern.');

	return (bool) preg_match($pattern, $field);
};

$config['one_of'] = function($validation,$field, $options = NULL) {
	// one_of[1,2,3,4]
	$types = ($options) ? $options : '';

	$validation->set_message('one_of', '%s must contain one of the available selections.');

	return (in_array($field, explode(',', $types)));
};

$config['not_one_of'] = function($validation,$field, $options = NULL) {
	// not_one_of[1,2,3,4]
	$types = ($options) ? $options : '';

	$validation->set_message('not_one_of', '%s must not contain one of the available selections.');

	return (!in_array($field, explode(',', $types)));
};

$config['check_captcha'] = function($validation,$field, $options = NULL) {
	// !todo -- captcha
	$validation->set_message('check_captcha','Captcha is incorrect.');

	return TRUE;
};

/* this will check for any valid primary key mongoid or sql integer */
$config['is_a_primary'] = function($validation,$field, $options = NULL) {
	$validation->set_message('is_a_primary','%s is not a primary id.');

	$field = trim($field);

	/* is it empty? */
	if ($field == '') {
		return FALSE;
	}

	/* is it a sql primary id? */
	if (is_numeric($field)) {
		return TRUE;
	}

	/* is it a mongoid */
	if ((bool)preg_match('/^([a-fA-F0-9]{24})$/',$field)) {
		return TRUE;
	}

	return FALSE;
};