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
	 * @var Config
	 */
	private $config;
	
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
		$this->config = new Config();
	}
	
	/**
	 * Get or set the session configuration
	 * 
	 * @param array $config
	 * @return Config
	 */
	public function config(array $config = null)
	{
		if ($config !== null) {
			foreach ($config as $name => $value) {
				call_user_func([$this->config, $name], $value);
			}
		}
		return $this->config;
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
	 * Attempt to load an existing session
	 * 
	 * @return \User\Session\Session
	 */
	public function loadSession()
	{
		$name = $this->config->name();
		$session = new Session($name);
		
		if (isset($_COOKIE[$name])) {
			$session->isValid(true);
			$session->id($_COOKIE[$name]);
		} else if (isset($_SESSION[$name])) {
			$session->isValid(true);
			$session->id($_SESSION[$name]);
		}
		
		return $session;
	}
	
	/**
	 * Create a new session using the credentials provided
	 * 
	 * @param string $email
	 * @param string $password
	 * @param boolean $isHashed Whether the value passed is already hashed
	 * @return \User\Session\Session
	 */
	public function createSession($email, $password, $isHashed = false)
	{
		$session = new Session($this->config->name());
		$usersTable = new UsersTable();
		$query = $usersTable->select(["id", "email", "password"]);
		$query->where(["email" => ":email"]);
		$user = $query->fetchRow([
			":email" => $email
		]);
		
		if (!empty($user)) {
			$valid = $isHashed === true
					? $password === $user->password()
					: $this->hasher->matches($password, $user->password());
			
			$session->isValid($valid);
			$session->userId($user->id());
			
			if ($valid === true) {
				$this->saveSession($session);
				$this->attachUser($session);
			}
		}
		
		return $session;
	}
	
	/**
	 * Save a session and update its database record
	 * 
	 * @param \User\Session\Session $session
	 */
	public function saveSession(Session $session)
	{
		if ($session->lifetime() > 0) {
			setcookie(
				$session->name(), 
				$session->id(), 
				time() + $session->lifetime(), 
				$this->config->path(), 
				$this->config->domain(),
				$this->config->secure(),
				$this->config->httpOnly()
			);
		} else {
			$_SESSION[$session->name()] = $session->id();
		}
		
		$sessionTable = new UserSessionsTable();
		$sessionTable->insert($session, ["updated"]);
	}
	
	
	/**
	 * Delete a session
	 */
	public function deleteSession(Session $session) 
	{
		unset($_SESSION[$session->name()]);
		setcookie(
			$session->name(),
			null, 
			time() - 1, 
			$this->config->path(), 
			$this->config->domain(),
			$this->config->secure(),
			$this->config->httpOnly()
		);
		$session->user(new GuestUser());
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
}