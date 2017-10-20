<?php
namespace Database\Table;

class Metadata
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * @var ColumnMetadata[]
	 */
	private $columns = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Database\Table\AbstractTable $table
	 */
	public function __construct(AbstractTable $table)
	{
		$this->table = $table;
	}
	
	/**
	 * Set the table's column metadata
	 * 
	 * @param array $columns
	 */
	public function setColumns(array $columns)
	{
		$this->columns = [];
		
		foreach ($columns as $name => $data) {
			if (is_numeric($name)) {
				throw new \UnexpectedValueException("setColumns() expects each key in the provided array to be the name of the column");
			}
			$this->columns[$name] = new ColumnMetadata($name, $data);
		}
	}
	
	/**
	 * Get a column's metadata.  If the column has not been provided, a new 
	 * instance of the column metadata will be created.
	 * 
	 * @param string $columnName
	 * @return \Database\Table\ColumnMetadata
	 */
	public function getColumn(string $columnName) : ColumnMetadata
	{
		if (!isset($this->columns[$columnName])) {
			$this->columns[$columnName] = new ColumnMetadata($columnName);
		}
		
		return $this->columns[$columnName];
	}
	
	/**
	 * Prepare a database row according to the metadata provided to this class
	 * 
	 * @param array $row
	 * @return mixed
	 */
	public function prepareRow(array $row)
	{
		$prepared = [];
		foreach ($row as $key => $value) {
			$column = $this->getColumn($key);
			$mapTo = empty($column->mapTo) ? $key : $column->mapTo;
			$value = empty($column->valueParser) ? $value : call_user_func($column->valueParser, $value);
			
			$prepared[$mapTo] = $value;
		}
		
		if ($this->table instanceof ModelProviderInterface) {
			$modelClass = $this->table->getModelClass();
			$ref = new \ReflectionClass($modelClass);
			$instance = $ref->newInstanceWithoutConstructor();
			
			foreach ($prepared as $key => $value) {
				if ($ref->hasProperty($key)) {
					$instance->{$key} = $value;
				}
			}
			
			if ($ref->hasMethod("__construct")) {
				$instance->__construct();
			}
			
			return $instance;
		}
		
		return $prepared;
	}
}