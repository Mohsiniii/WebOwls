<?php
session_start();
defined('ENVIRONMENT') ? null : define('ENVIRONMENT', 'DEVELOPMENT');
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('PROJECT_DIR') ? null : define('PROJECT_DIR', 'Web Owls' . DS . 'Web Owls' . DS);

if (ENVIRONMENT == 'DEPLOYMENT') {
	define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . DS);
	define('SERVER_PATH', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . DS);
	define('HTML_BASE_PATH', '/');
} elseif (ENVIRONMENT == 'DEVELOPMENT') {
	define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . DS . PROJECT_DIR);
	define('SERVER_PATH', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . DS . PROJECT_DIR);
	define('HTML_BASE_PATH', '/' . PROJECT_DIR);
} else {
	die('Server failure...');
}

$appData = (object)[
	'name'			=> (object)[
		'abbr'				=> 'Web Owls',
		'acr'					=> 'Web Owls'
	]
];

spl_autoload_register(function ($class) {
	$classRoute = ROOT_PATH . 'app' . DS . 'models' . DS;
	$tmpClassPath = $classRoute . DS . strtolower($class) . '.php';
	if (file_exists($tmpClassPath) === true) {
		require_once $tmpClassPath;
	} else {
		$tmpClassPath = $classRoute . DS . 'classes' . DS . strtolower($class) . '.php';
		if (file_exists($tmpClassPath) === true) {
			require_once $tmpClassPath;
		} else {
			die('Class not found.');
		}
	}
});

require_once 'core/App.php';
require_once 'core/Controller.php';
