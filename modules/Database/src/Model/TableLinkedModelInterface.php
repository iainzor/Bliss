<?php
namespace Database\Model;

use Database\Table\WritableTableInterface;

interface TableLinkedModelInterface
{
	public function setTable(WritableTableInterface $table);
	
	public function save() : bool;
}