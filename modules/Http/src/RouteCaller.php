<?php
namespace Http;

use Core\ModuleDefinition,
	Core\ControllerDefinition,
	Core\ActionDefinition;

class RouteCaller
{
	/**
	 * @var Application
	 */
	private $app;
	
	/**
	 * @var Route
	 */
	private $route;
	
	/**
	 * Constructor
	 * 
	 * @param \Http\Application $app
	 * @param \Http\Route $route
	 */
	public function __construct(Application $app, Route $route)
	{
		$this->app = $app;
		$this->route = $route;
	}
	
	/**
	 * Get the definition of the module that will be executed
	 * 
	 * @return ModuleDefinition
	 */
	public function moduleDefinition() : ModuleDefinition
	{
		$moduleName = $this->route->module();
		
		return $this->app->moduleRegistry()->module($moduleName);
	}
	
	/**
	 * Get the definition of the controller that will be executed
	 * 
	 * @return ControllerDefinition
	 */
	public function controllerDefinition() : ControllerDefinition
	{
		$controllerName = $this->route->controller();
		
		return $this->moduleDefinition()->controller($controllerName);
	}
	
	/**
	 * Get the definition of the action that will be executed
	 * 
	 * @return ActionDefinition
	 */
	public function actionDefinition() : ActionDefinition
	{
		$actionName = $this->route->action();
		
		return $this->controllerDefinition()->action($actionName);
	}
	
	/**
	 * Get the instance of the module to be executed
	 * 
	 * @return mixed
	 */
	public function module()
	{
		$moduleDef = $this->moduleDefinition();
		$moduleInstance = $moduleDef->instance($this->app);
		
		return $moduleInstance;
	}
	
	/**
	 * Get the instance of the controller to be executed
	 * 
	 * @return mixed
	 */
	public function controller()
	{
		$controllerDef = $this->controllerDefinition();
		$controllerInstance = $controllerDef->instance($this->app);
		
		return $controllerInstance;
	}
	
	/**
	 * Execute the route and return the results
	 * 
	 * @return mixed
	 */
	public function execute()
	{
		$actionDef = $this->actionDefinition();
		return $actionDef->call($this->app);
	}
}
