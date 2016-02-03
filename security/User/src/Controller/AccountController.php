<?php
namespace User\Controller;

use Bliss\Controller\AbstractController;

class AccountController extends AbstractController 
{
	public function indexAction(\User\Module $users, \Request\Module $request)
	{
		$user = $users->session()->user();
		
		return $user;
	}
}