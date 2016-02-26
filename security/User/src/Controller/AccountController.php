<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Form\ChangePasswordForm;

class AccountController extends AbstractController 
{
	public function indexAction(\User\Module $users, \Request\Module $request)
	{
		$user = $users->session()->user();
		
		return $user;
	}
	
	public function changePasswordAction(\Request\Module $request, \User\Module $um)
	{
		if (!$request->isPost()) {
			throw new \Exception("Only POST requests are allowed");
		}
		
		$user = $um->user();
		$form = new ChangePasswordForm($user);
		$response = [
			"result" => "success",
			"form" => $form
		];
		
		if ($form->isValid($request)) {
			$form->execute();
		} else {
			$response["result"] = "error";
		}
		
		return $response;
	}
}