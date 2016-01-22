<?php
namespace User;

class Role extends \Acl\Role\Role
{
	const ROLE_DEFAULT = "default";
	const ROLE_GUEST = "guest";
	const ROLE_ADMIN = "admin";
	
	/**
	 * @var string
	 */
	protected $defaultPath;
	
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