<?php
namespace User;

class RoleRegistry
{
	/**
	 * @var Role[]
	 */
	private $roles = [];
	
	/**
	 * Get or set a role directly
	 * If the role does not exist, it will be created
	 * 
	 * @param string $roleName
	 * @param \User\Role $role
	 * @return Role
	 */
	public function role($roleName, Role $role = null)
	{
		if ($role !== null) {
			$this->roles[$roleName] = $role;
			echo "Setting {$roleName} to ". get_class($role) ."\n";
		}
		if (!isset($this->roles[$roleName])) {
			$this->roles[$roleName] = new Role($roleName);
			echo "Created new role {$roleName}\n";
		}
		return $this->roles[$roleName];
	}
	
	/**
	 * Configure multiple roles
	 * 
	 * @param array $roles
	 */
	public function configure(array $roles)
	{
		foreach ($roles as $roleName => $role) {
			$this->configureRole($role, $roleName);
		}
	}
	
	/**
	 * Configure a user role
	 * 
	 * @param array|Role $role
	 * @param string $roleName
	 * @return Role The resulting configured role
	 * @throws \Exception
	 * @throws \UnexpectedValueException
	 */
	public function configureRole($role, $roleName = null) 
	{
		if (is_array($role)) {
			$role = Role::factory($role);
		} else if (!($role instanceof Role)) {
			throw new \Exception("\$role must be a configuration array or an instance of \\User\\Role");
		}
		
		if ($roleName !== null) {
			$role->name($roleName);
		}
		if (!$role->name()) {
			throw new \UnexpectedValueException("No role name provided in configuration: ". json_encode($role->toArray()));
		}
		
		if (!isset($this->roles[$role->name()])) {
			$this->roles[$role->name()] = $role;
 		} else {
			$this->roles[$role->name()]->merge($role);
		}
		
		return $this->roles[$role->name()];
	}
}