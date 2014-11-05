<?php
namespace BlissApp;

use View\Decorator\ProviderInterface as DecoratorProvider,
	View\Decorator\PartialWrapper,
	Config\ProviderInterface as ConfigProvider,
	Router\ProviderInterface as RouteProvider;

class Module extends \Bliss\Module\AbstractModule 
implements DecoratorProvider, ConfigProvider, RouteProvider
{
	public function initViewDecorator(\View\Decorator\Registry $registry) 
	{
		$defaultFormat = $this->app->response()->defaultFormat();
		$path = $this->resolvePath("layouts/primary.phtml");
		
		$registry->add(new PartialWrapper($path), $defaultFormat);
	}
	
	public function initConfig(\Config\Config $rootConfig) 
	{
		$rootConfig->assets->compiler->enabled = true;
	}
	
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^([a-z0-9-]+)?\/?([a-z0-9-]+)?\/?([a-z0-9-]+)?$/i", [], [
			"module" => $this->name(),
			"controller" => "index",
			"action" => "index"
		], 100);
	}
}