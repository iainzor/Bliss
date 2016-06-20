<?php
namespace User\Session;

use Database\Model\AbstractResourceModel,
	User\User,
	User\GuestUser;

class Session extends AbstractResourceModel implements SessionInterface
{
	const KEY = "USER_SESSION";
	const RESOURCE_NAME = "user-session";
	
	private $key = self::KEY;
	
	/**
	 * @var string
	 */
	protected $id;
	
	/**
	 * @var int
	 */
	protected $userId;
	
	/**
	 * @var boolean
	 */
	protected $isValid = false;
	
	/**
	 * @var \User\User
	 */
	protected $user;
	
	/**
	 * @return string
	 */
	public function getResourceName() { return self::RESOURCE_NAME; }
	
	/**
	 * Set the key used when saving the session
	 * 
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}
	
	/**
	 * Get or set the session's ID
	 * 
	 * @param string $id
	 * @return string
	 */
	public function id($id = null) 
	{
		if ($id !== null) {
			$this->id = $id;
		}
		
		if (!isset($this->id)) {
			$this->id = md5(uniqid($this->key));
		}
		
		return $this->id;
	}
	
	/**
	 * Get or set the ID of the user the session belongs to
	 * 
	 * @param int $userId
	 * @return int
	 */
	public function userId($userId = null)
	{
		if ($userId !== null) {
			$this->userId = (int) $userId;
		}
		return $this->userId;
	}
	
	/**
	 * Get or set the user instance 
	 * 
	 * @param \User\User $user
	 * @return \User\User
	 */
	public function user(User $user = null)
	{
		if ($user !== null) {
			$this->user = $user;
			$this->userId($user->id());
		}
		if (!$this->user) {
			$this->user = new GuestUser();
		}
		return $this->user;
	}
	
	/**
	 * Get or set whether the session is valid
	 * 
	 * @param boolean $isValid
	 * @return boolean
	 */
	public function isValid($isValid = null) 
	{
		if ($isValid !== null) {
			$this->isValid = (boolean) $isValid;
		}
		return $this->isValid;
	}
	
	/**
	 * Attempt to load the session
	 */
	public function load()
	{
		if (isset($_SESSION[$this->key])) {
			$this->isValid(true);
			$this->id($_SESSION[$this->key]);
		}
	}
	
	/**
	 * Save the session data
	 */
	public function save() 
	{
		$_SESSION[$this->key] = $this->id();
	}
	
	/**
	 * Delete the session data
	 */
	public function delete() 
	{
		unset($_SESSION[$this->key]);
		$this->user = new GuestUser();
	}
}