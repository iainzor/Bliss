<?php
$bootstrap = __DIR__ . DIRECTORY_SEPARATOR ."bootstrap.php";
$phpunitBootstrap = __DIR__ . DIRECTORY_SEPARATOR ."phpunit-bootstrap.php";

/* @var $app \CmdLine\Application */
$app = include_once $bootstrap;
$app->di()->call(function(Logs\Logger $logger) {
	$logger->registerOutput(new class() implements Logs\Output\OutputInterface {
		public function next(Logs\Message\AbstractMessage $message) {
			echo $message ."\n";
		}
	});
});
$app->moduleRegistry()->each(function(\Core\ModuleDefinition $module) use ($app, $phpunitBootstrap) {
	$app->di()->register(Logs\Output\OutputInterface::class, new Logs\Output\PrintToBuffer());
	$logger = $app->di()->get(Logs\Logger::class);
	$testDir = $module->rootDir() . DIRECTORY_SEPARATOR ."tests";
	
	if (is_dir($testDir)) {
		$logger->log("Running tests for module '". $module->getNamespace() ."' (". $module->rootDir() .")");
		$startTime = microtime(true);
		$command = implode(" ", [
			"php",
			"phpunit.phar",
			'--bootstrap="'. $phpunitBootstrap .'"',
			'"'. $module->rootDir() . DIRECTORY_SEPARATOR .'tests"'
		]);
		
		ob_start();
		system($command);
		$output = ob_get_clean();
		$endTime = microtime(true);
		$lines = explode("\n", $output);
		$separator = "\n\t> ";
		
		echo $separator . implode($separator, $lines);
		//echo "\n". str_repeat("=", 30) ."\n";
		echo "\n\n";
		
		$seconds = number_format(($endTime - $startTime), 5);
		$logger->log("Tests for module '". $module->getNamespace() ."' completed in {$seconds} seconds");
	}
});