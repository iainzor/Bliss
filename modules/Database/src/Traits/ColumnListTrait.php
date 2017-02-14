<?php
namespace Database\Traits;

use Database\Column;

trait ColumnListTrait
{
	/**
	 * @var Column\ColumnInterface[]
	 */
	private $columns = [];
	
	/**
	 * Get all of the columns in the list.  If $columns is not null, the list
	 * of columns will be cleared and the items in the array will be added to the list.
	 * 
	 * @param array $columns
	 * @return array
	 */
	public function columns(array $columns = null) : array
	{
		if ($columns !== null) {
			$this->columns = [];
			foreach ($columns as $key => $value) {
				$instance = $this->_createColumnInstance($key, $value);
				$this->addColumn($instance);
			}
		}
		return $this->columns;
	}
	
	/**
	 * Add a column to the list
	 * 
	 * @param \Database\Column\ColumnInterface $column
	 */
	public function addColumn(Column\ColumnInterface $column)
	{
		$this->columns[] = $column;
	}
	
	/**
	 * @parma mixed $key
	 * @param mixed $data
	 * @return \Database\Column\ColumnInterface
	 * @throws \Exception
	 */
	private function _createColumnInstance($key, $value) : Column\ColumnInterface
	{
		$instance = null;
		
		if (is_string($key) && is_string($value)) {
			$instance = new Column\AliasedColumn($value, new Column\NamedColumn($key));
		} else if (is_string($value)) {
			$instance = new Column\ColumnExpr($value);
		} else if ($value instanceof Column\ColumnInterface) {
			$instance = $value;
		}
		
		if (!($instance instanceof Column\ColumnInterface)) {
			throw new \Exception("Column must be an instance of ". Column\ColumnInterface::class);
		}
		
		return $instance;
	}
}
