<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class validate extends base {
	protected $attached = [];
	protected $field_data = [];
	protected $error_array = [];
	protected $error_prefix	= '';
	protected $error_suffix	= '';
	protected $error_string = '';
	protected $json_options;
	protected $die_on_failure;
	protected $success;
	protected $error;
	protected $internal = ['string']; /* internal already known libraries */
	protected $errors_detailed = []; /* used for debugging */

	public function init() {
		$this->json_options = $this->c->config->validate('json_options',JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
		$this->die_on_failure = $this->c->config->validate('die_on_failure',TRUE);
		$this->error_prefix = $this->c->config->validate('error_prefix','<p>');
		$this->error_suffix = $this->c->config->validate('error_suffix','</p>');

		$combined_config_functions = $this->c->config->validate('functions',[]);

		foreach ($this->internal as $i) {
			$combined_config_functions = array_merge($combined_config_functions,$this->c->config->absolute(__DIR__.'/../config/validate_'.$i.'.php'));
		}

		/* quickly setup our functions from the config */
		foreach ($combined_config_functions as $name=>$function) {
			$this->attach($name,$function);
		}

		/* setup the defaults */
		$this->clear();
	}

	public function errors_detailed() {
		return $this->errors_detailed;
	}

	public function add_error($text) {
		$this->error_array[] = $text;

		return $this; /* allow chaining */
	}

	public function error_array() {
		return $this->error_array;
	}

	public function error_string($prefix='',$suffix='') {
		$str = '';

		// No errors, validation passes!
		if (count($this->error_array) > 0) {
			$prefix = ($prefix === '') ? $prefix : $this->error_prefix;
			$suffix = ($suffix === '') ? $suffix : $this->error_suffix;

			// Generate the error string

			foreach ($this->error_array as $val) {
				if ($val !== '') {
					$str .= $prefix.$val.$suffix.chr(10);
				}
			}
		}

		return $str;
	}

	public function set_message($text='') {
		$this->error_string = $text;

		return $this; /* allow chaining */
	}

	public function set_error_delimiters($prefix='<p>',$suffix='</p>') {
		$this->error_prefix = $prefix;
		$this->error_suffix = $suffix;

		return $this; /* allow chaining */
	}

	/* get last error */
	public function error($field,$prefix='',$suffix='') {
		$html = end($this->error_array);

		return $prefix.$html.$suffix;
	}

	public function errors_json($options=NULL) {
		$options = ($options)  ? $options : $this->json_options;

		return json_encode(['err'=>TRUE,'errors'=>$this->error_string('','<br>'),'errors_array'=>$this->error_array()],$options);
	}

	public function clear() {
		$this->error_array = [];
		$this->errors_detailed = [];
		$this->die_on_failure = FALSE;
		$this->success = FALSE;

		return $this; /* allow chaining */
	}

	public function attach($name,$func) {
		$this->attached['validate_'.$name] = $func;

		return $this; /* allow chaining */
	}

	public function die_on_fail($boolean=TRUE) {
		$this->die_on_failure = $boolean;

		return $this; /* allow chaining */
	}

	public function test($rules,&$field) {
		/* by default fail on failure */
		$this->single($rules,$field,TRUE);

		return $this; /* allow chaining */
	}

	public function filter($rules,&$field) {
		/* by default who cares on failure use this to filter input only (they all return true) */
		$this->single($rules,$field,FALSE);

		return $this; /* allow chaining */
	}

	public function post($rules='',$index='') {
		$field = $this->c->input->post($index);

		/* filter post and die on fail */
		$this->validate->single($rules,$field);

		return $this; /* allow chaining */
	}

	public function multiple($rules,&$fields) {
		foreach ($rules as $fieldname=>$rule) {
			/* success/fail doesn't matter until we run all the tests on all of the fields */
			$this->single($rule['rules'],$fields[$fieldname],$rule['label']);
		}

		return (bool)(count($this->error_array) == 0);
	} /* end multiple */

	public function single($rules,&$field,$human_label=NULL) {
		/* store rule groups in the validate config */
		$config_rule = $this->c->config->validate('rule_'.$rules);

		$rules = ($config_rule) ? $config_rule : $rules;

		/* if human_label is true then die on fali */
		if ($human_label === TRUE) {
			$this->die_on_fail(TRUE);
			$human_label = NULL;
		}

		/* do we even have a rules to validate against? */
		if (!empty($rules)) {
			$rules = explode('|',$rules);

			foreach ($rules as $rule) {
				/* do we even have a rules to validate against? */
				if (empty($rule)) {
					$this->success = TRUE;
					break;
				}

				/*
				Strip the parameter (if exists) from the rule
				Rules can contain a parameter(s): max_length[5]
				*/
				$param = NULL;

				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
					$rule = $match[1];
					$param = $match[2];
				}

				$this->success = FALSE;
				$this->error_string = '%s is not valid.';

				$attached = $this->attached;

				/* is it a attached (closure) validation function? */
				if (isset($attached['validate_'.$rule])) {
					if ($param !== NULL) {
						$this->success = $attached['validate_'.$rule]($this,$field,$param);
					} else {
						$this->success = $attached['validate_'.$rule]($this,$field);
					}

				/* is it a PHP method? */
				} elseif (function_exists($rule)) {
					/* Try PHP Functions */
					if ($param !== NULL) {
						$success = call_user_func($rule,$field,$param);
					} else {
						$success = call_user_func($rule,$field);
					}

					if (is_bool($success)) {
						$this->success = $success;
					} else {
						$field = $success;
						$this->success = TRUE;
					}
				/* rule not found */
				} else {
					throw new \Exception('Could Not Validate Against "'.$rule.'"',805);
				}

				/* FAIL! */
				if ($this->success === FALSE) {
					/* ok let's clean out the field since it "failed" */
					$field = NULL;

					/* if the label is not provided use the rule name */
					$human_label = (empty($human_label)) ? ucwords(str_replace('_','',$rule)) : $human_label;

					/* replace %s with human label */
					$this->add_error(sprintf($this->error_string, $human_label, $param));

					/* for debugging */
					$this->errors_detailed[] = ['rule'=>$rule,'param'=>$param,'human_label'=>$human_label,'value'=>$field];

					/* they have the die on fail on then die now */
					if ($this->die_on_failure) {
						throw new \Exception('Validation Forgery Detected',806);
					}

					break;
				}
			}
		}

		return $this->success;
	} /* end single */

} /* end validate class */