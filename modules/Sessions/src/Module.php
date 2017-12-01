<?php
namespace Sessions;

use Core\AbstractApplication,
	Core\ConfigurableModuleInterface,
	Core\ModuleConfig;

class Module implements ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config) 
	{
		$app->di()->register(Session::class, function() use ($config) {
			$namespace = $config->get(Config::SESSION_NAMESPACE, "_BLISS_SESSION_");
			$handler = new Handler\DefaultHandler();
			
			return new Session($namespace, $handler);
		});
	}
}