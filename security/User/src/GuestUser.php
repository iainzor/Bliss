<?php
namespace User;

class GuestUser extends User
{
	protected $isActive = false;
	protected $roleId = Role::ROLE_GUEST;
	
	public function __construct() 
	{
		$this->role(
			Role::registry()->role(Role::ROLE_GUEST)
		);
	}
}