<?php
namespace Database\Table;

class Definition
{
	/**
	 * @var ColumnDefinition[]
	 */
	private $columns = [];
	
	/**
	 * @var array
	 */
	protected $indexes = [];
	
	/**
	 * @var array
	 */
	protected $foreignKeys = [];
	
	/**
	 * @var array
	 */
	protected $partitions = [];
	
	/**
	 * Get or set the column configuration
	 * 
	 * @param array $columns
	 * @return ColumnDefinition[]
	 */
	public function columns(array $columns = null)
	{
		if ($columns !== null) {
			foreach ($columns as $name => $def) {
				$columns[$name] = new ColumnDefinition($name, $def);
			}
			$this->columns = $columns;
		}
		return $this->columns;
	}
	
	/**
	 * Parse a row returned from the database
	 * 
	 * @param array $row
	 * @return array
	 */
	public function parseRow(array $row)
	{
		$parsed = [];
		foreach ($row as $name => $value) {
			if (isset($this->columns[$name])) {
				$column = $this->columns[$name];
				
				if ($column->isVisible()) {
					$parsed[$column->displayName()] = $column->parseValue($value);
				}
			} else {
				$parsed[$name] = $value;
			}
		}
		return $parsed;
	}
}