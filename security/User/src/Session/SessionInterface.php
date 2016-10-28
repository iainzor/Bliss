<?php
namespace User\Session;

use User\User;

interface SessionInterface
{
	public function id($id = null);
	
	public function name($name = null);
	
	public function isValid($isValid = null);
	
	public function user(User $user = null);
	
	public function lifetime($lifetime = null);
}