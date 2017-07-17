<?php
namespace Cache;

use Core\AbstractApplication,
	Core\ConfigurableModuleInterface,
	Core\ModuleConfig;

class Module implements ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config) 
	{
		$app->di()->register(Cache::class, function() use ($app, $config) {
			
			$driverClass = $config->get(Config::DRIVER_CLASS);
			$driverOptions = $config->get(Config::DRIVER_OPTIONS, []);
			
			if (!$driverClass) {
				throw new \Exception("No driver class specified in the configuration file");
			}
			
			if (!is_array($driverOptions)) {
				throw new \Exception("Cache driver options must be an array");
			}
			$driverConfig = new DriverConfig($driverOptions);
			$driver = $app->di()->create($driverClass);
			
			if (!($driver instanceof Driver\DriverInterface)) {
				throw new \UnexpectedValueException("Cache driver must be an instance of ". Driver\DriverInterface::class);
			}
			
			$driver->configure($driverConfig);
			
			return new Cache(
				new Config($config), 
				$driver
			);
		});
	}
}