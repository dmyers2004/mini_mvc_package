<?php

$config['valid_time'] = function($validate,$field,$options=NULL) {
	$validate->set_message('The %s is Invalid.');

	return (bool)(strtotime($field) > 1000);
};

$config['valid_date'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s Invalid.');

	/* basic format check */
	if (!preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/', $field)) {
		return FALSE;
	}

	list($d, $m, $y) = explode('/', $field);

	return checkdate($d,$m,$y);
};

$config['mysql_datetime'] = function($validate,&$inp,$options = NULL) {
	$format = ($options) ? $options : 'Y-m-d H:i:s';

	$inp = date($format,strtotime($inp));

	return TRUE;
};

$config['valid_datetime'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s Invalid.');

	/*
	optionally we are saying 0000-00-00 00:00:00 is valid
	this could be helpful as a "default" or "empty" value
	*/

	return ($field == '0000-00-00 00:00:00') ? TRUE : (strtotime($field) > 1);
};

$config['valid_dob'] = function($validate,$field, $options = NULL) {
	$validate->set_message('Date in wrong format');

	$yrs = ($options) ? $options : 18;

	/* is this a valid date? strtotime */
	if (!strtotime($field)) {
		return FALSE;
	}

	if (strtotime($field) > strtotime('-'.$yrs.' year', time())) {
		return FALSE;
	}

	if (strtotime($field) < strtotime('-100 year', time())) {
		return FALSE;
	}

	return TRUE;
};

$config['is_future_date'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must be before today.');

	/* is this a valid date? strtotime */
	return (!strtotime($field)) ? FALSE : $this->is_after_date($field, date("Y-m-d 00:00:00"));
};

$config['is_past_date'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must be after today.');

	/* is this a valid date? strtotime */
	return (!strtotime($field)) ? FALSE : $this->is_before_date($field, date("Y-m-d 23:59:59"));
};

$config['is_after_date'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must be after '.date('F j, Y, g:ia',strtotime($options)).'.');

	/* is this a valid date? strtotime */
	if (!strtotime($field)) {
		return FALSE;
	}

	return (bool) (strtotime($field) > strtotime($options));
};

$config['is_before_date'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must be before '.date('F j, Y, g:ia',strtotime($options)).'.');

	/* is this a valid date? strtotime */
	if (!strtotime($field)) {
		return FALSE;
	}

	return (bool) (strtotime($field) < strtotime($options));
};

/* is_between_date[2014-12-25,2015-12-25] */
$config['is_between_dates'] = function($validate,$field, $options = NULL) {
	$validate->set_message('%s must be between '.date('F j, Y',strtotime($after)).' and '.date('F j, Y',strtotime($before)).'.');

	list($after,$before) = explode(',',$options);

	if (!strtotime($after) || !strtotime($before)) {
		return FALSE;
	}

	$is_after = (strtotime($field) > strtotime($after)) ? TRUE : FALSE;
	$is_before = (strtotime($field) < strtotime($before)) ? TRUE : FALSE;

	return (bool) ($is_after && $is_before);
};