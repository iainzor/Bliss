<?php
namespace Database\SQLFactory;

use Database\Query;

class MySQLFactory implements SQLFactoryInterface
{
	public function generateDeleteSQL(Query $query) {
		
	}

	public function generateInsertSQL(Query $query) {
		
	}

	public function generateSelectSQL(Query $query) 
	{
		$whereClause = $this->_generateWhereClause($query);
		$parts = [
			"SELECT * FROM {$query->tableName()}",
			$whereClause
		];
			
		return trim(implode(" ", $parts));
	}

	public function generateUpdateSQL(Query $query) {
		
	}
	
	private function _generateWhereClause(Query $query)
	{
		$params = $query->params();
		$clause = null;
		if (count($params["where"])) {
			$parts = [];
			foreach ($params["where"] as $name => $value) {
				$parts[] = "{$name} = ". $value;
			}
			
			
			$clause = "WHERE ". implode(" AND ", $parts);
		}
		
		return $clause;
	}

}