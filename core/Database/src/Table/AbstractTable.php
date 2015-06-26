<?php
namespace Database\Table;

use Database\PDO,
	Database\Query;

abstract class AbstractTable
{
	/**
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var string
	 */
	private $schemaName;
	
	/**
	 * @var \Database\Module
	 */
	private static $dbModule;
	
	/**
	 * @return string
	 */
	abstract public function defaultTableName();
	
	/**
	 * @return string
	 */
	abstract public function defaultSchemaName();
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct()
	{
		$this->name = $this->defaultTableName();
		$this->schemaName = $this->defaultSchemaName();
	}
	
	/**
	 * Get or set the PDO instance for the table
	 * 
	 * If no PDO is explictly set, it will attempt to retrieve it from
	 * the Database module
	 * 
	 * @param PDO $db
	 * @return PDO
	 */
	public function db(PDO $db = null)
	{
		if ($db !== null) {
			$this->db = $db;
		}
		if (!$this->db) {
			if (!self::$dbModule) {
				throw new \Exception("The database module has not been set!");
			}
			
			$this->db = self::$dbModule->connection($this->schemaName());
		}
		
		return $this->db;
	}
	
	/**
	 * Get or set the name of the database table
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		if ($name !== null) {
			$this->name = $name;
		}
		return $this->name;
	}
	
	/**
	 * Get or set the table's schema name
	 * 
	 * @param string $name
	 * @return string
	 */
	public function schemaName($name = null)
	{
		if ($name !== null) {
			$this->schemaName = $name;
		}
		return $this->schemaName;
	}
	
	/**
	 * Set the DB Module instance
	 * 
	 * @param \Database\Module $dbModule
	 */
	public static function setDbModule(\Database\Module $dbModule)
	{
		self::$dbModule = $dbModule;
	}
	
	/**
	 * Create a new SelectQuery for the table
	 * 
	 * @return \Database\Query\SelectQuery
	 */
	public function select()
	{
		$query = new Query\SelectQuery();
		$query->from([$this->schemaName(), $this->name()]);
		
		return $query;
	}
	
	/**
	 * Find a single record from the table
	 * 
	 * @param array $params
	 * @return array
	 */
	public function find(array $params)
	{
		$query = $this->select();
		foreach ($params as $name => $value) {
			$query->where("{$name} = :{$name}")->params([
				":{$name}" => $value
			]);
		}
		$result = $this->db()->fetchRow($query->sql(), $query->getParams());
		
		return $result;
	}
}