<?php
namespace Database\Table;

use Database\Query\Query,
	Database\Query\SelectQuery,
	Database\Expr\JoinExpr;

trait JoinableTrait
{
	private $joins = [];
	
	private function addJoin(AbstractTable $table, $localKeys, $foreignKeys, array $fields = [], $joinType = Query::JOIN_DEFAULT)
	{
		$this->joins[] = [
			"table" => $table,
			"type" => $joinType,
			"localKeys" => is_array($localKeys) ? $localKeys : [$localKeys],
			"foreignKeys" => is_array($foreignKeys) ? $foreignKeys : [$foreignKeys],
			"fields" => $fields
		];
	}
	
	public function join(AbstractTable $table, $localKeys, $foreignKeys, array $fields = [])
	{
		$this->addJoin($table, $localKeys, $foreignKeys, $fields, Query::JOIN_DEFAULT);
	}
	
	public function leftJoin(AbstractTable $table, $localKeys, $foreignKeys, array $fields = [])
	{
		$this->addJoin($table, $localKeys, $foreignKeys, $fields, Query::JOIN_LEFT);
	}
	
	/**
	 * @var array
	 */
	public function joins()
	{
		return $this->joins;
	}
}