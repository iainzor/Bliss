<?php
namespace Docs;

use Router\ProviderInterface as RouteProvider;

class Module extends \Bliss\Module\AbstractModule
implements RouteProvider
{
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^docs\/modules\/([a-z0-9-]+)\/?([a-z0-9-]+)?\.?([a-z0-9]+)?$/i", [
			1 => "moduleName",
			2 => "actionName",
			3 => "format"
		], [
			"module" => "docs",
			"controller" => "module",
			"action" => "render"
		]);
	}
}