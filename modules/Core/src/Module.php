<?php
namespace Core;

class Module implements ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config) 
	{
		date_default_timezone_set($config->get(Config::DEFAULT_TIMEZONE, "UTC"));
	}
}