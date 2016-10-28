<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Db\UsersTable,
	User\Form\SignUpForm;

class AuthController extends AbstractController
{
	public function signInAction(\Request\Module $request, \User\Module $user)
	{
		if (!$request->isPost()) {
			throw new \Exception("Only POST requests are allowed");
		}
		
		$email = $this->param("email");
		$password = $this->param("password");
		$remember = (boolean) $this->param("remember", 0);
		$manager = $user->sessionManager();
		$session = $manager->createSession($email, $password);

		if (!$session->isValid()) {
			throw new \Exception("Invalid credentials provided", 401);
		}

		if ($remember) {
			$session->lifetime(2592000);
		}

		$manager->attachUser($session);
		$manager->saveSession($session);

		return $session;
	}
	
	public function signUpAction(\Database\Module $db, \Request\Module $request, \User\Module $userModule)
	{
		if (!$request->isPost()) {
			throw new \Exception("Only POST requests are allowed");
		}
		
		$form = new SignUpForm(
			new UsersTable()
		);
		$user = $form->create($request->params());

		if ($user === false) {
			return [
				"result" => "error",
				"errors" => $form->errors()
			];
		} else {
			$manager = $userModule->sessionManager();
			$session = $manager->createSession($user->email(), $user->password(), true);
			$manager->saveSession($session);

			return $user;
		}
	}
	
	public function signOutAction(\Request\Module $request, \User\Module $userModule) 
	{
		if ($request->isPost()) {
			$manager = $userModule->sessionManager();
			$session = $userModule->session();
			$manager->deleteSession($session);
			
			return $session->user();
		}
	}
}