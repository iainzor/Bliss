<?php
namespace Database\SQLFactory;

use Database\Query;

interface SQLFactoryInterface
{
	public function generateSelectSQL(Query $query);
	
	public function generateUpdateSQL(Query $query);
	
	public function generateInsertSQL(Query $query);
	
	public function generateDeleteSQL(Query $query);
}