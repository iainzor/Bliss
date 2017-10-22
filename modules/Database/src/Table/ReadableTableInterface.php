<?php
namespace Database\Table;

use Database\Query\QueryParams;

interface ReadableTableInterface extends TableInterface
{
	public function fetch(QueryParams $params, array $inputParams = []);
	
	public function fetchAll(QueryParams $params, array $inputParams = []) : array;
	
	public function fetchColumn(string $columnName, QueryParams $params, array $inputParams = []);
}