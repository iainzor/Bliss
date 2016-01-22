<?php
namespace User;

class GuestUser extends User
{
	protected $isActive = false;
	
	public function __construct() 
	{
		$this->role(new GuestRole());
	}
}