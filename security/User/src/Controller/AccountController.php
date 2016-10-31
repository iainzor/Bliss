<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Form\ChangePasswordForm,
	User\User,
	User\Db\UsersTable;

class AccountController extends AbstractController 
{
	/**
	 * @var User
	 */
	private $user;
	
	public function init()
	{
		$this->user = $this->app->call($this, "load");
	}
	
	public function load(\User\Module $userModule)
	{
		return $userModule->user();
	}
	
	public function indexAction(\Request\Module $request)
	{
		if ($request->isPost()) {
			$allowed = ["displayName"];
			$values = [];
			foreach ($request->params() as $name => $value) {
				if (in_array($name, $allowed)) {
					$values[$name] = call_user_func([$this->user, $name], $value);
				}
			}
			$users = new UsersTable();
			$update = $users->update();
			$update->values($values);
			$update->where([
				"id" => $this->user->id()
			]);
			$update->execute();
		}
		return $this->user;
	}
	
	public function settingsAction(\Request\Module $request)
	{
		$settings = $this->user->settings();
		
		if ($request->isPost()) {
			$newSettings = $request->param("settings");
			$settings->merge($newSettings);
			$settings->save();
		}
		
		return $settings;
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