<?php
namespace Angular;

use Bliss\Module\AbstractModule,
	Router\ProviderInterface as RouteProvider;

class Module extends AbstractModule implements RouteProvider
{
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^([a-z0-9-]+)\/(directives|views)\/(.*)\.([a-z]+)$/i", [
			1 => "moduleName",
			2 => "type",
			3 => "path",
			4 => "format"
		], [
			"module" => "angular",
			"controller" => "view-renderer",
			"action" => "render"
		]);
	}
}