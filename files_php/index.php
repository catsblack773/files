<?php
if (!defined('ROOT')) {
	define('ROOT',  $_SERVER['DOCUMENT_ROOT']);
}

session_start([
    'cookie_lifetime' => 2592000,
]);

$headers = include ROOT.'/classes/config/headers.php';
foreach ($headers as $key => $value) {
	header($key.': '.$value);
}

spl_autoload_register(function ($class) {
	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

	$autoload = __DIR__ . '/classes/' . $class . '.php';
	$vendor = __DIR__ . '/classes/vendors/' . $class . '.php';

	if (file_exists($autoload)) {
		include $autoload;
	} else if (file_exists($vendor)) {
		include $vendor;
	}
});

Apps::run();
