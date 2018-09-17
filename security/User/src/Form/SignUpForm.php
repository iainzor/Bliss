<?php
namespace User\Form;

use User\DbTable as UserDbTable,
	User\User;

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
	 * @param \User\DbTable $userDbTable
	 */
	public function __construct(UserDbTable $userDbTable)
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
	 * @return \User\User|boolean
	 */
	public function create(array $data)
	{
		if (!$this->isValid($data)) {
			return false;
		}
		
		$user = new User();
		$user->email($data["email"]);
		$user->displayName($data["displayName"]);
		$user->isActive(true);
		
		$insertData = $user->toBasicArray();
		$insertData["password"] = User::passwordHasher()->hash($data["password"]);
		$id = $this->userDbTable->insert($insertData);
		
		$user->id($id);
		
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
		
		$fields = ["email", "displayName", "password", "passwordConfirm"];
		
		foreach ($fields as $name) {
			if (empty($data[$name])) {
				$this->errors[] = "Field '{$name}' is required";
			}
		}
		
		if (count($this->errors)) {
			return false;
		}
		
		if (!preg_match("/.+@.+\..+/i", $data["email"])) {
			$this->errors[] = "Email address is invalid";
		} else if ($this->userDbTable->emailExists($data["email"])) {
			$this->errors[] = "Email address is already registered";
		}
		
		if ($data["password"] !== $data["passwordConfirm"]) {
			$this->errors[] = "Password confirmation does not match";
		}
		
		return !count($this->errors);
	}
}