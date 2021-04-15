<?php

class Apps
{
	private static $_request = null;
	private static $_router = null;
	private static $_response = null;
	private static $_stack = null;

	private function __construct() {}
	private function __clone() {}

	public static function run() {
		self::init();
		
		$router = self::router()->run();
		if ($router === false) {
			self::response()->setData([
				'res' => false,
				'message' => 'Отсутствует контролер или метод'
			]);
			return false;
		}

		$response = self::response()->run();
		if ($response === fasle) {
			return false;
		}
	}

	public static function request() {
		return self::$_request;
	}

	public static function router() {
		return self::$_router;
	}

	public static function response() {
		return self::$_response;
	}

	public static function stack() {
		return self::$_stack;
	}

	private static function init() {
		self::$_request = new \core\Request;
		self::$_router = new \core\Router;
		self::$_response = new \core\Response;
		self::$_stack = new \core\Stack;
	}
}