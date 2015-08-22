<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Db\UsersTable,
	User\Form\UserForm;

class UsersController extends AbstractController
{
	public function allAction()
	{
		$usersTable = new UsersTable();
		$query = $usersTable->select();
		$query->orderBy("displayName");
		
		$results = $query->fetchAll();
		foreach ($results as $i => $user) {
			unset($user["password"]);
			
			$user["path"] = "users/". $user["id"];
			
			$results[$i] = $user;
		}
		
		return $results;
	}
	
	public function newAction(\Request\Module $request)
	{
		$usersTable = new UsersTable();
		$userForm = new UserForm($usersTable);
		$userData = $request->param("user");
		
		if ($request->isPost()) {
			if ($userForm->isValid($userData)) {
				return $userForm->save($userData);
			} else {
				return [
					"result" => "error",
					"message" => "Form could not be validated",
					"errors" => $userForm->errors()
				];
			}
		}
	}
}