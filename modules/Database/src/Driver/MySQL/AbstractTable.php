<?php
namespace Database\Driver\MySQL;

use Database\Table\TableInterface,
	Database\PDO;

abstract class AbstractTable implements TableInterface
{
	/**
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}
	
	/**
	 * Insert a record into the table and return the last inserted ID
	 * 
	 * @param array $data
	 * @return mixed Returns the last insert ID
	 */
	public function insert(array $data)
	{
		$columnNames = array_keys($data);
		$columnValues = array_values($data);
		$statement = $this->db->prepare("
			INSERT INTO `". $this->getName() ."`
				(`". implode("`,`", $columnNames) ."`)
			VALUES
				(". implode(",", array_map([$this->db, "quote"], $columnValues)) .")
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
	public function update(array $data, array $params = [])
	{
		$pairs = [];
		foreach ($data as $name => $value) {
			$pairs[] = "`{$name}` = ". $this->db->quote($value);
		}
		
		$where = ["1"];
		foreach ($params as $name => $value) {
			$where[] = "`{$name}` = ". $this->db->quote($value);
		}
		
		return $this->db->exec("
			UPDATE	`". $this->getName() ."`
			SET		". implode(", ", $pairs) ."
			WHERE	". implode(" AND ", $where) ."
		");
	}
}
