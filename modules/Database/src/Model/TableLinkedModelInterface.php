<?php
namespace Database\Model;

use Database\Table\WritableTableInterface;

interface TableLinkedModelInterface
{
	public function getPrimaryKeys() : array;
	
	public function setTable(WritableTableInterface $table);
	
	public function getTable() : WritableTableInterface;
	
	public function save() : bool;
}