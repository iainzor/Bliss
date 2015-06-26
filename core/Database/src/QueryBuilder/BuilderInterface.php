<?php
namespace Database\QueryBuilder;

use Database\Table\AbstractTable;

interface BuilderInterface
{
	public function buildSelectQuery(AbstractTable $table, array $params = []);
}