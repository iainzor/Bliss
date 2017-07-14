<?php
namespace Database\Table;

use Database\Model\AbstractModel,
	Database\Query\QueryParams;

interface ReadableTableInterface
{
	public function fetch(QueryParams $params);
	
	public function fetchAll(QueryParams $params) : array;
	
	public function fetchColumn(string $columnName, QueryParams $params);
}