<?php
define("ROOT_DIR", __DIR__ . DIRECTORY_SEPARATOR);
define("PHPUNIT_DOWNLOAD_URL", "https://phar.phpunit.de/phpunit.phar");

/* @var $app \CmdLine\Application */
$app = include_once ROOT_DIR . "bootstrap.php";
$app->di()->call(function(Logs\Logger $logger) use ($app) {
	$logger->registerOutput(new class() implements Logs\Output\OutputInterface {
		public function next(Logs\Message\AbstractMessage $message) {
			echo "> ". $message ."\n";
		}
	});
	
	$pharPath = ROOT_DIR . "phpunit.phar";
	$logger->log("Checking for phpunit.phar at '{$pharPath}'");
	if (!is_file($pharPath)) {
		$logger->log("PHPUnit not found, attempt to download...");
		$from = fopen(PHPUNIT_DOWNLOAD_URL, "r");
		$to = fopen($pharPath, "w");
		
		while ($line = fgets($from)) {
			fputs($to, $line);
		}
		
		fclose($from);
		fclose($to);
		$logger->log("PHPUnit downloaded successfully!");
	} else {
		$logger->log("PHPUnit found, continuing with tests");
	}
	
	$app->moduleRegistry()->each(function(\Core\ModuleDefinition $module) use ($app, $logger) {
		$testDir = $module->rootDir() . DIRECTORY_SEPARATOR ."tests";

		if (is_dir($testDir)) {
			$logger->log("Running tests for module '". $module->getNamespace() ."' (". $module->rootDir() .")");
			$startTime = microtime(true);
			$command = implode(" ", [
				"php",
				"phpunit.phar",
				'--bootstrap="'. ROOT_DIR .'phpunit-bootstrap.php"',
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
});