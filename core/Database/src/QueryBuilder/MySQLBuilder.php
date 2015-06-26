<?php
namespace Database\QueryBuilder;

use Database\Table\AbstractTable,
	Database\Query;

class MySQLBuilder implements BuilderInterface
{
	public function buildSelectQuery(AbstractTable $table, array $params = array()) 
	{
		$query = new Query\SelectQuery();
		$query->from($table->qualifiedName());
		
		foreach ($params as $name => $value) {
			$query->where("{$name} = :{$name}")->params([
				":{$name}" => $value
			]);
		}
		
		foreach ($table->joins() as $join) {
			$subTable = $join["table"];
			$type = $join["type"];
			$localKeys = $join["localKeys"];
			$foreignKeys = $join["foreignKeys"];
			$fields = $join["fields"];
			$exprs = [];
			
			foreach ($localKeys as $i => $localKey) {
				if (isset($foreignKeys[$i])) {
					$exprs[] = $table->qualifiedName() .".". $localKey ." = ". $subTable->qualifiedName() .".". $foreignKeys[$i];
				}
			}
			
			if (count($exprs)) {
				$query->join($subTable->qualifiedName(), "ON ". implode(" AND ", $exprs), $fields, $type);
			}
		}
		
		return $query;
	}
}