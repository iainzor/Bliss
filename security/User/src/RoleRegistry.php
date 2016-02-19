<?php
namespace User;

class RoleRegistry
{
	/**
	 * @var Role[]
	 */
	private $roles = [];
	
	public function __construct()
	{
		$this->configure([
			Role::ROLE_GUEST => new GuestRole(),
			Role::ROLE_DEFAULT => new Role()
		]);
	}
	
	/**
	 * Get or set a role directly
	 * If the role does not exist, it will be created
	 * 
	 * @param string $roleId
	 * @param \User\Role $role
	 * @return Role
	 */
	public function role($roleId, Role $role = null)
	{
		if ($role !== null) {
			$this->roles[$roleId] = $role;
		}
		if (!isset($this->roles[$roleId])) {
			$this->roles[$roleId] = Role::factory([
				"id" => $roleId
			]);
		}
		return $this->roles[$roleId];
	}
	
	/**
	 * Configure multiple roles
	 * 
	 * @param array $roles
	 */
	public function configure(array $roles)
	{
		foreach ($roles as $id => $role) {
			$this->configureRole($role, $id);
		}
	}
	
	/**
	 * Configure a user role
	 * 
	 * @param array|Role $role
	 * @param string $roleId
	 * @return Role The resulting configured role
	 * @throws \Exception
	 * @throws \UnexpectedValueException
	 */
	public function configureRole($role, $roleId = null) 
	{
		if (is_array($role)) {
			$role = Role::factory($role);
		} else if (!($role instanceof Role)) {
			throw new \Exception("\$role must be a configuration array or an instance of \\User\\Role");
		}
		
		if ($roleId !== null) {
			$role->id($roleId);
		}
		
		if (!$role->id()) {
			//throw new \UnexpectedValueException("No role ID provided in configuration: ". json_encode($role->toArray()));
		}
		
		if (!isset($this->roles[$role->name()])) {
			$this->roles[$role->name()] = $role;
 		} else {
			$this->roles[$role->name()]->merge($role);
		}
		
		return $this->roles[$role->name()];
	}
}