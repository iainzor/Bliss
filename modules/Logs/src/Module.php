<?php
namespace Logs;

use Core\BootableModuleInterface;

class Module implements BootableModuleInterface
{
	public function bootstrap(\Core\AbstractApplication $app) 
	{
		$app->di()->register(new Logger());
	}
}