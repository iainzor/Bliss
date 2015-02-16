<?php
namespace User\Session;

use User\DbTable as UserDbTable,
	User\User,
	User\Hasher\HasherInterface,
	User\Hasher\Blowfish;

class Manager
{
	/**
	 * @var \User\Session\DbTable
	 */
	private $sessionDbTable;
	
	/**
	 * @var \User\DbTable
	 */
	private $userDbTable;
	
	/**
	 * @var HasherInterface
	 */
	private $hasher;
	
	/**
	 * Constructor
	 * 
	 * @param \User\Session\DbTable $sessionDbTable
	 * @param \User\DbTable $userDbTable
	 * @param HasherInterface
	 */
	public function __construct(DbTable $sessionDbTable, UserDbTable $userDbTable, HasherInterface $hasher = null)
	{
		if ($hasher === null) {
			$hasher = User::passwordHasher();
		}
		
		$this->sessionDbTable = $sessionDbTable;
		$this->userDbTable = $userDbTable;
		$this->hasher = $hasher;
	}
	
	/**
	 * Check if credentials are valid
	 * 
	 * @param string $email
	 * @param string $password
	 * @return boolean
	 */
	public function isValid($email, $password)
	{
		$session = $this->createSession($email, $password);
		
		return $session->isValid();
	}
	
	/**
	 * Create a new session using the credentials provided
	 * 
	 * @param string $email
	 * @param string $password
	 * @return \User\Session\Session
	 */
	public function createSession($email, $password)
	{
		$session = new Session();
		$userRow = $this->userDbTable->find("`email`=:email", [
			":email" => $email
		]);
		
		if (!empty($userRow)) {
			$user = User::populate(new User(), $userRow);
			
			if ($this->hasher->matches($password, $user->password())) {
				$session->user($user);
				$session->isValid(true);
			}
		}
		
		return $session;
	}
	
	/**
	 * Attempt to attach a user session instance
	 * 
	 * @param \User\Session\Session $session
	 * @return \User\Session\Session
	 */
	public function attachUser(Session $session)
	{
		$row = $this->sessionDbTable->find("`id` = :id", [
			":id" => $session->id()
		]);
		
		if (!empty($row)) {
			Session::populate($session, $row);
			
			$userRow = $this->userDbTable->find("`id` = :id", [
				":id" => $session->userId()
			]);
			if ($userRow) {
				$user = User::populate(new User(), $userRow);
				$session->user($user);
			}
		}
		
		return $session;
	}
	
	public function save(Session $session)
	{
		$session->save();
		
		$this->sessionDbTable->insert($session->toBasicArray());
	}
}