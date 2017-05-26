<?php
namespace Acl;

class Acl
{
	/**
	 * @var Role[]
	 */
	private $roles = [];
	
	/**
	 * Add multiple roles to the ACL
	 * 
	 * @param Role[] $roles
	 */
	public function addRoles(array $roles)
	{
		foreach ($roles as $role) {
			$this->addRole($role);
		}
	}
	
	/**
	 * Add a role to the ACL.  If the role ID already exists, it will be overwritten 
	 * 
	 * @param \Acl\Role $role
	 */
	public function addRole(Role $role)
	{
		$this->roles[$role->id] = $role;
	}
	
	/**
	 * Attempt to get a role by its ID
	 * 
	 * @param int $roleId
	 * @return \Acl\Role
	 * @throws \Exception
	 */
	public function getRole(int $roleId) : Role
	{
		if (!isset($this->roles[$roleId])) {
			throw new \Exception("Invalid role ID #{$roleId}");
		}
		
		return $this->roles[$roleId];
	}
}