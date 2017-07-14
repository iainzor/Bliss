<?php
namespace Database\Table;

interface WritableTableInterface
{
	public function insert(array $data, array $updateOnDuplicate = null);
	
	public function update(array $data, array $params = []);
}