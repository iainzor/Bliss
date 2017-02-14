<?php
namespace Database;

class Registry
{
	/**
	 * @var PDO[]
	 */
	private $instances = [];
	
	/**
	 * Check if the registry is empty
	 * 
	 * @return bool
	 */
	public function isEmpty() : bool
	{
		return empty($this->instances);
	}
	
	/**
	 * Assign a \PDO instance to the registry
	 *  
	 * @param string $name
	 * @param \PDO $pdo
	 */
	public function set(string $name, \PDO $pdo)
	{
		$this->instances[$name] = $pdo;
	}
	
	public function get(string $name) : \PDO
	{
		if (!isset($this->instances[$name])) {
			throw new \Exception("Database '{$name}' has not been registered");
		}
		
		return $this->instances[$name];
	}
}