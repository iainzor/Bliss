<?php
namespace Angular;

use Bliss\Module\AbstractModule,
	Router\ProviderInterface as RouteProvider;

class Module extends AbstractModule implements RouteProvider
{
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^([a-z0-9-]+)\/directives\/(.*)\.([a-z]+)$/i", [
			1 => "moduleName",
			2 => "path",
			3 => "format"
		], [
			"module" => "angular",
			"controller" => "directive",
			"action" => "render"
		]);
	}
}