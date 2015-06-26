<?php
namespace Database;

class PDO extends \PDO
{
	/**
	 * @var array
	 */
	private $logs = [];
	
	/**
	 * @var string
	 */
	private $schemaName;
	
	/**
	 * @var QueryBuilder\BuilderInterface
	 */
	private $queryBuilder;
	
	/**
	 * Constructor
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $passwd
	 * @param string $options
	 */
	public function __construct($dsn, $username, $passwd, $options) 
	{
		parent::__construct($dsn, $username, $passwd, $options);
		
		$this->queryBuilder = QueryBuilder\Factory::create($dsn);
	}
	
	/**
	 * Get the name of the database currently connected to
	 * 
	 * @return string
	 */
	public function schemaName()
	{
		if (!$this->schemaName) {
			$this->schemaName = $this->fetchColumn("SELECT DATABASE()");
		}
		return $this->schemaName;
	}
	
	/**
	 * Get or set the QueryBuilder used for the connection
	 * 
	 * @param \Database\QueryBuilder\BuilderInterface $builder
	 * @return \Database\QueryBuilder\BuilderInterface
	 */
	public function queryBuilder(QueryBuilder\BuilderInterface $builder = null)
	{
		if ($builder !== null) {
			$this->queryBuilder = $builder;
		}
		if (!$this->queryBuilder) {
			throw new \Exception("No query builder has been set");
		}
		
		return $this->queryBuilder;
	}
	
	/**
	 * Fetch all results of a SQL statement
	 *
	 * @param string|Query\Query $query
	 * @param array $params
	 * @param int $fetchStyle
	 * @return array
	 */
	public function fetchAll($query, array $params = [], $fetchStyle = \PDO::FETCH_ASSOC)
	{
		$statement = $this->_exec($query, $params);
		$results = $statement->fetchAll($fetchStyle);
		
		unset($statement);
		return $results;
	}

	/**
	 * Fetch a single row from a SQL statement
	 *
	 * @param string $sql
	 * @param array $params
	 * @param int $fetchStyle
	 * @param int $rowOffset
	 * @return mixed
	 */
	public function fetchRow($sql, array $params = [], $fetchStyle = \PDO::FETCH_ASSOC, $rowOffset = 0)
	{
		$statement = $this->_exec($sql, $params);
		$result = $statement->fetch($fetchStyle, \PDO::FETCH_ORI_NEXT, $rowOffset);
		
		unset($statement);
		return $result;
	}

	/**
	 * Fetch a single column's value from a SQL statement
	 * If no results can be found, NULL will be returned
	 *
	 * @param string $sql
	 * @param array $params
	 * @param int $columnNumber
	 * @return mixed
	 */
	public function fetchColumn($sql, array $params = [], $columnNumber = 0)
	{
		$statement = $this->_exec($sql, $params);
		$result = $statement->fetchColumn($columnNumber);
		
		unset($statement);
		return $result;
	}
	
	/**
	 * Override the default query method in order to log the query statement
	 * 
	 * @see \PDO::query()
	 * @param string $statement
	 * @return \PDOStatement|false
	 */
	public function query($statement) {
		$startTime = microtime(true);
		$result = parent::query($statement);
		$totalTime = microtime(true) - $startTime;
		
		$this->logs[] = [
			"sql" => $statement,
			"totalTime" => $totalTime
		];
		
		return $result;
	}
	
	/**
	 * Override the default exec method in order to log the query statement
	 * 
	 * @see \PDO::exec()
	 * @param string $statement
	 * @return int
	 */
	public function exec($statement) 
	{
		$startTime = microtime(true);
		$result = parent::exec($statement);
		$totalTime = microtime(true) - $startTime;
		
		$this->logs[] = [
			"sql" => $statement,
			"totalTime" => $totalTime
		];
		
		return $result;
	}
	
	/**
	 * Override the default prepare method in order to log the statement
	 * 
	 * @param string $statement
	 * @param array $options
	 * @return \PDOStatement
	 */
	public function prepare($statement, $options = null) {
		if ($options === null) {
			$options = [];
		}
		
		$this->logs[] = [
			"sql" => $statement,
			"options" => $options
		];
		
		return parent::prepare($statement, $options);
	}
	
	/**
	 * Create a PDOStatement from a SQL string, execute it, log it and return it
	 * 
	 * @param string $sql
	 * @param array $params
	 * @return \PDOStatement
	 */
	private function _exec($sql, array $params = [])
	{
		if ($sql instanceof Query\Query) {
			$sql = $sql->sql($this);
		}
		
		$startTime = microtime(true);
		$statement = $this->prepare($sql);
		$statement->execute($params);
		$totalTime = microtime(true) - $startTime;
		
		$this->logs[] = [
			"sql" => $sql,
			"params" => $params,
			"totalTime" => $totalTime
		];
		
		return $statement;
	}
}