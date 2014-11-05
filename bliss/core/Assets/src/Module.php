<?php
namespace Assets;

use Router\ProviderInterface as RouteProvider;

class Module extends \Bliss\Module\AbstractModule
implements RouteProvider
{
	/**
	 * @var \Assets\Compiler\Container
	 */
	private $compiler;
	
	/**
	 * Add routes used to render assets
	 * 
	 * @param \Router\Module $router
	 */
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^([a-z0-9-]+)\/((css|js|img|elements)\/.*\.([a-z]+))$/i", [
			1 => "moduleName",
			2 => "path",
			4 => "format"
		], [
			"module" => "assets",
			"controller" => "asset",
			"action" => "render"
		], 100)->when("/^assets\/all\.([a-z0-9-]+)$/i", [
			1 => "format"
		], [
			"module" => "assets",
			"controller" => "asset",
			"action" => "render-all"
		], 101);
	}
	
	/**
	 * Get the asset compiler instance
	 * 
	 * @return \Assets\Compiler\Container
	 */
	public function compiler()
	{
		if (!isset($this->compiler)) {
			$this->compiler = new Compiler\Container(
				$this->config("compiler")
			);
		}
		
		return $this->compiler;
	}
}