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
