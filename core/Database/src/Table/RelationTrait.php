<?php
namespace Database\Table;

trait RelationTrait
{
	private $relations = [];
	
	private function addRelation($type, $name, AbstractTable $table, $localKeys, $foreignKeys)
	{
		$this->relations[$name] = [
			"type" => $type,
			"table" => $table,
			"localKeys" => is_array($localKeys) ? $localKeys : [$localKeys],
			"foreignKeys" => is_array($foreignKeys) ? $foreignKeys : [$foreignKeys]
		];
	}
	
	public function hasOne($name, AbstractTable $table, $localKeys, $foreignKeys)
	{
		$this->addRelation("one", $name, $table, $localKeys, $foreignKeys);
	}
	
	public function hasMany($name, AbstractTable $table, $localKeys, $foreignKeys)
	{
		$this->addRelation("many", $name, $table, $localKeys, $foreignKeys);
	}
}