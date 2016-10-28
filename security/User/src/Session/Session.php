<?php
namespace User\Session;

use Database\Model\AbstractResourceModel,
	User\User,
	User\GuestUser;

class Session extends AbstractResourceModel implements SessionInterface
{
	const RESOURCE_NAME = "user-session";
	
	/**
	 * @var string
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $name;
	
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
	 * @var int
	 */
	protected $lifetime;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 */
	public function __construct($name) 
	{
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getResourceName() { return self::RESOURCE_NAME; }
	
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
			$this->id = md5(uniqid("SESSION_ID"));
		}
		
		return $this->id;
	}
	
	/**
	 * Get or set the session's name
	 * 
	 * @param string $name
	 */
	public function name($name = null) 
	{
		return $this->getSet("name", $name);
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
	 * Get or set the lifetime of the session.  If the lifetime is
	 * anything greater than 0, a cookie will be created.
	 * 
	 * @param int $lifetime
	 * @return int
	 */
	public function lifetime($lifetime = null)
	{
		return $this->getSet("lifetime", $lifetime);
	}
}