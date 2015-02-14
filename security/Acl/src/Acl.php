<?php
namespace Acl;

class Acl extends Component implements AclInterface
{
	const CREATE = "create";
	const READ = "read";
	const UPDATE = "update";
	const DELETE = "delete";
	
	/**
	 * @var boolean
	 */
	protected $allowByDefault = false;
	
	/**
	 * @var \Acl\Permission\Permission[]
	 */
	protected $permissions = [];
	
	/**
	 * Get or set whether permissions are allowed by default for the ACL
	 * This will only affect permissions that are not explicitly set
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function allowByDefault($flag = null) 
	{
		if ($flag !== null) {
			$this->allowByDefault = (boolean) $flag;
		}
		return $this->allowByDefault;
	}
	
	/**
	 * Set a permission for a resource
	 * 
	 * The only required parameter is $resourceName
	 * If no $resourceId is provided, all resources will be affected
	 * If no $action is set, all actions for a resource will be affected
	 * 
	 * @param string $resourceName
	 * @param string $action
	 * @param array $params
	 * @param boolean $isAllowed
	 */
	public function set($resourceName, $action = null, array $params = [], $isAllowed = true)
	{
		$permission = Permission\Permission::factory([
			"resourceName" => $resourceName,
			"action" => $action,
			"params" => $params,
			"isAllowed" => (boolean) $isAllowed
		]);
		
		$this->permissions[$resourceName][] = $permission;
	}
	
	/**
	 * Allow a resource
	 * Short hand for set()
	 * 
	 * @param string $resourceName
	 * @param string $action
	 * @param array $params
	 */
	public function allow($resourceName, $action = null, array $params = [])
	{
		$this->set($resourceName, $action, $params, true);
	}
	
	/**
	 * Deny access to a resource
	 * 
	 * Short hand for set()
	 * 
	 * @param string $resourceName
	 * @param string $action
	 * @param array $params
	 */
	public function deny($resourceName, $action = null, array $params = [])
	{
		$this->set($resourceName, $action, $params, false);
	}
	
	/**
	 * Check if the ACL has access to a resource
	 * 
	 * @param string $resourceName
	 * @param string $action
	 * @param array $params
	 * @return boolean
	 */
	public function isAllowed($resourceName, $action = null, array $params = [])
	{
		if (is_array($action)) {
			$action = empty($action) ? null : array_shift($action);
		}
		
		$allowed = $this->allowByDefault;
		
		if (isset($this->permissions[$resourceName])) {
			$perms = array_filter($this->permissions[$resourceName], function(Permission\Permission $perm) use ($action) {
				return $perm->action() === $action || $perm->action() === null;
			});
			
			foreach ($perms as $perm) {
				if ($perm->matches($params)) {
					if ($perm->isAllowed()) {
						$allowed = true;
					} else if ($allowed && !$perm->isAllowed()) {
						$allowed = false;
					}
				}
			}
		}
		
		return $allowed;
	}
	
	/**
	 * Assert that a permission is allowed
	 * 
	 * @param string $resourceName
	 * @param string $action
	 * @param array $params
	 * @throws PermissionDeniedException
	 */
	public function assertIsAllowed($resourceName, $action = null, array $params = []) 
	{
		if (!$this->isAllowed($resourceName, $action, $params)) {
			if ($action !== null) {
				$message = "Permission for action '{$action}' denied for resource: {$resourceName}";
			} else {
				$message = "Permission denied for resource: {$resourceName}";
			}
			
			if (count($params)) {
				$message .= ", using params: ". json_encode($params);
			}
			
			throw new PermissionDeniedException($message);
		}
	}
	
	/**
	 * Get all permissions in the ACL
	 * 
	 * @return \Acl\Permission\Permission[]
	 */
	public function permissions()
	{
		$permissions = [];
		foreach ($this->permissions as $resourcePerms) {
			$permissions = array_merge($permissions, $resourcePerms);
		}
		return $permissions;
	}
	
	/**
	 * Add a permission to the ACL
	 * 
	 * @param \Acl\Permission\Permission $permission
	 */
	public function add(Permission\Permission $permission)
	{
		$i = $permission->resourceName();
		if (!isset($this->permissions[$i])) {
			$this->permissions[$i] = [];
		}
		
		$this->permissions[$i][] = $permission;
	}
	
	/**
	 * Merge another ACL with this one
	 * 
	 * @param \Acl\AclInterface $acl
	 */
	public function merge(AclInterface $acl)
	{
		foreach ($acl->permissions() as $permission) {
			$this->add($permission);
		}
	}
	
	/**
	 * Add additional properties to the exported array
	 * 
	 * @return array
	 */
	public function toArray() {
		$data = array_merge(parent::toArray(), [
			"permissions" => $this->_parse("permissions", $this->permissions())
		]);
		
		return $data;
	}
}