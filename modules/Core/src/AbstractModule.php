<?php
namespace Core;

abstract class AbstractModule
{
	abstract public static function bootstrap(AbstractApplication $app);
	
	public function init()
	{
		echo "Initializing ". get_class($this) ."\n";
	}
}
