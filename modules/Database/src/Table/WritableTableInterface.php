<?php
namespace Database\Table;

use Database\Query\QueryParams;

interface WritableTableInterface extends TableInterface
{
	/**
	 * @param array $data
	 * @param array $updateOnDuplicate
	 * @return mixed The last inserted ID
	 */
	public function insert(array $data, array $updateOnDuplicate = null);
	
	/**
	 * @param array $data
	 * @param array $params
	 * @return int Number of rows affected by the update
	 */
	public function update(array $data, array $params = []) : int;
	
	/**
	 * @param QueryParams $queryParams
	 * @return int
	 */
	public function delete(QueryParams $queryParams) : int;
}