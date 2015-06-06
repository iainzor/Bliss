<?php
namespace Database\Query;

use Database\PDO;

class SelectQuery extends Query
{
	/**
	 * @var array
	 */
	protected $fields = [];
	
	/**
	 * Set the table to select from
	 * 
	 * @param string $tableName
	 * @return SelectQuery
	 */
	public function from($tableName)
	{
		$this->tableName($tableName);
		
		return $this;
	}
	
	/**
	 * Get or set the fields to select
	 * 
	 * @param array $fields
	 * @param boolean $merge
	 * @return array
	 */
	public function fields(array $fields = null, $merge = true)
	{
		if ($fields !== null) {
			foreach ($fields as $i => $field) {
				if (!preg_match("/^`?([a-z0-9-_]+)`?\./i", $field)) {
					$field = $this->tableName() .".{$field}";
				}
				$fields[$i] = $this->quoteField($field);
			}
			
			if ($merge === true) {
				$this->fields = array_merge($this->fields, $fields);
			} else {
				$this->fields = $fields;
			}
		}
		
		return $this->fields;
	}
	
	/**
	 * Add a JOIN expression to the query
	 * 
	 * @param string $tableName
	 * @param string|Expr $expr
	 * @param array $fields Fields to select from the joined table
	 * @param string $type
	 * @return SelectQuery
	 */
	public function join($tableName, $expr, array $fields = [], $type = self::JOIN_DEFAULT)
	{
		$tableName = $this->quoteField($tableName);
		$sql = self::sqlFactory()->generateJoinClause($tableName, $expr, $type);
		
		$this->addPart(self::PART_JOIN, $sql);
		$this->fields($fields);
		
		return $this;
	}
	
	/**
	 * Add a LEFT JOIN to the query
	 * 
	 * @param string $tableName
	 * @param string|Expr $expr
	 * @param array $fields
	 * @return SelectQuery
	 */
	public function leftJoin($tableName, $expr, array $fields = [])
	{
		$this->join($tableName, $expr, $fields, self::JOIN_LEFT);
		
		return $this;
	}
	
	/**
	 * @param PDO $db
	 * @return string
	 */
	public function sql(PDO $db) 
	{
		$fields = $this->fields();
		if (!count($fields)) {
			$fields = ["*"];
		}
		
		$fieldList = implode(", ", $fields);
		$this->addPart(self::PART_SQL_START, "SELECT {$fieldList} FROM {$this->tableName()}");
		
		return parent::sql($db);
	}
}