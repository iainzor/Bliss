<?php
namespace Database;

use Bliss\Module\AbstractModule;

class Module extends AbstractModule
{
	/**
	 * @var \PDO
	 */
	private $connection;
	
	/**
	 * Get a connection to the database
	 * 
	 * @return \PDO
	 */
	public function database()
	{
		$this->app->log("Getting database connection");
		
		if (!isset($this->connection)) {
			$registry = $this->_generateRegistry();
			$this->connection = $registry->generateConnection(
				$this->config(Config::SECTION_DEFAULT_CONNECTION)
			);
		}
		
		return $this->connection;
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