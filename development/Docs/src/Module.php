<?php
namespace Docs;

use Router\ProviderInterface as RouteProvider,
	Pages\ProviderInterface as PageProvider;

class Module extends \Bliss\Module\AbstractModule
implements RouteProvider, PageProvider
{
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^docs\/modules\/([a-z0-9-]+)\/?([a-z0-9-]+)?\.([a-z0-9]+)$/i", [
			1 => "moduleName",
			2 => "actionName",
			3 => "format"
		], [
			"module" => "docs",
			"controller" => "module",
			"action" => "render"
		])->when("/^docs\/modules\/[a-z0-9-]+\/[a-z0-9-]+?$/i", [], [
			"module" => "docs",
			"controller" => "module",
			"action" => "index"
		])->when("/^docs\/api\/(.*)\.([a-z0-9]+)?$/", [
			1 => "path",
			2 => "format"
		], [
			"module" => "docs",
			"controller" => "api",
			"action" => "reflect"
		]);
	}
	
	public function initPages(\Pages\Container $root) 
	{
		$root->add([
			"title" => "Documentation",
			"path" => "docs",
			"visible" => false
		]);
	}
}