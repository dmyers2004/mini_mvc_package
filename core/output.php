<?php
namespace dmyers\mvc;

use \dmyers\mvc\base;

class output extends base {
	protected $final_output = '';
	protected $headers = [];
	protected $mime_type = 'text/html';

	public function get_output() {
		return $this->final_output;
	}

	public function set_output($output) {
		$this->final_output = $output;

		return $this;
	}

	public function append_output($output) {
		if ($this->final_output == '') {
			$this->final_output = $output;
		} else {
			$this->final_output .= $output;
		}

		return $this;
	}

	public function set_content_type($mime_type) {
		$this->headers[] = ['Content-Type: '.$mime_type, TRUE];

		return $this;
	}

	public function get_content_type() {
		$content_type = $this->mime_type;
		
		for ($i = 0, $c = count($this->headers); $i < $c; $i++) {
			if (sscanf($this->headers[$i][0], 'Content-Type: %[^;]', $ctype) === 1) {
				$content_type = $ctype;

				break;
			}
		}

		return $content_type;
	}

	public function set_header($header, $replace = TRUE) {
		$this->headers[] = [$header, $replace];

		return $this;
	}

	public function get_header($header) {
		// Combine headers already sent with our batched headers
		$headers = array_merge(
			// We only need [x][0] from our multi-dimensional array
			array_map('array_shift', $this->headers),
			headers_list()
		);

		if (empty($headers) || empty($header)) {
			return NULL;
		}

		for ($i = 0, $c = count($headers); $i < $c; $i++) {
			if (strncasecmp($header, $headers[$i], $l = strlen($header)) === 0) {
				return trim(substr($headers[$i], $l+1));
			}
		}

		return NULL;
	}

	/* dump the output */
	public function display($output=NULL) {
		// Set the output data
		if (!$output) {
			$output = $this->final_output;
		}

		if (count($this->headers) > 0) {
			foreach ($this->headers as $header) {
				@header($header[0], $header[1]);
			}
		}

		if (method_exists($this->c->app->controller(),'_output')) {
			$this->c->app->controller()->_output($output);
		} else {
			echo $output;
		}
	}

	public function nocache() {
		$this->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		$this->set_header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
		$this->set_header('Cache-Control: post-check=0, pre-check=0', FALSE);
		$this->set_header('Pragma: no-cache');

		/* allow chaining */
		return $this;
	}

	/* wrapper for input delete cookie */
	public function delete_cookie($name='',$domain='',$path='/',$prefix='') {
		$this->c->input->set_cookie($name,'','',$domain,$path,$prefix);

		/* allow chaining */
		return $this;
	}

	/* wrapper for setting a cookie */
	public function cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE) {
		$this->c->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure,$httponly);

		/* allow chaining */
		return $this;
	}

	public function json($data=array(),$val=NULL) {
		$data = ($val !== NULL) ? array($data=>$val) : $data;
		$json = (is_array($data)) ? json_encode($data) : $data;

		$this
			->nocache()
			->set_content_type('application/json','utf=8')
			->set_output($json);

		/* allow chaining */
		return $this;
	}
	
} /* end response */