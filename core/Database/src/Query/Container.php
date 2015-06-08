<?php
namespace Database\Query;

class Container
{
	/**
	 * @var Query[] 
	 */
	private $queries = [];
	
	/**
	 * Get the SelectQuery
	 * 
	 * @param array $fields
	 * @return SelectQuery
	 */
	public function select(array $fields = [])
	{
		/* @var $query SelectQuery */
		$query = new SelectQuery();
		$query->fields($fields);
		$this->queries[] = $query;
		
		return $query;
	}
}