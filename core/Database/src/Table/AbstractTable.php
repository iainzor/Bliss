<?php
namespace Database\Table;

use Database\PDO,
	Database\Query;

abstract class AbstractTable
{
	use JoinableTrait, RelationTrait;
	
	/**
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var Definition
	 */
	private $definition;
	
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
	abstract public function connectionName();
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct()
	{
		$this->name = $this->defaultTableName();
		$this->definition = new Definition();
		
		if ($this instanceof DefinitionInterface) {
			$this->initTableDefinition($this->definition);
		}
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
				throw new \Exception("No database instance could be found and the database module has not been provided");
			}
			
			$this->db = self::$dbModule->connection($this->connectionName());
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
	 * Get the fully qualified name of the table
	 * 
	 * @return string
	 */
	public function qualifiedName()
	{
		return $this->db()->schemaName() .".". $this->name();
	}
	
	/**
	 * Get the qualified name of a column
	 * 
	 * @param string $name
	 * @return string
	 */
	public function column($name)
	{
		$column = null;
		foreach ($this->definition->columns() as $c) {
			if ($c->name() === $name || $c->displayName() === $name) {
				$column = $c;
			}
		}
		
		return $this->name() .".". ($column ? $column->name() : $name);
	}
	
	/**
	 * Create a new SelectQuery for the table
	 * 
	 * @return \Database\Query\SelectQuery
	 */
	public function select()
	{
		$query = new Query\SelectQuery();
		$query->from([$this->db()->schemaName(), $this->name()]);
		
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
		$factory = $this->db()->queryBuilder();
		$query = $factory->buildSelectQuery($this, $params);
		
		print_r($query);
		exit;
		
		
		foreach ($params as $name => $value) {
			$query->where("{$name} = :{$name}")->params([
				":{$name}" => $value
			]);
		}
		$this->applyJoins($query);
		
		
		var_dump($query->sql());
		exit;
		
		$result = $this->db()->fetchRow($query->sql(), $query->getParams());
		
		return $this->definition->parseRow($result);
	}
}