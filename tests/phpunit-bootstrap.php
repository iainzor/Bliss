<?php
$bootstrap = __DIR__ . DIRECTORY_SEPARATOR ."bootstrap.php";

/* @var $app CmdLine\Application */
$app = include_once $bootstrap;
$argv = $_SERVER["argv"];
$testsDir = array_pop($argv);

if (is_dir($testsDir)) {
	spl_autoload_register(function($className) use ($testsDir) {
		$parts = explode("\\", $className);
		$file = implode(DIRECTORY_SEPARATOR, $parts) .".php";
		$path = $testsDir . DIRECTORY_SEPARATOR . $file;

		if (file_exists($path)) {
			require_once $path;
		}
	});
}
	
