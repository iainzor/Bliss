<?php
$bootstrap = __DIR__ . DIRECTORY_SEPARATOR ."bootstrap.php";
$app = include $bootstrap;
$app->moduleRegistry()->each(function(\Core\ModuleDefinition $module) use ($bootstrap) {
	$testDir = $module->rootDir() . DIRECTORY_SEPARATOR ."tests";
	if (is_dir($testDir)) {
		echo str_repeat("=", 30) ."\n";
		echo "Running tests for module '". $module->getNamespace() ."'\n";

		$command = implode(" ", [
			"php",
			"phpunit.phar",
			"--bootstrap ". $bootstrap,
			$module->rootDir() . DIRECTORY_SEPARATOR ."tests"
		]);
		echo $command ."\n";
		system($command);
		echo str_repeat("=", 30) ."\n";
		echo "\n";
	}
});

$endTime = microtime(true);
$totalTime = $endTime - $startTime;