<?php
$root = dirname(__DIR__);
$startTime = microtime(true);

chdir($root . DIRECTORY_SEPARATOR . "tests");
require_once $root ."/modules/CmdLine/src/Application.php";
date_default_timezone_set("America/Los_Angeles");

$app = new CmdLine\Application();
$app->moduleRegistry()->registerDirectory(__DIR__ . DIRECTORY_SEPARATOR . "PHPUnit");

foreach (new DirectoryIterator($root ."/modules") as $item) {
	if ($item->isDot() || $item->isFile()) {
		continue;
	}
	$app->moduleRegistry()->registerDirectory($item->getPathname());
}

return $app;