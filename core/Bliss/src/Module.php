<?php
namespace Bliss;

use Router\ProviderInterface as RouteProvider;

class Module extends Module\AbstractModule implements RouteProvider
{
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^app\.json$/i", [], [
			"module" => "bliss",
			"controller" => "app",
			"action" => "index",
			"format" => "json"
		]);
	}
}
