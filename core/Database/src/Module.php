<?php
namespace Database;

use Bliss\Module\AbstractModule;

class Module extends AbstractModule
{
	/**
	 * @var PDO
	 */
	private $connection;
	
	/**
	 * @var array
	 */
	private $defaultConnection = [];
	
	/**
	 * Get a connection to the database
	 * 
	 * @return PDO
	 */
	public function connection()
	{
		$this->app->log("Getting database connection");
		
		if (!isset($this->connection)) {
			$registry = $this->_generateRegistry();
			$this->connection = $registry->generateConnection(
				$this->defaultConnection
			);
		}
		
		return $this->connection;
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
			$this->defaultConnection = $config;
		}
		return $this->defaultConnection;
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