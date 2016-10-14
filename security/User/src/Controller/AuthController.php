<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Db\UsersTable,
	User\Form\SignUpForm;

class AuthController extends AbstractController
{
	public function signInAction(\Request\Module $request, \User\Module $user)
	{
		if ($request->isPost()) {
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
			$manager->save($session);
			
			return $session;
		}
	}
	
	public function signUpAction(\Database\Module $db, \Request\Module $request, \User\Module $userModule)
	{
		if ($request->isPost()) {
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
				$manager->save($session);
				
				return $user;
			}
		}
	}
	
	public function signOutAction(\Request\Module $request, \User\Module $userModule) 
	{
		if ($request->isPost()) {
			$session = $userModule->session();
			$session->delete();
			
			return $session->user();
		}
	}
}