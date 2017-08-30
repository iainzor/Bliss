<?php
namespace Database;

use Core\AbstractApplication,
	Core\ConfigurableModuleInterface,
	Core\ModuleConfig;

class Module implements ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config) 
	{
		$app->di()->register(PDO::class, function() use ($config) {
			$host = $config->get(Config::DEFAULT_HOST);
			$schema = $config->get(Config::DEFAULT_SCHEMA);
			$username = $config->get(Config::DEFAULT_USER);
			$password = $config->get(Config::DEFAULT_PASSWORD);
			$dsn = "mysql:host={$host};dbname={$schema}";
			
			return new PDO($dsn, $username, $password, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]);
		});
	}
}