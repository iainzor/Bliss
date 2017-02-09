<?php
namespace Logs;

use Core\BootableModuleInterface,
	Core\DI;

class Module implements BootableModuleInterface
{
	public static function bootstrap(\Core\AbstractApplication $app) 
	{
		$app->di()->register(Logger::class, function() use ($app) {
			try {
				$output = $app->di()->get(Output\OutputInterface::class);
			} catch (\Exception $e) {
				throw new \Exception(
					"No default log output interface was provided to the injector.\n" .
					"\t> ". DI::class ."::register(". Output\OutputInterface::class .", [output instance])\n\n"
				);
			}
			return new Logger($output);
		});
	}
}