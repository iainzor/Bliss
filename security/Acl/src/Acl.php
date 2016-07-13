<?php
namespace Acl;

use Bliss\Component;

class Acl extends Component
{
	const CREATE = "create";
	const READ = "read";
	const UPDATE = "update";
	const DELETE = "delete";
	
	/**
	 * @var Permission\PermissionInterface[]
	 */
	protected $permissions = [];
	
	/**
	 * Get or set the ACL's permissions
	 * 
	 * @param Permission\PermissionInterface[] $permissions
	 * @return Permission\PermissionInterface[]
	 */
	public function permissions(array $permissions = null)
	{
		if ($permissions !== null) {
			$this->permissions = [];
			foreach ($permissions as $permission) {
				$this->addPermission($permission);
			}
		}
		return $this->permissions;
	}
	
	/**
	 * Add a permission to the ACL
	 * 
	 * @param \Acl\Permission\PermissionInterface $permission
	 */
	public function addPermission(Permission\PermissionInterface $permission)
	{
		$this->permissions[] = $permission;
	}
	
	/**
	 * Check if an action is allowed on a path
	 * 
	 * @param string $path
	 * @param string $action
	 * @return boolean
	 */
	public function isAllowed($path, $action)
	{
		$isAllowed = false;
		foreach ($this->permissions as $permission) {
			if ($permission->matches($path)) {
				$isAllowed = $permission->isAllowed($action);
			}
		}
		return $isAllowed;
	}
	
	/**
	 * Assert that $action is allowed on $path
	 * 
	 * @param string $path
	 * @param string $action
	 * @throws PermissionDeniedException
	 */
	public function assertIsAllowed($path, $action)
	{
		if (!$this->isAllowed($path, $action)) {
			throw new PermissionDeniedException("Action '{$action}' on '{$path}' is not allowed");
		}
	}
}