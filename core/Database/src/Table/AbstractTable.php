<?php
namespace Database\Table;

use Database\PDO;

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
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
		$this->name = $this->defaultName();
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
	 * @return string
	 */
	abstract public function defaultName();
	
	/**
	 * Find a single record from the table
	 * 
	 * @param string $where
	 * @param array $params
	 * @return array|null
	 */
	public function find($where = null, array $params = [])
	{
		if ($where !== null) {
			$where = "WHERE {$where}";
		}
		
		return $this->db->fetchRow("SELECT * FROM `". $this->name() ."` {$where}", $params);
	}
	
	/**
	 * Insert a record into the database table
	 * 
	 * @param array $data
	 * @return int The ID of the inserted data
	 */
	public function insert(array $data)
	{
		$fields = [];
		$values = [];
		foreach ($data as $field => $value) {
			$fields[] = "`". $field ."`";
			$values[] = $this->db->quote($value);
		}
		
		$fieldList = "(". implode(",", $fields) .")";
		$valueList = "(". implode(",", $values) .")";
		$statement = $this->db->prepare("INSERT INTO `". $this->name() ."` {$fieldList} VALUES {$valueList}");
		$statement->execute();
		
		return $this->db->lastInsertId();
	}
}