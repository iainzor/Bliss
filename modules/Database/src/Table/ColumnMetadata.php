<?php
namespace Database\Table;

class ColumnMetadata
{
	use \Common\PopulatePropertiesTrait;
	
	public $columnName;
	public $mapTo;
	public $valueParser;
	
	/**
	 * Constructor
	 * 
	 * @param string $columnName
	 * @param array $properties
	 */
	public function __construct(string $columnName, array $properties = [])
	{
		$this->columnName = $columnName;
		$this->populateProperties($properties);
	}
}
