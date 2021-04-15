<?php
namespace core;

class Router
{
	public function run() {
		$parse = $this->init();
		$class = '\modules\\'.$parse['app'].'\controllers\Main';
		if (class_exists($class)) {
			$method = $parse['action'].'Action';
			if (method_exists($class, $method)) {
				$obj = new $class;
				return $obj->$method();
			}
		}
		return false;
	}

	private function init() {
		$url = $this->parseUrl();
		$app = $url[0];
		if (isset($url[1])) {
			$action = $this->actionReplace($url[1]);
		} else {
			$action = $this->actionReplace($url[0]);
		}

		if (isset($url[2])) {
			$param = $url[2];
		} else {
			$param = end($url);
		}
		return [
			'app' => $app,
			'action' => $action,
			'param' => $param
		];
	}

	private function actionReplace($action) {
		$a = '';
		$e = explode('-', $action);
		if (count($e) < 2)
			return $e[0];

		foreach($e as $k => $v)
			if ($k < 1)
				$a .= $v;
			else
				$a .= ucfirst($v);
		return $a;
	}

	private function parseUrl() {
		$url = trim(urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');
		return explode('/', empty($url) ? 'main' : $url);
	}
}