<?php
namespace Logs;

use Core\BootableModuleInterface,
	Core\DI;

class Module implements BootableModuleInterface
{
	public static function bootstrap(\Core\AbstractApplication $app) 
	{
		$app->di()->register(new Logger());
	}
}