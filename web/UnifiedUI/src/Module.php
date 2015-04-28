<?php
namespace UnifiedUI;

use Bliss\Module\AbstractModule,
	View\Partial\Partial,
	View\Decorator\PartialWrapper,
	View\Decorator\ProviderInterface as DecoratorProvider,
	View\Partial\InjectableTrait,
	View\Partial\InjectorInterface,
	View\Partial\InjectableInterface,
	Router\ProviderInterface as RouteProvider;

class Module extends AbstractModule implements DecoratorProvider, InjectorInterface, InjectableInterface, RouteProvider
{
	use InjectableTrait;
	
	const AREA_HEADER_WIDGETS = "area.header.widgets";
	const AREA_MENU = "area.menu";
	const AREA_FOOTER = "area.footer";
	const AREA_HEAD = "area.head";
	const AREA_CSS = "area.css";
	const AREA_JS = "area.js";
	
	public function initViewDecorator(\View\Decorator\Registry $registry) 
	{
		$partial = new Partial($this->resolvePath("layouts/main.phtml"), $this->app); 
		$registry->add(new PartialWrapper($partial));
	}
	
	public function compileInjectables() 
	{
		$this->app->log("Compiling injectables");

		foreach ($this->app->modules() as $module) {
			if ($module instanceof InjectorInterface) {
				$this->app->log("----Initializing injector for module '". $module->name() ."'");

				$module->initPartialInjector($this);
			}
		}
	}
	
	public function initPartialInjector(InjectableInterface $injectable) 
	{
		$injectable->inject(self::AREA_MENU, new Partial($this->resolvePath("layouts/partials/navigation.phtml"), $this->app));
		$injectable->inject(self::AREA_HEAD, new Partial($this->resolvePath("layouts/partials/css.phtml"), $this->app));
	}
	
	public function initRouter(\Router\Module $router) 
	{
		$router->when("/^((?:[a-z0-9-]\/?)+)?$/i", [], [
			"module" => $this->name(),
			"controller" => "view",
			"action" => "render"
		]);
	}
}