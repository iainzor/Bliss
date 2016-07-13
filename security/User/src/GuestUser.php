<?php
namespace User;

class GuestUser extends User
{
	protected $isActive = false;
	protected $roleId = Role::GUEST_ROLE;
}