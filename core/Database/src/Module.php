<?php
namespace Database;

use Bliss\Module\AbstractModule;

class Module extends AbstractModule
{
	/**
	 * @var array
	 */
	private $connections = [
		Config::DEFAULT_CONNECTION => []
	];
	
	public function init()
	{
		Table\AbstractTable::setDbModule($this);
	}
	
	public function connections(array $connections = null) 
	{
		if ($connections !== null) {
			$this->connections = array_merge($this->connections, $connections);
		}
		return $this->connections;
	}
	
	/**
	 * Get a connection to the database
	 * 
	 * @param string $name the name of the connection to retrieve.  Defaults to \Database\Config::DEFAULT_CONNECTION
	 * @return PDO
	 */
	public function connection($name = Config::DEFAULT_CONNECTION)
	{
		$this->app->log("Getting database connection for '{$name}'");
		
		if (!isset($this->connections[$name])) {
			throw new \Exception("Connection by the name of '{$name}' could not be found");
		}
		
		$connection = $this->connections[$name];
		if (!isset($connection["pdo"])) {
			$this->connections[$name]["pdo"] = $this->generatePdo($connection);
		}
		
		return $this->connections[$name]["pdo"];
	}
	
	/**
	 * Get or set the default database connection configuration
	 * 
	 * @param array $config
	 * @return array
	 */
	public function defaultConnection(array $config = null)
	{
		if ($config !== null) {
			$this->connections[Config::DEFAULT_CONNECTION] = $config;
		}
		return $this->connections[Config::DEFAULT_CONNECTION];
	}
	
	/**
	 * Generate a new PDO instance using a configuration array
	 * 
	 * @param array $config
	 * @return \Database\PDO
	 * @throws \Exception
	 */
	private function generatePDO(array $config)
	{
		$options = array_replace([
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		], isset($config[Config::CONF_OPTIONS]) ? $config[Config::CONF_OPTIONS] : []);
		
		$config = array_merge([
			Config::CONF_USER	=> null,
			Config::CONF_PASSWORD => null
		], $config);
		
		if (!isset($config[Config::CONF_DSN])) {
			throw new \Exception("No DSN value provided in connection configuration");
		}
		
		return new PDO($config[Config::CONF_DSN], $config[Config::CONF_USER], $config[Config::CONF_PASSWORD], $options);
	}
	
	/**
	 * Generate a server registry using the application's modules
	 * 
	 * @return \Database\Registry
	 */
	private function _generateRegistry()
	{
		$registry = new Registry();
		foreach ($this->app->modules() as $module) {
			if ($module instanceof ServerProviderInterface) {
				$module->initDatabaseServer($registry);
			}
		}
		return $registry;
	}
}