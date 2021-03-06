<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class log extends base {
	protected $log_level = 0;
	protected $log_file;
	protected $log_format;
	protected $log_generic;

	protected static $psr_levels = [ /* also the method names */
		'EMERGENCY' => 1,
		'ALERT'     => 2,
		'CRITICAL'  => 4,
		'ERROR'     => 8,
		'WARNING'   => 16,
		'NOTICE'    => 32,
		'INFO'      => 64,
		'DEBUG'     => 128,
	];
	protected static $rfc_log_levels = [
		'DEBUG'			=> 100,
		'INFO'			=> 200,
		'NOTICE'		=> 250,
		'WARNING'		=> 300,
		'ERROR'			=> 400,
		'CRITICAL'	=> 500,
		'ALERT'			=> 550,
		'EMERGENCY'	=> 600,
	];

	public function init() {
		$this->log_level = $this->c->config->log('log_level',0);
		$this->log_file = $this->c->app->root().$this->c->config->log('log_file',NULL);
		$this->log_format = $this->c->config->log('log_format','Y-m-d H:i:s');
		$this->log_generic = $this->c->config->log('log_generic','GENERAL');
	}

	public function __call($level,$value) {
		$level = strtoupper($level);

		if (array_key_exists($level, $this->psr_levels)) {
			return $this->_write($level,$value[0]);
		}

		return FALSE;
	} /* end __call */

	protected function _write($level, $msg='') {
		if ($this->log_level > 0) {
			$level = strtoupper($level);

			if ((!array_key_exists($level,$this->psr_levels)) || (!($this->log_level & $this->psr_levels[$level]))) {
				return FALSE;
			}
			
			return $this->write($msg,$level);
		}
	} /* end _write */

	public function write($msg, $level) {
		$level = ($level) ? $level : $this->log_generic;

		if ($this->log_file != NULL) {
			return file_put_contents($this->log_file,date($this->log_format).' '.$level.' '.$msg.chr(10),FILE_APPEND);
		}
		
		return FALSE;
	} /* end write */


} /* end log class */