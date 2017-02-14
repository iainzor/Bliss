<?php
namespace Database\Traits;

use Database\Table;

trait TableTrait
{
	/**
	 * @var \Database\Table\TableInterface
	 */
	private $table;
	
	/**
	 * Get or set the table 
	 * 
	 * @param mixed $table
	 * @return \Database\Table\TableInterface
	 * @throws \Exception
	 */
	public function table($table = null) : Table\TableInterface
	{
		if ($table !== null) {
			$instance = $this->_createTableInstance($table);
			$this->table = $instance; 
		}
		if (!$this->table) {
			throw new \Exception("No table has been set");
		}
		return $this->table;
	}
	
	/**
	 * Alias for table() method
	 * 
	 * @param mixed $table
	 * @return \Database\Table\TableInterface
	 */
	public function from($table = null) : Table\TableInterface
	{
		return $this->table($table);
	}
	
	/**
	 * @param \Database\Table\TableInterface $table
	 * @return \Database\Table\TableInterface
	 * @throws \UnexpectedValueException
	 */
	private function _createTableInstance($table) : Table\TableInterface
	{
		if (is_string($table)) {
			return new Table\NamedTable($table);
		}
		if (!$table instanceof Table\TableInterface) {
			throw new \UnexpectedValueException("Expecting and an instance of ". Table\TableInterface::class);
		}
	}
}