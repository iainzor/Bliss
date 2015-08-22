<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\User,
	User\Db\UsersTable,
	User\Form\UserForm;

class UserController extends AbstractController 
{
	/**
	 * @var \User\User
	 */
	private $user;
	
	public function init()
	{
		$usersTable = new UsersTable();
		$userId = $this->param("userId");
		$userData = $usersTable->find([
			"id" => $userId
		]);
		
		if (!$userData) {
			throw new \Exception("Could not find user by ID: #{$userId}", 404);
		}
		
		$this->user = User::factory($userData);
	}
	
	public function indexAction(\Request\Module $request)
	{
		if ($request->isPost()) {
			$usersTable = new UsersTable();
			$userForm = new UserForm($usersTable, $this->user);
			$userData = $request->param("user");
			
			if ($userForm->isValid($userData)) {
				$this->user = $userForm->save($userData);
			} else {
				return [
					"result" => "error",
					"message" => "Form could not be validated",
					"errors" => $userForm->errors()
				];
			}
		}
		
		return $this->user;
	}
	
}