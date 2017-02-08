<?php
$bootstrap = __DIR__ . DIRECTORY_SEPARATOR ."bootstrap.php";
$app = include_once $bootstrap;
$app->moduleRegistry()->each(function(\Core\ModuleDefinition $module) use ($bootstrap) {
	$testDir = $module->rootDir() . DIRECTORY_SEPARATOR ."tests";
	if (is_dir($testDir)) {
		echo "Running tests for module '". $module->getNamespace() ."'\n";
		
		$command = implode(" ", [
			"php",
			"phpunit.phar",
			'--bootstrap="'. $bootstrap .'"',
			'"'. $module->rootDir() . DIRECTORY_SEPARATOR .'tests"'
		]);
		
		echo $command ."\n";
		
		ob_start();
		system($command);
		$output = ob_get_clean();
		$lines = explode("\n", $output);
		$separator = "\n\t| ";
		
		echo $separator . implode($separator, $lines);
		//echo "\n". str_repeat("=", 30) ."\n";
		echo "\n\n";
	}
});