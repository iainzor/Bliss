<?php
namespace User\Session;

use User\Db\UsersTable,
	User\Db\UserSessionsTable,
	User\Db\UserRolesTable,
	User\User,
	User\GuestUser,
	User\Hasher\HasherInterface;

class Manager
{
	/**
	 * @var HasherInterface
	 */
	private $hasher;
	
	/**
	 * Constructor
	 * 
	 * @param HasherInterface
	 */
	public function __construct(HasherInterface $hasher = null)
	{
		if ($hasher === null) {
			$hasher = User::passwordHasher();
		}
		
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
		$usersTable = new UsersTable();
		$query = $usersTable->select(["id", "email", "password"]);
		$query->where(["email" => ":email"]);
		$user = $query->fetchRow([
			":email" => $email
		]);
		
		if (!empty($user) && $this->hasher->matches($password, $user->password())) {
			$session->isValid(true);
			$session->userId($user->id());
			
			$this->save($session);
			$this->attachUser($session);
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
		$sessionsTable = new UserSessionsTable();
		$usersTable = new UsersTable();
		$rolesTable = new UserRolesTable();
		
		$query = $usersTable->select();
		$query->join($sessionsTable, "userId", "id")->where([
			"id" => $session->id()
		]);
		$query->hasOne("role", $rolesTable, "roleId", "id");
		
		$user = $query->fetchRow();
		
		if ($user) {
			$session->user($user);
			$session->isValid(true);
			$user->touch();
		} else {
			$session->isValid(false);
			$session->user(new GuestUser());
		}
		
		return $session;
	}
	
	public function save(Session $session)
	{
		$session->save();
		
		$sessionTable = new UserSessionsTable();
		$sessionTable->insert($session, ["updated"]);
	}
}