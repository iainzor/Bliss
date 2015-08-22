<?php
namespace User\Db;

use Database\Table\AbstractTable,
	User\User;

class UsersTable extends AbstractTable
{
	const NAME = "users";
	
	public function defaultName() { return self::NAME; }
	
	/**
	 * Check if an email address exists in the database
	 * 
	 * @param string $email
	 * @param User $excludeUser Optional user instance to ignore from the query
	 * @return boolean
	 */
	public function emailExists($email, User $excludeUser = null)
	{
		$query = $this->select();
		$query->where("`email` = :email");
		
		if ($excludeUser) {
			$query->where("`id` != :id");
		}
		
		$row = $query->fetchRow([
			":email" => $email,
			":id" => isset($excludeUser) ? $excludeUser->id() : null
		]);
		
		return !empty($row);
	}
}