<?php
namespace Database\Query;

use Database\Traits;

class SelectQuery
{
	use Traits\ColumnListTrait,
		Traits\TableTrait;
	
	/**
	 * Constructor
	 * 
	 * @param array $columns The initial columns for the select query
	 */
	public function __construct(array $columns = null)
	{
		$this->columns($columns);
	}
}