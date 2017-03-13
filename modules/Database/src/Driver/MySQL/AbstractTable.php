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
	 * @return mixed
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
}
