<?php
namespace User\Form;

use User\Db\UsersTable,
	User\User;

class UserForm
{
	/**
	 * @var \User\Db\UsersTable
	 */
	private $usersTable;
	
	/**
	 * @var \User\User
	 */
	private $user;
	
	/**
	 * @var array
	 */
	private $errors = [];
	
	/**
	 * Constructor
	 * 
	 * @param UsersTable $usersTable
	 * @param User $user Optional instance of a user being updated
	 */
	public function __construct(UsersTable $usersTable, User $user = null)
	{
		$this->usersTable = $usersTable;
		$this->user = $user;
	}
	
	/**
	 * Add an error to the form
	 * 
	 * @param string $error
	 */
	public function error($error)
	{
		$this->errors[] = $error;
	}
	
	/**
	 * Get all errors from the form
	 * 
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}
	
	/**
	 * Check if the form contains any errors
	 * 
	 * @return boolean
	 */
	public function hasErrors()
	{
		return count($this->errors) > 0;
	}
	
	/**
	 * Check if the data provided is valid
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function isValid(array $data = null)
	{
		array_walk($data, "trim");
		
		$this->errors = [];
		
		if (empty($data)) {
			$this->error("No data was provided");
		}
		
		if (empty($data["displayName"])) {
			$this->error("Display name cannot be empty");
		}
		
		if (!preg_match("/.+@.+\..+/i", $data["email"])) {
			$this->error("Email address is invalid");
		} else if ($this->usersTable->emailExists($data["email"], $this->user)) {
			$this->error("Email address is already registered");
		}
		
		if (!$this->user || (isset($data["changePassword"]) && $data["changePassword"])) {
			if (empty($data["password"])) {
				$this->error("Please provide a password");
			} else if ($data["password"] !== $data["passwordConfirm"]) {
				$this->error("Password confirmation does not match");
			}
		}
		
		return !$this->hasErrors();
	}
	
	/**
	 * Save the user data
	 * 
	 * @param array $data
	 * @return \User\User
	 */
	public function save(array $data)
	{
		unset($data["passwordConfirm"]);
		
		$user = User::factory($data);
		$user->updated(time());
		
		if (!$this->user || isset($data["password"])) {
			$user->password(User::passwordHasher()->hash($data["password"]));
		} else if ($this->user) {
			$user->password($this->user->password());
		}
		
		if ($user->id()) {
			$this->update($user);
		} else {
			$this->insert($user);
		}
		
		return $user;
	}
	
	/**
	 * Update a user's database record
	 * 
	 * @param User $user
	 */
	private function update(User $user)
	{
		$query = $this->usersTable->update();
		$query->values([
			"email" => $user->email(),
			"password" => $user->password(),
			"displayName" => $user->displayName(),
			"updated" => $user->updated()
		]);
		$query->where([
			"id" => $user->id()
		]);
		$query->execute();
	}
	
	/**
	 * Insert a new user and set its ID 
	 * 
	 * @param User $user
	 */
	private function insert(User $user)
	{
		$user->created(time());
		
		$id = $this->usersTable->insert([
			"email" => $user->email(),
			"password" => $user->password(),
			"displayName" => $user->displayName(),
			"created" => $user->created(),
			"updated" => $user->updated(),
			"isActive" => $user->isActive()
		]);
		$user->id($id);
	}
}