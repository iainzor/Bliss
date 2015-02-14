<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\User,
	User\DbTable as UserDbTable,
	User\Form\SignUpForm;

class AuthController extends AbstractController
{
	public function signInAction()
	{
		if ($this->request()->isPost()) {
			$email = $this->param("email");
			$password = $this->param("password");
			$remember = (boolean) $this->param("remember", 0);
			$manager = $this->module->sessionManager();
			$session = $manager->createSession($email, $password);
			
			if (!$session->isValid()) {
				throw new \Exception("Invalid credentials provided", 401);
			}
			
			$manager->attachUser($session);
			$manager->save($session);
			
			return $session->toArray();
		}
	}
	
	public function signUpAction()
	{
		if ($this->request()->isPost()) {
			$form = new SignUpForm(
				new UserDbTable($this->database())
			);
			$user = $form->create($this->param("user", []));
			
			if ($user === false) {
				return [
					"result" => "error",
					"errors" => $form->errors()
				];
			} else {
				return $user->toArray();
			}
		}
	}
	
	public function signOutAction() 
	{
		if ($this->request()->isPost()) {
			$session = $this->module->session();
			$session->delete();
			
			return [
				"result" => "success"
			];
		}
	}
}