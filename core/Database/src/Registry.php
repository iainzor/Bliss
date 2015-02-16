<?php
namespace Database;

class Registry
{
	/**
	 * @var array
	 */
	private $servers = [];
	
	/**
	 * Add a database server to the registry
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $options
	 */
	public function addServer($dsn, $username = null, $password = null, array $options = [])
	{
		$this->servers[] = [
			"dsn" => $dsn,
			"username" => $username,
			"password" => $password,
			"options" => $options
		];
	}
	
	/**
	 * Count the number of servers registered
	 * 
	 * @return int
	 */
	public function totalServers()
	{
		return count($this->servers);
	}
	
	/**
	 * Generate a random connection using the available servers
	 * 
	 * @param \Config\Config $defaultConnection Optional default connection to use if no servers have been added
	 * @return \Database\PDO
	 * @throws \Exception
	 */
	public function generateConnection(\Config\Config $defaultConnection = null)
	{
		if ($this->totalServers() === 0 && $defaultConnection === null) {
			throw new \Exception("No database connections have been registered");
		} else if ($this->totalServers() === 0) {
			$config = $defaultConnection->toArray();
		} else {
			$config = $this->servers[array_rand($this->servers)];
		}
		
		$options = array_replace([
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		], isset($config[Config::CONF_OPTIONS]) ? $config[Config::CONF_OPTIONS] : []);
		
		$config = array_merge([
			Config::CONF_USER	=> null,
			Config::CONF_PASSWORD => null
		], $config);
		
		if (!isset($config[Config::CONF_DSN])) {
			throw new \Exception("No DSN (". Config::CONF_DSN .") value provided in connection configuration");
		}
		
		return new PDO($config[Config::CONF_DSN], $config[Config::CONF_USER], $config[Config::CONF_PASSWORD], $options);
	}
}