<?php
namespace core;

class Response
{
	public function run(): bool {
		if (\Apps::request()->header('is-json')) {
			header("Content-type:application/json");
			echo json_encode($this->data());
			return true;
		} else {
			include ROOT.'/templates/index.php';
			return true;
		}
	}

	public function data($key = null): ?array {
		if (is_null($key)) {
			return $this->_data;
		}
		return isset($this->_data[$key]) ? $this->_data[$key] : false;
	}

	public function setData($key, $data = []): bool {
		if (is_array($key) && empty($data)) {
			$this->_data = $key;
			return true;
		} elseif (is_array($key) && !empty($data)) {
			return false;
		} elseif (!empty($data) && is_array($data)) {
			foreach ($data as $k => $v) {
				$this->_data[$key][$k] = $v;
			}
			return true;
		} elseif (!empty($data) && !is_array($data)) {
			$this->_data[$key] = $data;
			return true;
		}
		return false;
	}
}