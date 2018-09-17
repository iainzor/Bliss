<?php
namespace User\Controller;

use Bliss\Controller\AbstractController;

class AccountController extends AbstractController 
{
	public function indexAction(\User\Module $user)
	{
		return $user->session()->user();
	}
}