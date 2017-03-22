<?php
namespace MockModule;

use Core\BootableModuleInterface;

class Module implements BootableModuleInterface
{
	public function bootstrap(\Core\AbstractApplication $app) 
	{
		$app->router()->when("/^users\/([0-9]+)\/?([a-z0-9-]+)?$/i")
			->module("mockmodule")
			->controller("user")
			->action(function(array $matches) {
				return isset($matches[2]) ? $matches[2] : "index";
			})
			->params(function(array $matches) {
				return [
					"userId" => $matches[1]
				];
			});
	}
}
