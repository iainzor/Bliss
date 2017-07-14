<?php
namespace Database\Model;

use Database\Table\TableInterface;

interface TableLinkedModelInterface
{
	public function setTable(TableInterface $table);
	
	public function save();
}