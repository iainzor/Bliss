<?php
namespace User;

use Database\Table\AbstractTable;

class DbTable extends AbstractTable
{
	const NAME = "users";
	
	public function defaultName() { return self::NAME; }
	
	/**
	 * Check if an email address exists in the database
	 * 
	 * @param string $email
	 * @return boolean
	 */
	public function emailExists($email)
	{
		$row = $this->find("`email` = :email", [
			":email" => $email
		]);
		
		return !empty($row);
	}
}