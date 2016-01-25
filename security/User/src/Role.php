<?php
namespace User;

class Role extends \Acl\Role\Role
{
	const ROLE_DEFAULT = "default";
	const ROLE_GUEST = "guest";
	const ROLE_ADMIN = "admin";
	
	/**
	 * @var RoleRegistry
	 */
	private static $registry;
	
	/**
	 * @var string
	 */
	protected $defaultPath;
	
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