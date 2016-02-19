<?php
namespace User;

use Database\Model\ModelInterface,
	Acl\Role\Role as BaseRole;

class Role extends BaseRole implements ModelInterface
{
	const ROLE_DEFAULT = 1;
	const ROLE_GUEST = 0;
	
	/**
	 * @var RoleRegistry
	 */
	private static $registry;
	
	/**
	 * @var int
	 */
	protected $id = self::ROLE_DEFAULT;
	
	/**
	 * @var string
	 */
	protected $name = "Standard User";
	
	/**
	 * @var string
	 */
	protected $defaultPath = "/";
	
	/**
	 * Get or set the global role registry
	 * 
	 * @param \User\RoleRegistry $registry
	 */
	public static function registry(RoleRegistry $registry = null)
	{
		if ($registry !== null) {
			self::$registry = $registry;
		}
		if (!self::$registry) {
			self::$registry = new RoleRegistry();
		}
		return self::$registry;
	}
	
	/**
	 * Get or set the ID of the role
	 * 
	 * @param int $id
	 * @return int
	 */
	public function id($id = null)
	{
		return $this->getSet("id", $id, "intval");
	}
	
	/**
	 * Get or set the role's default path
	 * When this role first accesses the application, they will be directed here first
	 * 
	 * @param string $defaultPath
	 * @return string
	 */
	public function defaultPath($defaultPath = null)
	{
		return $this->getSet("defaultPath", $defaultPath);
	}
}