<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Nav;

class AccountController extends AbstractController 
{
	public function indexAction(\User\Module $users, \Request\Module $request)
	{
		$user = $users->session()->user();
		$user->set("nav", new Nav($user, $request));
		
		return $user;
	}
}