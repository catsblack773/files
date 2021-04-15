<?php
namespace core;

class Request
{
	private $_get = [];
	private $_post = [];
	private $_files = [];
	private $_headers = [];

	public function __construct() {
		if (!empty($_GET)) {
			$this->setGet();
		}
		
		if (!empty($_POST)) {
			$this->setPost();
		}

		if (!empty($_FILES)) {
			$this->setFiles();
		}

		$this->setHeader();
	}

	public function get($key = null): array {
		if (is_null($key)) {
			return $this->_get;
		}
		return isset($this->_get[$key]) ? $this->_get[$key] : [];
	}

	public function post($key = null) {
		if (is_null($key)) {
			return $this->_post;
		}
		return isset($this->_post[$key]) ? $this->_post[$key] : [];
	}

	public function header($key = null) {
		if (is_null($key)) {
			return $this->_headers;
		}
		return isset($this->_headers[$key]) ? $this->_headers[$key] : [];
	}

	public function files($key = null) {
		if (is_null($key)) {
			return $this->_files;
		}
		return isset($this->_files[$key]) ? $this->_files[$key] : [];
	}

	private function setPost() {
		foreach ($_POST as $k => $v) {
			$this->_post[$k] = $this->validate($v);
		}
	}

	private function setGet() {
		foreach ($_GET as $k => $v) {
			$this->_get[$k] = $this->validate($v);
		}
	}

	private function setHeader() {
		if (!function_exists('getallheaders')) {
			foreach (getallheaders() as $name => $value) {
				$this->_headers[strtolower($name)] = $value;
			}
		} else {
			foreach ($_SERVER as $name => $value) {
				if (strncmp($name, 'HTTP_', 5) === 0) {
					$name = strtolower(str_replace(' ', '-', ucwords(str_replace('_', ' ', substr($name, 5)))));
					$this->_headers[$name] = $value;
				}
			}
		}
	}

	private function setFiles() {
		foreach ($_FILES as $k => $v) {
			$this->_files[$k] = $v;
		}
	}

	private function validate($v) {
		return strip_tags($v);
	}
}