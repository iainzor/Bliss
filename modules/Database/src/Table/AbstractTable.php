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
	
	/**
	 * Get the table's PDO instance
	 * 
	 * @return PDO
	 */
	public function getDb() : PDO 
	{
		return $this->db;
	}
	
	public function prepareRows(array $rows) : array
	{
		$metadata = new Metadata($this);
		
		if ($this instanceof MetadataProviderInterface) {
			$this->populateMetadata($metadata);
		}
		
		return array_map(function($row) use ($metadata) {
			return $metadata->prepareRow($row);
		}, $rows);
	}
}