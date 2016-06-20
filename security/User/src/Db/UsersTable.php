<?php
namespace User\Db;

use Database\Table\AbstractTable,
	Database\Model,
	User\User,
	User\BasicUser;

class UsersTable extends AbstractTable implements Model\ModelGeneratorInterface
{
	use Model\ModelGeneratorTrait;
	
	const NAME = "users";
	
	/**
	 * @var boolean
	 */
	private $hideSensitiveData = false;
	
	/**
	 * @return string
	 */
	public function defaultName() { return self::NAME; }
	
	/**
	 * @return User
	 */
	public function createModelInstance() 
	{ 
		return $this->hideSensitiveData ? new BasicUser() : new User(); 
	}
	
	/**
	 * Get or set whether sensitive data should be hidden when retrieving users
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function hideSensitiveData($flag = null)
	{
		if ($flag !== null) {
			$this->hideSensitiveData = (boolean) $flag;
		}
		return $this->hideSensitiveData;
	}
	
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
		$params = [":email" => $email];
		$query->where("`email` = :email");
		
		if ($excludeUser) {
			$query->where("`id` != :id");
			$params[":id"] = $excludeUser->id();
		}
		
		$row = $query->fetchRow($params);
		
		return !empty($row);
	}
	
	/**
	 * Check if a username exists
	 * 
	 * @param string $username
	 * @param User $excludeUser Optional user instance to ignore
	 * @return boolean
	 */
	public function usernameExists($username, User $excludeUser = null)
	{
		$query = $this->select();
		$params = [":username" => $username];
		$query->where("`username` = :username");
		
		if ($excludeUser) {
			$query->where("`id` != :id");
			$params[":id"] = $excludeUser->id();
		}
		
		$row = $query->fetchRow($params);
		
		return !empty($row);
	}
}