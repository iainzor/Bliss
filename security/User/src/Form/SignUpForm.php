<?php
namespace User\Form;

use User\Db\UsersTable,
	User\User,
	Database\Query\InsertQuery;

class SignUpForm
{
	/**
	 * @var \User\DbTable
	 */
	private $userDbTable;
	
	/**
	 * @var array
	 */
	private $errors = [];
	
	/**
	 * Constructor
	 * 
	 * @param UsersTable $userDbTable
	 */
	public function __construct(UsersTable $userDbTable)
	{
		$this->userDbTable = $userDbTable;
	}
	
	/**
	 * Get any available errors from the form
	 * 
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}
	
	/**
	 * Attempt to create a new user
	 * Returns a User instance on success and FALSE on failure
	 * 
	 * @param array $data
	 * @return \User\User
	 */
	public function create(array $data)
	{
		if (!$this->isValid($data)) {
			return false;
		}
		
		$user = User::factory([
			"email" => $data["email"],
			"username" => $data["username"],
			"displayName" => $data["username"],
			"password" => User::passwordHasher()->hash($data["password"]),
			"isActive" => true
		]);
		$user->preservePassword(true);
		
		try {
			$this->userDbTable->insert($user);
		} catch (\PDOException $e) {
			throw new \Exception("Could not create user", 500, $e);
		}
		
			
		$user->preservePassword(false);
		
		return $user;
	}
	
	/**
	 * Check if form data is valid
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function isValid(array $data)
	{
		$this->errors = [];
		
		array_walk($data, "trim");
		
		$fields = ["email", "username", "password"];
		
		foreach ($fields as $name) {
			if (empty($data[$name])) {
				$this->errors[$name] = "Field is required";
			}
		}
		
		if (count($this->errors)) {
			return false;
		}
		
		if (!preg_match("/.+@.+\..+/i", $data["email"])) {
			$this->errors["email"] = "Email address is invalid";
		} else if ($this->userDbTable->emailExists($data["email"])) {
			$this->errors["email"] = "Email address has already been registered";
		}
		
		if ($this->userDbTable->usernameExists($data["username"])) {
			$this->errors["username"] = "Username has already been registered";
		}
		
		return !count($this->errors);
	}
}