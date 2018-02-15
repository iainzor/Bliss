<?php
namespace Database\Table;

interface TableInterface
{
	public function getName() : string;
	
	public function getPrimaryKeys() : array;
	
	public function prepareRows(array $rows) : array;
}