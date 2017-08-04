<?php
namespace Database\Table;

use Database\Query\QueryParams;

interface ReadableTableInterface extends TableInterface
{
	public function fetch(QueryParams $params);
	
	public function fetchAll(QueryParams $params) : array;
	
	public function fetchColumn(string $columnName, QueryParams $params);
}