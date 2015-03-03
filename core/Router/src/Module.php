<?php
namespace Router;

class Module extends \Bliss\Module\AbstractModule
implements ProviderInterface
{
	/**
	 * @var \Router\Route[]
	 */
	private $routes = [];
	
	public function initRouter(Module $router) 
	{
		$router->when("/^([a-z0-9-]+)\/?([a-z0-9-]+)?\/?([a-z0-9-]+)?\.?([a-z0-9]+)?$/i", [
			1 => "module",
			2 => "controller",
			3 => "action",
			4 => "format"
		], [], -1);
	}
	
	/**
	 * Attempt to find a route matching the $test string
	 * 
	 * @param string $test
	 * @return \Router\Route
	 * @throws \Exception
	 */
	public function find($test)
	{
		$this->app->log("Looking for route matching '{$test}'");
		
		if (empty($this->routes)) {
			$this->_compileRoutes();
		}
		
		$result = null;
		
		foreach ($this->routes as $route) {
			if ($route->isActive() && $route->matches($test)) {
				if (!isset($result)) {
					$result = $route;
				} else if ($route->priority() >= $result->priority()) {
					$result = $route;
				}
			}
		}
		
		if ($result === null) {
			throw new \Exception("Could not find a matching route for '{$test}'", 404);
		} else {
			$this->app->log("Route found using: ". $result->route());
		}
		
		return $result;
	}
	
	/**
	 * Add a route to the router
	 * 
	 * @param string $regexRoute A RegEx string used to match the URI
	 * @param array $matchValues Pairs of values to use for each match found in the route
	 * @param array $defaultValues Default values of the route
	 * @param int $priority The order in which the route should be prioritized
	 * @return \Assets\Module
	 */
	public function when($regexRoute, array $matchValues, array $defaultValues = [], $priority = 1)
	{
		$this->routes[] = new Route($regexRoute, $matchValues, $defaultValues, $priority);
		
		return $this;
	}
	
	/**
	 * Compile routes from all available modules
	 */
	private function _compileRoutes()
	{
		foreach ($this->app->modules() as $module) {
			if ($module instanceof ProviderInterface) {
				$module->initRouter($this);
			}
		}
	}
}