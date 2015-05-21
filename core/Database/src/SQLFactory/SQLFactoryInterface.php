<?php
namespace Database\SQLFactory;

use Database\Query\Query;

interface SQLFactoryInterface
{
	public function generateSelectSQL(Query $query);
	
	public function generateUpdateSQL(Query $query);
	
	public function generateInsertSQL(Query $query);
	
	public function generateDeleteSQL(Query $query);
	
	public function generateJoinClause($tableName, $expr, $type = Query::JOIN_DEFAULT);
	
	public function generateWhereClause(array $exprs);
	
	public function generateOrderClause(array $exprs);
}