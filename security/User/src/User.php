<?php
namespace User;

use Database\Model;

class User extends Model\AbstractResourceModel
{
	const RESOURCE_NAME = "user";
	
	/**
	 * @var string
	 */
	protected $email;
	
	/**
	 * @var string
	 */
	protected $username;
	
	/**
	 * @var string
	 */
	private $password;
	
	/**
	 * @var boolean 
	 */
	private $preservePassword = false;
	
	/**
	 * @var string
	 */
	protected $roleId = Role::ROLE_DEFAULT;
	
	/**
	 * @var string
	 */
	protected $displayName;
	
	/**
	 * @var boolean
	 */
	protected $isActive = true;
	
	/**
	 * @var Role 
	 */
	protected $role;
	
	/**
	 * @var \User\Hasher\HasherInterface
	 */
	private static $passwordHasher;
	
	/**
	 * @return string
	 */
	public function getResourceName() { return self::RESOURCE_NAME; }
	
	/**
	 * Get or set the user's email address
	 * 
	 * @param string $email
	 * @return string
	 */
	public function email($email = null)
	{
		if ($email !== null) {
			$this->email = $email;
		}
		return $this->email;
	}
	
	/**
	 * Get or set the user's unique username
	 * 
	 * @param string $username
	 * @return string
	 */
	public function username($username = null)
	{
		return $this->getSet("username", $username);
	}
	
	/**
	 * Get or set the user's hashed password
	 * 
	 * @param string $password
	 * @param boolean $hash Whether to hash the password before setting it
	 * @return string
	 */
	public function password($password = null, $hash = false)
	{
		if ($password !== null) {
			if ($hash === true) {
				$password = self::passwordHasher()->hash($password);
			}
			$this->password = $password;
		}
		return $this->password;
	}
	
	/**
	 * Get or set whether to preserve the password hash in 
	 * the exported array
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function preservePassword($flag = null)
	{
		if ($flag !== null) {
			$this->preservePassword = (boolean) $flag;
		}
		return $this->preservePassword;
	}
	
	/**
	 * Get or the set user's display name
	 * 
	 * @param string $displayName
	 * @return string
	 */
	public function displayName($displayName = null)
	{
		if ($displayName !== null) {
			$this->displayName = $displayName;
		}
		return $this->displayName;
	}
	
	/**
	 * Get or set whether the user is active 
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function isActive($flag = null)
	{
		if ($flag !== null) {
			$this->isActive = (boolean) $flag;
		}
		return $this->isActive;
	}
	
	/**
	 * Get or set the user's role Id
	 * 
	 * @param int $id
	 * @return int
	 */
	public function roleId($id = null)
	{
		return $this->getSet("roleId", $id, "intval");
	}
	
	/**
	 * Get or set the user's ACL Role.  If one has not been set, an empty ACL Role instance will be created
	 * 
	 * @param array|Role $role
	 * @return Role
	 */
	public function role($role = null)
	{
		if ($role !== null) {
			if (is_array($role)) {
				$role = Role::factory($role);
			}
			if (!($role instanceof Role)) {
				throw new \UnexpectedValueException("\$role must be a property array or and instance of \\User\\Role");
			}
			
			$this->role = $role;
		}
		if (!$this->role) {
			$this->role = Role::registry()->role(Role::ROLE_DEFAULT);
		}
		
		return $this->role;
	}
	
	/**
	 * Check if the user is allowed to perform an action on a resource
	 * 
	 * @param string $resourceName
	 * @param string $action
	 * @param array $params
	 * @return boolean
	 */
	public function isAllowed($resourceName, $action = null, array $params = [])
	{
		return $this->acl()->isAllowed($resourceName, $action, $params);
	}
	
	/**
	 * Set the user's last updated time
	 */
	public function touch()
	{
		if ($this->id()) {
			$usersTable = new Db\UsersTable();
			$query = $usersTable->update();
			$query->values([
				"updated" => time()
			]);
			$query->where(["id" => $this->id()]);
			$query->execute();
		}
	}
	
	/**
	 * Make alterations to the exported array
	 * 
	 * @return array
	 */
	public function toArray() 
	{
		$data = parent::toArray();
		
		if ($this->preservePassword) {
			$data["password"] = $this->password();
		}
		
		return $data;
	}
	
	/**
	 * Get or set the global password hasher for users
	 * 
	 * @param \User\Hasher\HasherInterface $hasher
	 * @return \User\Hasher\HasherInterface
	 */
	public static function passwordHasher(Hasher\HasherInterface $hasher = null)
	{
		if ($hasher !== null) {
			self::$passwordHasher = $hasher;
		}
		if (!isset(self::$passwordHasher)) {
			self::$passwordHasher = new Hasher\Blowfish();
		}
		
		return self::$passwordHasher;
	}
			
}