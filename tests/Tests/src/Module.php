<?php
namespace Tests;

class Module// implements \Core\BootableModuleInterface
{
	public static function bootstrap(\Core\AbstractApplication $app) {
		
		echo "Bootstrapping Tests Module...\n";
		sleep(1);
	}
	
	public function __construct(\DateTime $now, \Core\AbstractApplication $app, \CmdLine\Application $cmd)
	{
		echo "Constructing Tests Module At: ";
		echo $now->format("H:i:s") ."\n";
	}
}