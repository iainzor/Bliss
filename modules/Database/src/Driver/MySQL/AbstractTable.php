<?php
namespace Database\Driver\MySQL;

use Database\Table,
	Database\Model\AbstractModel,
	Database\Model\TableLinkedModelInterface,
	Database\Query\QueryParams;

abstract class AbstractTable extends Table\AbstractTable implements Table\ReadableTableInterface, Table\WritableTableInterface, Table\ModelProviderInterface
{
	/**
	 * Insert a record into the table and return the last inserted ID
	 * 
	 * @param array $data
	 * @param array $updateOnDuplicate
	 * @return mixed Returns the last insert ID
	 */
	public function insert(array $data, array $updateOnDuplicate = null)
	{
		$update = null;
		if (!empty($updateOnDuplicate)) {
			$pairs = [];
			foreach ($updateOnDuplicate as $key => $value) {
				if (is_numeric($key)) {
					$pairs[] = "`{$value}` = VALUES(`{$value}`)";
				} else { 
					$pairs[] = "`{$key}` = ". $this->db->quote($value);
				}
			}
			
			if (!empty($pairs)) {
				$update = "ON DUPLICATE KEY UPDATE ". implode(",", $pairs);
			}
		}
		
		$columnNames = array_keys($data);
		$columnValues = array_map(function($value) {
			if ($value === null) {
				return "NULL";
			} else {
				return $this->db->quote($value);
			}
		}, array_values($data));
		
		$statement = $this->db->prepare("
			INSERT INTO `". $this->getName() ."`
				(`". implode("`,`", $columnNames) ."`)
			VALUES
				(". implode(",", $columnValues) .")
			{$update}
		");
		$statement->execute();
		
		return $this->db->lastInsertId();
	}
	
	/**
	 * Update the data in the table matching $params
	 * 
	 * @param array $data
	 * @param array $params
	 * @return int Number of rows affected
	 */
	public function update(array $data, array $params = []) : int
	{
		$pairs = [];
		foreach ($data as $name => $value) {
			$pairs[] = "`{$name}` = ". $this->db->quote($value);
		}
		
		$whereClause = $this->_generateWhereClause($params);
		
		return $this->db->exec("
			UPDATE	`". $this->getName() ."`
			SET		". implode(", ", $pairs) ."
			{$whereClause}
		");
	}
	
	/**
	 * Attempt to fetch a single row from the table
	 * 
	 * @param QueryParams $params
	 * @return AbstractModel|boolean
	 */
	public function fetch(QueryParams $params) 
	{
		$params->maxResults = 1;
		$all = $this->fetchAll($params);
		
		if (count($all)) {
			return array_shift($all);
		}
		return false;
	}
	
	/**
	 * Fetch all records matching the parameters passed
	 * 
	 * @param QueryParams $params
	 * @return AbstractModel[]
	 */
	public function fetchAll(QueryParams $params) : array 
	{
		$whereClause = $this->_generateWhereClause($params->conditions);
		$orderClause = $this->_generateOrderClause($params->orderings);
		$limitClause = $this->_generateLimitClause($params->maxResults, $params->resultOffset);
		$statement = $this->db->prepare("
			SELECT	*
			FROM	`". $this->getName() ."`
			{$whereClause}
			{$orderClause}
			{$limitClause}
		");
		$statement->execute();
		
		$results = $statement->fetchAll(\PDO::FETCH_CLASS, $this->getModelClass());
		foreach ($results as $result) {
			if ($result instanceof TableLinkedModelInterface) {
				$result->setTable($this);
			}
		}
		return $results;
	}

	/**
	 * Fetch a single column value from the table
	 * 
	 * @param string $columnName
	 * @param QueryParams $params
	 * @return mixed
	 */
	public function fetchColumn(string $columnName, QueryParams $params) 
	{
		$row = $this->fetch($params);
		
		if ($row && property_exists($row, $columnName)) {
			return $row->{$columnName};
		}
		
		return false;
	}

	/**
	 * Generate a WHERE clause based on the conditions provided
	 * 
	 * @param array $conditions
	 * @return string
	 */
	private function _generateWhereClause(array $conditions) : string
	{
		if (empty($conditions)) {
			return "";
		}
		
		$pairs = [];
		foreach ($conditions as $key => $value) {
			$pairs[] = "`{$key}` = ". $this->db->quote($value);
		}
		
		return "WHERE ". implode(" AND ", $pairs);
	}
	
	/**
	 * Generate an ORDER BY clause for a query
	 * 
	 * @param array $orderings
	 * @return string
	 */
	private function _generateOrderClause(array $orderings) : string
	{
		if (empty($orderings)) {
			return "";
		}
		
		$pairs = [];
		foreach ($orderings as $key => $value) {
			if (is_numeric($key)) {
				$pairs[] = $value;
			} else {
				$pairs[] = "`{$key}` {$value}";
			}
		}
		
		return "ORDER BY ". implode(",", $pairs);
	}
	
	/**
	 * Generate a LIMIT clause for a query
	 * 
	 * @param int $maxResults
	 * @param int $resultOffset
	 * @return string
	 */
	private function _generateLimitClause(int $maxResults, int $resultOffset) : string
	{
		if (!$maxResults < 1) {
			return "";
		}
		
		return "LIMIT {$maxResults} OFFSET {$resultOffset}";
	}
}
