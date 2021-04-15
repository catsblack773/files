<?php
namespace core;

class Stack
{
	private $_stack;

	public function get($key = null) {
		if (is_null($key)) {
			return $this->_stack;
		}
		return isset($this->_stack[$key]) ? $this->_stack[$key] : [];
	}

	public function set($key, $data = []) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->_stack[$k] = $v;
			}
			return true;
		}
		$this->_stack[$key] = $data;
		return true;
	}

	public function remove($key = null) {
		if (is_null($key)) {
			$this->_stack = [];
			return true;
		}
		if (isset($this->_stack[$key])) {
			unset($this->_stack);
			return true;
		}
		return false;
	}
}
