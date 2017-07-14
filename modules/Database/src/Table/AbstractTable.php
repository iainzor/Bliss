<?php
namespace Database\Table;

use Database\PDO;

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
}