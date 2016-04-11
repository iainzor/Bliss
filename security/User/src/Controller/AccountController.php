<?php
namespace User\Controller;

use Bliss\Controller\AbstractController,
	User\Form\ChangePasswordForm,
	User\User;

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
	
	public function indexAction()
	{
		return $this->user;
	}
	
	public function settingsAction(\Request\Module $request)
	{
		$moduleName = $request->param("moduleName", "user");
		$module = $this->app->module($moduleName);
		$settings = $this->user->settings()->module($module);
		
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