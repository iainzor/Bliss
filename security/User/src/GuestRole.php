<?php
namespace User;

class GuestRole extends Role
{
	public function __construct() 
	{
		parent::__construct(self::ROLE_GUEST);
		
		$this->defaultPath("sign-in");
		$this->deny("*");
		$this->allow(Module::RESOURCE_NAME, "sign-in");
		$this->allow(Module::RESOURCE_NAME, "sign-up");
	}
}